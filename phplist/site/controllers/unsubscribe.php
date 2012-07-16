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

class PhplistControllerUnsubscribe extends PhplistController
{
	/**
	 * constructor
	 */
	function __construct() 
	{
		parent::__construct();
		$this->set('suffix', 'unsubscribe');
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
			//if uid invalid, redirect to newsletters page with message.
			if (!$phplistUser)
			{
			JError::raiseNotice( 'Invalid UID', JText::_( "INVALID UID ERROR UNSUBSCRIBE" ) );
			$app = JFactory::getApplication();
	    	$app->redirect( $link );
			}	
		}
		
		if (!JFactory::getUser() || !$uid)
		{
			JError::raiseNotice( 'Invalid UID', JText::_( "MISSING UID ERROR UNSUBSCRIBE" ) );
			$app = JFactory::getApplication();
	    	$app->redirect( $link );
		}
		
		parent::display();
	} 

	/**
	 * Subscribes a user to unsubscribe checked off
	 * @return void
	 */
	function unsubscribe() 
	{
		JLoader::import( 'com_phplist.helpers.subscription', JPATH_ADMINISTRATOR.DS.'components' );
		JLoader::import( 'com_phplist.helpers.user', JPATH_ADMINISTRATOR.DS.'components' );
		JLoader::import( 'com_phplist.helpers.newsletter', JPATH_ADMINISTRATOR.DS.'components' );
		JLoader::import( 'com_phplist.helpers.url', JPATH_ADMINISTRATOR.DS.'components' );
		
		$uid = JRequest::getVar( 'uid' );
        $phplistUser = PhplistHelperUser::getUser( $uid, '1', 'uid' );
		
		$redirect = "index.php?option=com_phplist&view=unsubscribe&uid={$uid}";
		if ($id = PhplistUrl::getItemid()) {
			$redirect .= "&Itemid={$id}";
		}
		$redirect = JRoute::_( $redirect, false );
		
		if (!$phplistUser) 
			{
				$this->message .= JText::_( $phplistUser->id ."UNSUBSCRIBED FAILED NO USER" );
				$this->messagetype	= 'notice';
				$this->setRedirect( $redirect, $this->message, $this->messagetype );
				return false;
			}
		$details->userid = $phplistUser->id;
		
		$cids = JRequest :: getVar('cid', array(0), 'request', 'array');
		
		foreach ($cids as $cid)
		{
			$details->listid = $cid;
			$isSubscribed = PhplistHelperSubscription::isUser( $details->userid, $details->listid );
			$newslettername = PhplistHelperNewsletter::getName ($details->listid)->name;
			$unsubscribe = PhplistHelperSubscription::removeUserFrom($details);
			$this->message .= JText::_( "YOU HAVE BEEN SUCESSFULLY UNSUBSCRIBED FROM" ). ": ". $newslettername ."<br/>";
		}
    
        $this->messagetype	= 'message';
        $this->setRedirect( $redirect, $this->message, $this->messagetype );
	}
	
	/**
	 * cancel redirects to main newsletters page
	 * @return void
	 */
	function cancel() 
	{
		$uid =  JRequest::getVar( 'uid' );
		$redirect = "index.php?option=com_phplist&view=newsletters&uid=".$uid ;
		$redirect = JRoute::_( $redirect, false );
        $this->setRedirect( $redirect, $this->message, $this->messagetype );
	}
}

?>