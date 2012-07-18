<?php
/**
* Author: Dioscouri Design - www.dioscouri.com
* @package Phplist
* @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

// Import library dependencies
jimport('joomla.plugin.plugin');
jimport( 'joomla.filesystem.file' );

//get PHPList language file for confirmation email text
$element = 'com_phplist';
$lang =& JFactory::getLanguage();
$lang->load( $element, JPATH_BASE );

/**
 * Phplist User Plugin
 *
 * @package		Joomla
 * @subpackage	JFramework
 * @since 		1.5
 */
class plgContentPhplist extends JPlugin 
{
	/**
	 * Constructor 
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param object $subject The object to observe
	 * @param 	array  $config  An array that holds the plugin configuration
	 * @since 1.5
	 */
	function plgContentPhplist(& $subject, $config)
	{
		parent::__construct($subject, $config);		
	}

	/**
	 * Confirms the extension is installed completely and adds helper files
	 * @return unknown_type
	 */
	function _isInstalled()
	{
		$success = false;
		
		jimport('joomla.filesystem.file');
		if (JFile::exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_phplist'.DS.'helpers'.DS.'_base.php')) 
		{
			require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_phplist'.DS.'defines.php' );
			JLoader::import( 'com_phplist.helpers.newsletter', JPATH_ADMINISTRATOR.DS.'components' );
			JLoader::import( 'com_phplist.helpers.message', JPATH_ADMINISTRATOR.DS.'components' );
			JLoader::import( 'com_phplist.helpers.phplist', JPATH_ADMINISTRATOR.DS.'components' );
			JLoader::import( 'com_phplist.helpers.email', JPATH_ADMINISTRATOR.DS.'components' );
			JLoader::import( 'com_phplist.controller', JPATH_ADMINISTRATOR.DS.'components' );
			JLoader::import( 'com_phplist.models.messages', JPATH_ADMINISTRATOR.DS.'components' );
			JLoader::import( 'com_phplist.tables.messages', JPATH_ADMINISTRATOR.DS.'components' );
			JLoader::import( 'com_phplist.controllers.messages', JPATH_ADMINISTRATOR.DS.'components' );			
			$success = true;

			// Also check that DB is setup
			$database = PhplistHelperPhplist::getDBO();
			if (!isset($database->error)) 
			{
				$success = true;
			}
		}
		return $success;
	}
	
	/**
	 * Example after save content method
	 * Article is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the content is saved
	 *
	 *
	 * @param 	object		A JTableContent object
	 * @param 	bool		If the content is just about to be created
	 * @return	void
	 */
	
	function onAfterContentSave( &$article, $isNew )
	{
		global $mainframe;

		$success = false;

		if ( !$this->_isInstalled() ) {
			return $success;
		}

		//load controllers
		$basecontroller = new PhplistController();
		$controller = new PhplistControllerMessages();
		
		// get cat params array or convert to array if single values.
		$categories = $this->params->get( 'contentcategory', '1' );
		if (!is_array($categories)) {
			$categories = array($categories);
		}
		
		//if article isn't in the cat array, end.
		if (!in_array($article->catid, $categories)) {
			return $success;
		}
				
		//convert Article to Message
		$message = '';
		$message->subject = '';
		$customSubject = $this->params->get( 'messagesubject', '1' );
		$appendSubject = $this->params->get( 'appendsubject', '1' );
		
		//make Message Subject
		if ($customSubject != '0') {
			if ($appendSubject == 'before') {
				$message->subject .= $article->title;
			}
			
			$message->subject .= $customSubject;
			
			if ($appendSubject == 'after') {
				$message->subject .= $article->title;
			}
		}
		else {
			$message->subject .= $article->title;
		}
		switch ($this->params->get( 'articlecontent', '1' )) {
			case 'intro':
				$message->message = $article->introtext;
			break;
			case 'main':
				$message->message = $article->fulltext;
			break;
			case 'both':
				$message->message = $article->introtext . $article->fulltext;
			break;
		}
		
		// convert relative links to absolute
		$message->message = PhplistHelperMessage::_relToAbs($message->message);
		
		//TODO remove html tags for text message
		$message->textmessage = $message->message;

			//save as draft or to queue
			if ($this->params->get( 'autoqueue', '1' ) == '1') {
				$message->status = 'submitted';
			}
			else {
				$message->status = 'draft';
			}

			// get defaults from phplist config
			$config = &PhplistConfig::getInstance();
			$message->fromfield = $config->get( 'default_fromemail', '1' );
			$message->template = $config->get( 'default_template', '1' );
			$message->footer = PhplistHelperMessage::getDefaultFooter()->value;
			$message->htmlformatted = $config->get('default_html', '1');
			if ($config->get('default_html', '') == '1') $message->sendformat = 'HTML';
			else $message->sendformat = 'text';

			//set message created date same as article created date
			$message->entered = $article->created;
				
			$message->repeatuntil = $article->created;
			
			//check if re-saved content should be 
			
			if ($isNew) {
				$message->id = '';
				$message->modified = $article->created;
			} elseif ($this->params->get( 'autoupdate', '1' ) == '1') {
				//check for existing phplist message to match article (!!based on created datetime!!)
				$tablename = PhplistHelperMessage::getTableName();
				$query = "SELECT * FROM {$tablename} WHERE {$tablename}.entered = '{$article->created}'";
				$database = PhplistHelperPhplist::getDBO();
				$data = $database->setQuery( $query );
				if ($database->loadObject()) {
					$message->id = $database->loadObject()->id;
				} else {
					// if onlynewmessages param set to 'no', create new phplist message
					if ($this->params->get( 'onlynewmessages', '1' ) == '0') {
						$message->id = '';
					}
					else {
						return $success;
					}
				}
				$message->modified = $article->modified;
			}
				
			//set embargo time (add hours)
			if ($this->params->get( 'emargotime', '1' ) != '0')
			{
				$message->embargo = strtotime( $message->modified .' + '.$this->params->get( 'embargotime', '1' ).' hours');
				$message->embargo = date( 'Y-m-d H:i:s', $message->embargo );
			}
			else $message->embargo = $article->created;

			//get model
			$model 	= $controller->getModel( $controller->get('suffix') );
			$row = $model->getTable();
			$row->load( $model->getId() );
			$row->bind( $message );

			// Stripslashes - for magic quotes on
			if (get_magic_quotes_gpc()) {
				$row->message = stripslashes($row->message);
				$row->subject = stripslashes($row->subject);
				$row->textmessage = stripslashes($row->textmessage);
				$row->footer = stripslashes($row->footer);
			}

			//save the Joomla! Article as a PHPList Message.
			if ( $row->save() )
			{
				$this->messagetype 	= 'message';
				$this->message  	= JText::_( 'MESSAGE SAVED' );

				// Get the array of newsletters
				$addtonewsletter = $this->params->get( 'newsletters', '1' );
				if (!is_array($addtonewsletter)) {
					$addtonewsletter = array($addtonewsletter);
				}
				
				$messageid = $row->id;
				$newsletters = PhplistHelperNewsletter::getNewsletters();
				if ($newsletters)
				{
					foreach ($newsletters as $d)
					{
						if ($d->id > 0)
						{
							if (!in_array($d->id, $addtonewsletter))
							{
								$remove = PhplistHelperMessage::removeFromNewsletter( $messageid, $d->id );
							}
							elseif (in_array($d->id, $addtonewsletter))
							{
								$is = PhplistHelperMessage::isNewsletter( $messageid, $d->id );
								if ($is != 'true')
								{
									$add = PhplistHelperMessage::addToNewsletter( $messageid, $d->id );
								}
							}
						}
					}
				}

				$dispatcher = JDispatcher::getInstance();
				$dispatcher->trigger( 'onAfterSave'.$this->get('suffix'), array( $row ) );
			}
	}

}

?>