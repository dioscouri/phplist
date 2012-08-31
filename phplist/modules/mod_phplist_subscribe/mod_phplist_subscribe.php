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

// Include the helper file
require_once( dirname(__FILE__).DS.'helper.php' );

$isInstalled = modPhplistSubscribeHelper::_isInstalled();

// include lang files
$element = strtolower( 'com_phplist' );
$lang = JFactory::getLanguage();
$lang->load( $element, JPATH_BASE );
$lang->load( $element, JPATH_ADMINISTRATOR );

// if not installed, do nothing
if ( $isInstalled )
{
	//get module parameters
	$moduleclass_sfx = $params->get( 'moduleclass_sfx' );
	$display_type = $params->get( 'display_type', '1' );
	$display_url = $params->get( 'display_url', '1' );
	$display_url_prefs = $params->get( 'display_url_prefs', '1' );
	$display_already = $params->get( 'display_already', '0' );
	$newsletterid = $params->get( 'newsletterid', '0' );
	$display_html = $params->get( 'display_html', '0' );
		
	if (!$newsletterid && !$display_type)
	{
		// do nothing because no newsletterid set and form would fail
		$donothing = true;
	}
	else
	{
		// get logged in Joomla! user details
		$user = JFactory::getUser();
		$phplistUser = false;
		
		if ($uid =  JRequest::getVar( 'uid' ))
		{
			$phplistUser = PhplistHelperUser::getUser( $uid, '1', 'uid' );
		}
		
		else if ($user->id)
		{
			// get phplist user if exists
			$phplistUser = PhplistHelperUser::getUser( $user->id, '1', 'foreignkey' );
			if (!isset($phplistUser->id))
			{
				//create PHPList user if doesn't exist
				$phplistUser = PhplistHelperUser::create( $user );
			}
			
		}
		
		// check if user already subscribed to single newsletter id
		if ($phplistUser)
		{
			for ($i=0; $i<count($newsletterid); $i++) {
				$isSubscribed = PhplistHelperSubscription::isUser( $phplistUser->id, $newsletterid[$i] );
				if ($isSubscribed && !$display_already)
				{
					// do nothing
					$donothing = true;
				}
			}
		}
		
		// get attributes form fields
		$attributes = PhplistHelperAttribute::getAttributes(true);
		
		// newlsetters page link
		$newsletters_link = "index.php?option=com_phplist&view=newsletters";
		if ($itemid = modPhplistSubscribeHelper::getItemid())
		{
			$newsletters_link .= "&Itemid={$itemid}";	
		}
		$newsletters_link = JRoute::_( $newsletters_link, true );
		
		// newlsetters page link
		$prefs_link = "index.php?option=com_phplist&view=preferences";
		if ($itemid = modPhplistSubscribeHelper::getItemid())
		{
			$prefs_link .= "&Itemid={$itemid}";	
		}
		$prefs_link = JRoute::_( $prefs_link, true );
		
		
		// get return URL
		$return = modPhplistSubscribeHelper::getReturnURL( $params );

	}

	if (!isset($donothing))
	{
		require(JModuleHelper::getLayoutPath('mod_phplist_subscribe'));
	}

}
?>