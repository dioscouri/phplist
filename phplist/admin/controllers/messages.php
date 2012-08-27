<?php
/**
 * @version	1.5
 * @package	Phplist
 * @author 	Dioscouri Design
 * @link 	http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

class PhplistControllerMessages extends PhplistController
{
	/**
	 * constructor
	 */
	function __construct()
	{
		parent::__construct();
		
		$this->set('suffix', 'messages');
		$this->registerTask( 'addtoqueue', 'changestatus' );
		$this->registerTask( 'suspend', 'changestatus' );
		$this->registerTask( 'insertArticle', 'insertArticle' );
		$this->registerTask( 'send_test', 'sendTest' );
		$this->registerTask( 'send_test_email', 'sendTestEmail' );
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
    function _setModelState()
    {
    	$state = parent::_setModelState();   	
		$app = JFactory::getApplication();
		$model = $this->getModel( $this->get('suffix') );
    	$ns = $this->getNamespace();

    	$state['order']     = $app->getUserStateFromRequest($ns.'.filter_order', 'filter_order', 'tbl.id', 'cmd');
        $state['direction'] = $app->getUserStateFromRequest($ns.'.filter_direction', 'filter_direction', 'DESC', 'word');
        $state['filter_id_from']    = $app->getUserStateFromRequest($ns.'id_from', 'filter_id_from', '', '');
        $state['filter_id_to']      = $app->getUserStateFromRequest($ns.'id_to', 'filter_id_to', '', '');
        $state['filter_subject']   = $app->getUserStateFromRequest($ns.'subject', 'filter_subject', '', '');
        $state['filter_date_from'] = $app->getUserStateFromRequest($ns.'date_from', 'filter_date_from', '', '');
        $state['filter_date_to'] = $app->getUserStateFromRequest($ns.'date_to', 'filter_date_to', '', '');
      	$state['filter_listid'] 	= $app->getUserStateFromRequest($ns.'listid', 'filter_listid', '', '');
      	$state['filter_messagestate'] 	= $app->getUserStateFromRequest($ns.'messagestate', 'filter_messagestate', '', '');

    	foreach (@$state as $key=>$value)
		{
			$model->setState( $key, $value );	
		}
  		return $state;
    }
	
	/**
	 * change status
	 * @return void
	 */
	function changestatus() 
	{
		$error = false;
		$this->messagetype	= '';
		$this->message 		= '';
		$redirect = 'index.php?option=com_phplist&view='.$this->get('suffix');
		$redirect = JRoute::_( $redirect, false );
				
		$model = $this->getModel($this->get('suffix'));
		$row = $model->getTable();
		
		$cids = JRequest::getVar('cid', array (0), 'request', 'array');
		
		$task = JRequest::getVar( 'task' );
		switch ($task) 
		{
			case "addtoqueue":
				$status = "submitted";
				break;
			case "suspend":
				$status = "suspended";
				break;
			default:
				$this->messagetype 	= 'notice';
				$this->message 		= JText::_( "INVALID TASK" );
				$this->setRedirect( $redirect, $this->message, $this->messagetype );
				return;
			  break;
		}
		
		foreach (@$cids as $cid)
		{
			$row->load($cid);
			$row->status = $status;
			
			if ( !$row->save() ) 
			{
				$this->message .= $row->getError();
				$this->messagetype = 'notice';
				$error = true;
			}
		}
		
		if ($error)
		{
			$this->message = JText::_('MESSAGE STATUS CHANGE FAILED') . " - " . $this->message;
		}
			else
		{
			$this->message = JText::_('MESSAGE STATUS CHANGED');
		}
		
		$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}
	
	/**
	 * save a record
	 * @return void
	 */
	function save() 
	{
		$model 	= $this->getModel( $this->get('suffix') );
			
	    $row = $model->getTable();
	    $row->load( $model->getId() );
		$row->bind( $_POST );
		$isNew = ($row->id < 1);
		
		$config =& JFactory::getConfig();
		$date = JFactory::getDate();
		$date->setOffset($config->getValue('config.offset'));
		$datetime = $date->toMySQL(true);
		
		// Stripslashes - for magic quotes on
   		if (get_magic_quotes_gpc()) {
       		$row->message = stripslashes($row->message);
       		$row->subject = stripslashes($row->subject);
       		$row->textmessage = stripslashes($row->textmessage);
       		$row->footer = stripslashes($row->footer);
   		}
		
   		//convert relative to absolute links
   		$row->message = PhplistHelperMessage::_relToAbs($row->message);
   		
		if ($isNew)
		{
			// draft if new
			$row->status = 'draft';
			// set entered date as now
			$row->entered = $datetime;
			if (JRequest::getVar('embargo') == '')
			{
				$row->embargo = $datetime;
			}
			$row->repeatuntil = $datetime;
		}
		else
		{
			$row->modified = $datetime;
		}
		
		if (JRequest::getVar('sendformat') == 'HTML')
		{
			$row->htmlformatted = '1';
		}
		else
		{
			$row->htmlformatted = '0';
		}
		
		if ( $row->save() ) 
		{
			$model->setId( $row->id );
			
			// Get the array of addtonewsletter[] from request
			$addtonewsletter = JRequest::getVar( 'addtonewsletter', '', 'request', 'array' );
			$messageid = $model->getId();
			$newsletters = PhplistHelperNewsletter::getNewsletters();
			if ($newsletters)
			{
				foreach ($newsletters as $d)
				{
					if ($d->id > 0)
					{
						if (!isset($addtonewsletter[$d->id]))
						{
							PhplistHelperMessage::removeFromNewsletter( $messageid, $d->id );
						}
						elseif (isset($addtonewsletter[$d->id]))
						{
							$is = PhplistHelperMessage::isNewsletter( $messageid, $d->id );
							if ($is != 'true')
							{
								PhplistHelperMessage::addToNewsletter( $messageid, $d->id );
							}
						}
					}
				}
			}
			
			
			$this->messagetype 	= 'message';
			$this->message  	= JText::_( 'MESSAGE SAVED'. $this->getError());
			
			$dispatcher = JDispatcher::getInstance();
			$dispatcher->trigger( 'onAfterSave'.$this->get('suffix'), array( $row ) );
		} 
		else
		{
			$this->messagetype 	= 'notice';			
			$this->message 		= JText::_( 'SAVE FAILED' )." - ".$row->getError();
		}
		
		$redirect = "index.php?option=com_phplist";
		$task = JRequest::getVar('task');
		switch ($task)
		{
			case "savenew":
				$redirect .= '&view='.$this->get('suffix').'&layout=form';
				break;
			case "apply":
				$redirect .= '&view='.$this->get('suffix').'&layout=form&id='.$model->getId();
				break;
			case "save":
			default:
				$redirect .= "&view=".$this->get('suffix');
				break;
		}
		
		$redirect = JRoute::_( $redirect, false );
		$this->setRedirect( $redirect, $this->message, $this->messagetype );
		}
		
		/**
		 * Articles element
		 */
		function elementArticle()
		{
			JModel::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models' );
			$model = JModel::getInstance( 'Element', 'ContentModel' );
			$this->addViewPath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'views' );
			$view	= &$this->getView( 'Element', '', 'ContentView' );
			include_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'helper.php' );
			// $view->addHelperPath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'helper' );
			$view->setModel( $model, true );
			$view->display();
		}
		
		/**
		 *
		 * @return
		 */
		function insertArticle()
		{
			$success = true;
			$response = array();
			$response['msg'] = "";
			$response['error'] = "";
			$msg = new stdClass();
			$msg->message = "";
			$msg->error = "";
		
			$dispatcher	   =& JDispatcher::getInstance();
			$articleId = JRequest::getVar( 'articleid');
		
			$article =& JTable::getInstance('content');
			$article->load( $articleId );
			$article->text = $article->introtext . chr(13).chr(13) . $article->fulltext;
		
			$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');
			$params		= &JComponentHelper::getParams('com_content');
			$aparams	=& $article->parameters;
			$params->merge($aparams);
		
			// Fire Content plugins on the article so they change their tags
			/*
				* Process the prepare content plugins
			*/
			JPluginHelper::importPlugin('content');
			$results = $dispatcher->trigger('onPrepareContent', array (& $article, & $params, $limitstart));
		
			/*
			 * Handle display events
			*/
			$article->event = new stdClass();
			$results = $dispatcher->trigger('onAfterDisplayTitle', array ($article, &$params, $limitstart));
			$article->event->afterDisplayTitle = trim(implode("\n", $results));
				
			$results = $dispatcher->trigger('onBeforeDisplayContent', array (& $article, & $params, $limitstart));
			$article->event->beforeDisplayContent = trim(implode("\n", $results));
				
			$results = $dispatcher->trigger('onAfterDisplayContent', array (& $article, & $params, $limitstart));
			$article->event->afterDisplayContent = trim(implode("\n", $results));
		
			$text = "";
			$text .= "<h3>{$article->title}</h3>";
			$text .= $article->event->afterDisplayTitle;
			$text .= $article->event->beforeDisplayContent;
			$text .= $article->text;
			$text .= $article->event->afterDisplayContent;
				
			// encode and echo (need to echo to send back to browser)
			echo ( json_encode( $text ) );
			return $success;
		
		}
		
		// toolbar button
		function sendTest()
		{
			$model 	= $this->getModel( $this->get('suffix') );
			$row = $model->getTable();
			$row->load( $model->getId() );
			$redirect = "index.php?option=com_phplist&view=messages&layout=testemail&id=".$row->id;
			$redirect = JRoute::_( $redirect, false );
			$this->setRedirect( $redirect );
		}
		
		// send out test email
		function sendTestEmail()
		{
			$model  = $this->getModel( 'messages' );
			$message = $model->getTable();
		
			$mid =  JRequest::getVar( 'mid' );
			$message->load( $mid , 'id' );
		
			$toemail = JRequest::getVar( 'email' );
			$fromemail = $message->fromemail;
		
			$testemail = PhplistHelperEmail::_sendMessage( $message, 'test', $toemail);
		
			if ($testemail)
			{
				$this->messagetype  = 'notice';
				$this->message      = JText::_( 'TEST MESSAGE SUCCESSFULLY SENT TO' ) .' '. $toemail;
			}
			else
			{
				$this->messagetype  = 'error';
				$this->message      = JText::_( 'SENDING TEST MESSAGE FAILED' );
			}
		
    	$redirect = "index.php?option=com_phplist&view=messages";
		$redirect = JRoute::_( $redirect, false );
		$this->setRedirect( $redirect, $this->message, $this->messagetype );  
	}
}
?>