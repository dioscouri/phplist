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

class PhplistControllerPreferences extends PhplistController 
{
	/**
	 * constructor
	 */
	function __construct() 
	{
		parent::__construct();
		$this->set('suffix', 'preferences');
	}
	
	function display()
	{
		JLoader::import( 'com_phplist.helpers.user', JPATH_ADMINISTRATOR.DS.'components' );
		JLoader::import( 'com_phplist.library.url', JPATH_ADMINISTRATOR.DS.'components' );
		
		$link = PhplistUrl::addItemid("index.php?option=com_phplist&view=newsletters");
		$this->messagetype  = 'notice';
		
		if ($uid =  JRequest::getVar( 'uid' ))
		{
			$phplistUser = PhplistHelperUser::getUser( $uid, '1', 'uid' );
		}
		else
		{
			$juserid = JFactory::getUser()->id;
			$phplistUser = PhplistHelperUser::getUser( $juserid, '1', 'foreignkey' );
		}
		if (!$phplistUser)
		{
			JError::raiseNotice( 'Invalid UID', JText::_( "INVALID UID ERROR PREFS" ) );
			$app = JFactory::getApplication();
	    	$app->redirect( $link );
		}
		
		parent::display();
	} 
	/**
	 * save a record
	 * @return void
	 */
	function save() 
	{
		JLoader::import( 'com_phplist.helpers.attribute', JPATH_ADMINISTRATOR.DS.'components' );
		JLoader::import( 'com_phplist.library.url', JPATH_ADMINISTRATOR.DS.'components' );
        $model  = $this->getModel( $this->get('suffix') );        
        $row = $model->getTable();
        if (JFactory::getUser()->id) {
        	//load logged-in joomla user
        	$row->load( JFactory::getUser()->id , 'foreignkey' );
        	$redirect = "index.php?option=com_phplist&view=newsletters" ;
        }
        else {
        	//load phplist user from uniqueid
        	$uid =  JRequest::getVar( 'uid' );
        	$row->load( $uid , 'uniqid' );
        	$redirect = PhplistUrl::appendURL("index.php?option=com_phplist&view=newsletters") ;
        }
        
        // Potentially could cause problems with hidden input of id, etc
        // $row->bind( $_POST );
        // manually setting values from form
        $row->htmlemail = JRequest::getVar( 'htmlemail' );
        $row->email = JRequest::getVar( 'email' );
        
        if ( $row->save() ) 
        {
            $model->setId( $row->id );
            $this->messagetype  = 'message';
            $this->message      = JText::_( 'PREFERENCES SAVED' );
            
            $dispatcher = JDispatcher::getInstance();
            $dispatcher->trigger( 'onAfterSave'.$this->get('suffix'), array( $row ) );
        } 
            else 
        {
            $this->messagetype  = 'notice';         
            $this->message      = JText::_( 'SAVE FAILED' )." - ".$row->getError();
        }
		
        // update attributes
	    $saveattributes = PhplistHelperAttribute::saveAttributes($row->id);
        
        $redirect = JRoute::_( $redirect, false );
        $this->setRedirect( $redirect, $this->message, $this->messagetype );

	}
	
	/**
	 * cancel redirects to main newsletters page
	 * @return void
	 */
	function cancel() 
	{
		JLoader::import( 'com_phplist.library.url', JPATH_ADMINISTRATOR.DS.'components' );
		
		$this->messagetype = 'message';
		$this->message = JText::_( 'CANCEL PREFS MESSAGE' );
		$redirect = PhplistUrl::appendURL("index.php?option=com_phplist&view=newsletters") ;
		$redirect = JRoute::_( $redirect, false );
        $this->setRedirect( $redirect, $this->message, $this->messagetype );
	}
}

?>