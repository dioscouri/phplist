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