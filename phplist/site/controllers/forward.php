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

class PhplistControllerForward extends PhplistController 
{
	/**
	 * constructor
	 */
	function __construct() 
	{
		parent::__construct();
		$this->set('suffix', 'forward');
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
			JError::raiseNotice( 'Invalid UID', JText::_( "INVALID UID ERROR FORWARD" ) );
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
		JLoader::import( 'com_phplist.library.url', JPATH_ADMINISTRATOR.DS.'components' );
		JLoader::import( 'com_phplist.helpers.email', JPATH_ADMINISTRATOR.DS.'components' );
		JLoader::import( 'com_phplist.helpers.user', JPATH_ADMINISTRATOR.DS.'components' );
		JLoader::import( 'com_phplist.helpers.phplist', JPATH_ADMINISTRATOR.DS.'components' );
		
		$model  = $this->getModel( 'messages' );        
        $message = $model->getTable();
        
        $mid =  JRequest::getVar( 'mid' );
		$message->load( $mid , 'id' );

		$toemail = JRequest::getVar( 'email' );
		
		$userid = JRequest::getVar('userid');
		$useremail = PhplistHelperUser::getUser( $userid, '1', 'id' )->email;
		
		$forwardemail = PhplistHelperEmail::_sendMessage( $message, 'forward', $toemail, $useremail);
		
		$database = PhplistHelperPhplist::getDBO();
        $tablename = PhplistHelperEmail::getTableName_forward();
       
        $date = JFactory::getDate();
		$datetime = $date->toMySQL();
        
		/// TODO use getInstance...
        $insertQuery = "
			INSERT INTO
			{$tablename}
			VALUES
			('','{$userid}','{$mid}', '{$toemail}', 'sent', '{$datetime}')
			";
			
			$database->setQuery( $insertQuery );
			$success = $database->query();
		
		$this->messagetype  = 'notice';         
        $this->message      = JText::_( 'MESSAGE_SUCCESSFULLY_FORWARDED_TO' ) .' '. $toemail;
        
        $redirect = PhplistUrl::siteLink("index.php?option=com_phplist&view=newsletters") ;
        $redirect = JRoute::_( $redirect, false );
        $this->setRedirect( $redirect, $this->message, $this->messagetype );
	}
}

?>