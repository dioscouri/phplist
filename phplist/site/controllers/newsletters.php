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

		$uid = JRequest::getVar( 'uid', '0', 'request');
	   	$redirect = 'index.php?option=com_phplist&controller=newsletters&task=list';
	   	$redirect = JRoute::_( PhplistUrl::siteLink($redirect), false );
	   	
	   	// Validation is by JS validate() function . Duplicated here as a backup for if no JS.
		//check a newsletter is selected in list
		$cids = JRequest :: getVar('cid', array(0), 'request', 'array');
		if (intval($cids['0']) == '0')
		{
			$this->message = JText::_( "PLEASE_SELECT_A_NEWSLETTER" );
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

		$redirect = 'index.php?option=com_phplist&view=newsletters';
		$redirect = JRoute::_( PhplistUrl::siteLink($redirect), false );
		
		// Validation is by JS validate() function in root site controller. Duplicated here as a backup.
		//check a newsletter is selected in list
		$cids = JRequest :: getVar('cid', array(0), 'request', 'array');
		if (intval($cids['0']) == '0')
		{
			$this->message = JText::_( "PLEASE_SELECT_A_NEWSLETTER" );
			$this->messagetype	= 'notice';
			$this->setRedirect( $redirect, $this->message, $this->messagetype );
			return false;
		}

		$subscriber2add = JRequest::getVar( 'subscriber2add' );
		
		jimport('joomla.mail.helper');
		if (!$isEmailAddress = JMailHelper::isEmailAddress( $subscriber2add ))
		{
			$this->message .= JText::_( "PLEASE_ENTER_A_VALID_EMAIL_ADDRESS" );
			$this->messagetype	= 'notice';
			$this->setRedirect( $redirect, $this->message, $this->messagetype );
			return false;
		}
		
		// If joomla user account exists for email address, ask them to login
		if ($emailExists = PhplistHelperUser::emailExists( $subscriber2add, '1' ))
		{
			$this->message .= JText::_( "EMAIL_IS_JUSER" );
			$this->setRedirect( $redirect, $this->message, $this->messagetype );
			return false;
		}
		else
		{
			
			if ($user = PhplistHelperUser::getUser( $subscriber2add, '1', 'email' ))
			{
				$this->message .= JText::_( "EMAIL_IS_PHPLISTUSER" );
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
					$this->message = JText::_( "COULD_NOT_CREATE_NEW_PHPLIST_USER" );
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
		
		$redirect = 'index.php?option=com_phplist&view=newsletters';
		$redirect = JRoute::_( PhplistUrl::siteLink($redirect), false );
		$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}
	
	/**
	 * Confirms a user when they have clicked link in activation email
	 * @return void
	 */
	function confirm_user() 
	{
		
		$uid = JRequest::getVar( 'uid', '0', 'request');
	   	$redirect = 'index.php?option=com_phplist&controller=newsletters&task=list';
	   	$redirect = JRoute::_( PhplistUrl::siteLink($redirect), false );
	   	$this->message = JText::_( "YOUR_SUBSCRIPTION_HAS_BEEN_ACTIVATED" );
		
		$phplistUser = PhplistHelperUser::confirmUser( $uid );
		
		$this->setRedirect( $redirect, $this->message, $this->messagetype );		
	}
	
	/**
	 *
	 * @return
	 */
	function validate()
	{
	
		$success = true;
		$response = array();
		$response['msg'] = "";
		$response['error'] = "";
		$msg = new stdClass();
		$msg->message = "";
		$msg->error = "";
	
		//get front end attributes info
		$attributes = PhplistHelperAttribute::getAttributes('1');
		$required_attribs = false;
	
		// get elements from post
		$elements = json_decode( preg_replace('/[\n\r]+/', '\n', JRequest::getVar( 'elements', '', 'post', 'string' ) ) );
		// elements is an array of objects
		// $object->name
		// $object->value
		// $object->id (if present, is the element id in the form)
	
		// loop through every field in the form
		// collect the ones to be verified
		for ($i=0; $i<count($elements); $i++)
		{
		$element = $elements[$i];
		if (trim(strtolower($element->name)) == 'subscriber2add')
		{
		$email = $element->value;
			
		}
		if (trim(strtolower($element->name)) == 'boxchecked')
		{
		$cid = $element->value;
		}
	
			// attributes fields
			if ($attributes)
			{
			foreach ($attributes as $a)
		{
		if ($a->required == '1')
		{
		//replace spaces with _ in input names
		$name = str_replace(' ','_',$a->name);
			$name = str_replace('.','_',$name);
	
			if ($element->name == $name)
			{
			$required_attribs[$name]->value = $element->value;
			$required_attribs[$name]->type = $a->type;
				$required_attribs[$name]->name = $a->name;
				$required_attribs[$name]->checked = $element->checked;
			}
			}
			}
			}
		}
	
		if (isset($email))
		{
		if (empty($email))
		{
		$msg->message .= '<li>"'.JText::_( 'Email' ).'" '.JText::_( 'is Required' ).'</li>';
		$msg->error = '1';
		}
		else
		{
		jimport('joomla.mail.helper');
		if (!$isEmailAddress = JMailHelper::isEmailAddress( $email ))
	
		{
		$msg->message .= '<li>'.JText::_( 'PLEASE_ENTER_A_VALID_EMAIL_ADDRESS' ).'</li>';
		$msg->error = '1';
		}
		if ($emailExists = PhplistHelperUser::emailExists( $email, '1' ))
		{
		$msg->message .= '<li>'.JText::_( 'EMAIL_IS_JUSER' ).'</li>';
		$msg->error = '1';
		}
		elseif ($user = PhplistHelperUser::getUser( $email, '1', 'email' ))
		{
		$msg->message .= '<li>'.JText::_( 'EMAIL_IS_PHPLISTUSER' ).'</li>';
		$msg->error = '1';
		}
		}
		}
		if ($cid == '' || $cid == '0')
		{
		$msg->message .= '<li>'.JText::_( 'PLEASE_SELECT_A_NEWSLETTER' ).'</li>';
			$msg->error = '1';
		}
			
		// validate required Attributes if they exist
			if ($attributes && $required_attribs)
			{
			foreach ($required_attribs as $a)
				{
				switch ($a->type)
				{
				case 'textline':
				case 'textarea':
				case 'date': //TODO make this check for valid date
				default:
				if ($a->value == '')
				{
				$msg->message .= '<li>'.$a->name. ' ' .JText::_( 'IS_REQUIRED' ).'</li>';
				$msg->error = '1';
				}
				break;
				case 'checkbox':
				case 'radio':
				if ($a->checked != '1')
				{
				$msg->message .= '<li>'.$a->name. ' ' .JText::_( 'IS_REQUIRED' ).'</li>';
				$msg->error = '1';
				}
				break;
				case 'checkboxgroup':
				case 'select':
				// TODO add validation for these
				break;
				}
					}
				}
	
				// set response array
				if (!empty($msg->error))
				{
				$response['msg'] = '
				<dl id="system-message">
				<dt class="notice">notice</dt>
				<dd class="notice message fade">
				<ul>'.
				$msg->message
				.'</ul>
				</dd>
				</dl>
				';
				$response['error'] = '1';
				}
					
				// encode and echo (need to echo to send back to browser)
				echo ( json_encode( $response ) );
	
				return $success;
				}
}
?>