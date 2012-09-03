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
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

class PhplistUrl extends DSCUrl
{
	/**
	 * Get the link to a menu by specifying it's ID
	 *
	 * @param $menu_id integer The menu's ID
	 */
	function getMenuLink($menu_id)
	{
	
		Phplist::load('PhplistMenu', 'library.menu');
		$menu =& PhplistMenu::getInstance( 'Menu' );
	
		if (!$menu->load($menu_id) || trim($menu->link) == '')
		{
			return 'index.php';
		}
	
		return $menu->link;
	}
	
	/**
	 * Wrapper that adds the current Uid to the URL
	 *
	 * @param	string $string The string to translate
	 *
	 */
	function siteLink( $url )
	{
		$return = DSCUrl::addItemid($url);
		
		if ($uid = PhplistHelperUser::getUid()) {
			$return .= '&uid='.$uid;
		}
		return $return;
	}

}