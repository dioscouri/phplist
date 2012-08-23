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

jimport( 'joomla.application.component.controller' );

class PhplistController extends DSCControllerSite 
{
	var $_models = array();
	var $message = "";
	var $messagetype = "";
	public $default_view = 'newsletters';

	/**
	 * allows user to manage subscriptions via uid rather than logging in.
	 * Serves as target for subscribe links 
	 * which will be in the format:
	 * index.php?option=com_phplist&task=subscribe&uid=38338f464bb36127b34da1f63de666a5
	 * 
	 * @return unknown_type
	 */
	function subscribe()
	{	
		$uid = JRequest::getVar( 'uid' );
		
		$msg = new stdClass();
		$msg->type 		= "";
		$msg->message 	= "";
		$msg->link 		= "index.php?option=com_phplist&view=subscribe&uid={$uid}";
		
		if ($id = PhplistUrl::getItemid()) {
			$msg->link .= "&Itemid={$id}";
		}
		
		$msg->link 		= JRoute::_( $msg->link, false );
		$this->setRedirect( $msg->link, $msg->message, $msg->type );

	}
	
	/**
	 * allows user to unsubscribe from all newsletters via one-click using uid.
	 * Serves as target for unsubscribe links 
	 * which will be in the format:
	 * index.php?option=com_phplist&task=unsubscribe&uid=38338f464bb36127b34da1f63de666a5
	 * 
	 * @return unknown_type
	 */
	function unsubscribe()
	{		
		$uid = JRequest::getVar( 'uid' );
		
		$msg = new stdClass();
		$msg->type 		= "";
		$msg->message 	= "";
		$msg->link 		= "index.php?option=com_phplist&view=unsubscribe&uid={$uid}";
		
		if ($id = PhplistUrl::getItemid()) {
			$msg->link .= "&Itemid={$id}";
		}
		
		$msg->link 		= JRoute::_( $msg->link, false );
		$this->setRedirect( $msg->link, $msg->message, $msg->type );
	}
	
	/**
	 * allows user to forward a single message to another user.
	 * a typical incoming link looks like:
	 * index.php?option=com_phplist&task=forward&uid=38338f464bb36127b34da1f63de666a5&mid=13
	 */
	function forward()
	{
		// TODO Write this function
		return true;
					
		$uid = JRequest::getVar( 'uid' );
		$mid = JRequest::getVar( 'mid' );
		
		$msg = new stdClass();
		$msg->type 		= "";
		$msg->message 	= "";
		$msg->link 		= "index.php?option=com_phplist&view=forward&id={$mid}&uid={$uid}";
		
		if ($id = PhplistUrl::getItemid()) {
			$msg->link .= "&Itemid={$id}";
		}
		
		$msg->link 		= JRoute::_( $msg->link, false );
		$this->setRedirect( $msg->link, $msg->message, $msg->type );
	}
	
	/**
	 * 
	 * @return 
	 */
	function validate()
	{
		JLoader::import( 'com_phplist.helpers.user', JPATH_ADMINISTRATOR.DS.'components' );
		JLoader::import( 'com_phplist.helpers.attribute', JPATH_ADMINISTRATOR.DS.'components' );
		JLoader::import( 'com_phplist.library.json', JPATH_ADMINISTRATOR.DS.'components' );
		
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
						$msg->message .= '<li>'.JText::_( 'PLEASE ENTER A VALID EMAIL ADDRESS' ).'</li>';
						$msg->error = '1';
					}
					if ($emailExists = PhplistHelperUser::emailExists( $email, '1' ))
					{
						$msg->message .= '<li>'.JText::_( 'EMAIL IS JUSER' ).'</li>';
						$msg->error = '1';
					}
					elseif ($user = PhplistHelperUser::getUser( $email, '1', 'email' ))
					{
						$msg->message .= '<li>'.JText::_( 'EMAIL IS PHPLISTUSER' ).'</li>';
						$msg->error = '1';
					}
				}
			}
			if ($cid == '' || $cid == '0')
			{
				$msg->message .= '<li>'.JText::_( 'PLEASE SELECT A NEWSLETTER' ).'</li>';
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
								$msg->message .= '<li>'.$a->name. ' ' .JText::_( 'IS REQUIRED' ).'</li>';
								$msg->error = '1';
							}
							break;
						case 'checkbox':
						case 'radio':
							if ($a->checked != '1')
							{
								$msg->message .= '<li>'.$a->name. ' ' .JText::_( 'IS REQUIRED' ).'</li>';
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

	/**
	 * This method processes the form submitted by the mod_subscribe module.
	 * It is just a wrapper for the module's helper, which sets the html
	 * so this entire process is confined to the module itself
	 * 
	 * @return unknown_type
	 */
	function subscribeModule()
	{
		// include the module's helper file
		JLoader::import( 'mod_phplist_subscribe.helper', JPATH_SITE.DS.'modules' );
		// display whatever output comes from the processForm method
		echo modPhplistSubscribeHelper::processForm();
	}
	
	
	
}
?>