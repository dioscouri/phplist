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

class PhplistControllerNewsletters extends PhplistController
{
	/**
	 * constructor
	 */
	function __construct() 
	{
		parent::__construct();
		$this->set('suffix', 'newsletters');
		
		$this->registerTask( 'list', 'display' );
		$this->registerTask( 'subscribe_selected', 'subscribe' );
		$this->registerTask( 'unsubscribe_selected', 'subscribe' );
		$this->registerTask( 'switch_subscription', 'subscribe' );
		$this->registerTask( 'subscribe_new', 'subscribe_new' );
		$this->registerTask( 'confirm', 'confirm_user' );
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
    	
    	$listid = JRequest::getVar( 'id' );
    	$config = Phplist::getInstance();
		$order_dir = $config->get('display_newsletter_order_dir', '1');
		$order = $config->get('display_newsletter_order', '1');
		
    	$state['order']     = $app->getUserStateFromRequest($ns.'.filter_order', 'filter_order', $order, 'cmd');
    	
    	$state['direction'] = $app->getUserStateFromRequest($ns.'.filter_direction', 'filter_direction', $order_dir, 'word');
			
      	$state['filter_active'] 	= $app->getUserStateFromRequest($ns.'active', 'filter_active', '1', '');

    	foreach (@$state as $key=>$value)
		{
			$model->setState( $key, $value );	
		}
  		return $state;
    }
	
	/**
	 * Subscribes user to newsletters checked off (logged in or using Uniqueid)
	 * @return void
	 */
	function subscribe() 
	{
	   	JLoader::import( 'com_phplist.helpers.user', JPATH_ADMINISTRATOR.DS.'components' );
		JLoader::import( 'com_phplist.helpers.subscription', JPATH_ADMINISTRATOR.DS.'components' );
		JLoader::import( 'com_phplist.library.url', JPATH_ADMINISTRATOR.DS.'components' );
		
		$uid = JRequest::getVar( 'uid', '0', 'request');
	   	$redirect = PhplistUrl::appendUrl('index.php?option=com_phplist&controller=newsletters&task=list');
	   	$redirect = JRoute::_( $redirect, false );
	   	
	   	// Validation is by JS validate() function in root site controller. Duplicated here as a backup.
		//check a newsletter is selected in list
		$cids = JRequest :: getVar('cid', array(0), 'request', 'array');
		if (intval($cids['0']) == '0')
		{
			$this->message = JText::_( "PLEASE SELECT A NEWSLETTER" );
			$this->messagetype	= 'notice';
			$this->setRedirect( $redirect, $this->message, $this->messagetype );
			return false;
		}
		
		if ($uid)
		{
			// get phplist user from unique id
			$phplistUser = PhplistHelperUser::getUser( $uid, '1', 'uid' );
		}
		else
		{
			// get phplist user from joomla user
			$user = JFactory::getUser();
			$phplistUser = PhplistHelperUser::getUser( $user->id, '1', 'foreignkey' );
		}
		
		// get phplist user details
		$details = new JObject();
		$details->userid = $phplistUser->id;
		
		$switch = PhplistHelperSubscription::switchSubscriptions($details);
		
		$this->setRedirect( $redirect, $this->message, $this->messagetype );		
	}
	
	/**
	 * Subscribes a user to newsletters checked off (NOT logged in or using Uniqueid)
	 * @return void
	 */
	function subscribe_new()
	{
		JLoader::import( 'com_phplist.helpers.user', JPATH_ADMINISTRATOR.DS.'components' );
		JLoader::import( 'com_phplist.helpers.subscription', JPATH_ADMINISTRATOR.DS.'components' );
		JLoader::import( 'com_phplist.helpers.attribute', JPATH_ADMINISTRATOR.DS.'components' );
		JLoader::import( 'com_phplist.helpers.newsletter', JPATH_ADMINISTRATOR.DS.'components' );
		JLoader::import( 'com_phplist.helpers.email', JPATH_ADMINISTRATOR.DS.'components' );
		JLoader::import( 'com_phplist.library.url', JPATH_ADMINISTRATOR.DS.'components' );
		
		$redirect = PhplistUrl::appendUrl('index.php?option=com_phplist&view=newsletters');
		$redirect = JRoute::_( $redirect, false );
		
		// Validation is by JS validate() function in root site controller. Duplicated here as a backup.
		//check a newsletter is selected in list
		$cids = JRequest :: getVar('cid', array(0), 'request', 'array');
		if (intval($cids['0']) == '0')
		{
			$this->message = JText::_( "PLEASE SELECT A NEWSLETTER" );
			$this->messagetype	= 'notice';
			$this->setRedirect( $redirect, $this->message, $this->messagetype );
			return false;
		}

		$subscriber2add = JRequest::getVar( 'subscriber2add' );
		
		jimport('joomla.mail.helper');
		if (!$isEmailAddress = JMailHelper::isEmailAddress( $subscriber2add ))
		{
			$this->message .= JText::_( "PLEASE ENTER A VALID EMAIL ADDRESS" );
			$this->messagetype	= 'notice';
			$this->setRedirect( $redirect, $this->message, $this->messagetype );
			return false;
		}
		
		// If joomla user account exists for email address, ask them to login
		if ($emailExists = PhplistHelperUser::emailExists( $subscriber2add, '1' ))
		{
			$this->message .= JText::_( "EMAIL IS JUSER" );
			$this->setRedirect( $redirect, $this->message, $this->messagetype );
			return false;
		}
		else
		{
			
			if ($user = PhplistHelperUser::getUser( $subscriber2add, '1', 'email' ))
			{
				$this->message .= JText::_( "EMAIL IS PHPLISTUSER" );
				$this->setRedirect( $redirect, $this->message, $this->messagetype );
				return false;
			} 
			else
			{
				// create the user
				$details = new JObject();
				$details->id = '';
				$details->email = $subscriber2add;
				$details->htmlemail = JRequest::getVar( 'htmlemail' );
				
				if (!$phplistUser = PhplistHelperUser::create( $details, '', 'true' ))
				{
					$this->message = JText::_( "COULD NOT CREATE NEW PHPLIST USER" );
					$this->messagetype	= 'notice';
					$this->setRedirect( $redirect, $this->message, $this->messagetype );
					return false;
				}

				$details->userid = $phplistUser->id;
				$details->uid = $phplistUser->uniqid;
				
				// send confirmation email to New Users
				$config = &Phplist::getInstance();
				$activation_email = $config->get( 'activation_email', '1' );
				if ($activation_email == '1')
				{
					$send = PhplistHelperEmail::_sendConfirmationEmail($details, 'cids');
				}
			}
		}
		
		$switch = PhplistHelperSubscription::switchSubscriptions($details);
		$saveattributes = PhplistHelperAttribute::saveAttributes($details->userid);
		
		$redirect = PhplistUrl::appendURL('index.php?option=com_phplist&view=newsletters');
		//add uid to redirect so new users can see their subscriptions and edit prefs.
		$redirect .= '&uid=' .$details->uid;
		$redirect = JRoute::_( $redirect, false );
		$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}
	
/**
	 * Confirms a user when they have clicked link in activation email
	 * @return void
	 */
	function confirm_user() 
	{
	   	JLoader::import( 'com_phplist.helpers.user', JPATH_ADMINISTRATOR.DS.'components' );
		JLoader::import( 'com_phplist.library.url', JPATH_ADMINISTRATOR.DS.'components' );
		
		$uid = JRequest::getVar( 'uid', '0', 'request');
	   	$redirect = PhplistUrl::appendUrl('index.php?option=com_phplist&controller=newsletters&task=list');
	   	$redirect = JRoute::_( $redirect, false );
	   	$this->message = JText::_( "YOUR SUBSCRIPTION HAS BEEN ACTIVATED" );
		
		$phplistUser = PhplistHelperUser::confirmUser( $uid );
		
		$this->setRedirect( $redirect, $this->message, $this->messagetype );		
	}
}
?>