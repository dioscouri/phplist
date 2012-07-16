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

jimport('joomla.html.toolbar');
require_once( JPATH_ADMINISTRATOR.DS.'includes'.DS.'toolbar.php' );

class PhplistMenu 
{
	/**
	 * Returns HTML to display the submenu
	 * 
	 * @return unknown_type
	 */
	function display()
	{
		// Check the config to see if the admin has disabled the menu
		if (!PhplistConfig::getInstance()->get('display_submenu', '1'))
		{
			return null;
		}
		
		JHTML::_('stylesheet', 'menu.css', 'media/com_phplist/css/');
		
		$mainframe = JFactory::getApplication();

		// Lets get some variables we are going to need
		$menu = JToolBar::getInstance('submenu');
		$list = $menu->_bar;

		if (!is_array($list) || !count($list)) {
			return null;
		}

		$hide = JRequest::getInt('hidemainmenu');
		// $txt = "<ul id=\"submenu\">\n";
		$txt = "<div id=\"submenu\">\n";

		/*
		 * Iterate through the link items for building the menu items
		 */
		foreach ($list as $item)
		{
			// $txt .= "<li>\n";
			if ($hide)
			{
				if (isset ($item[2]) && $item[2] == 1) {
					$txt .= "<span class=\"nolink active\">".$item[0]."</span>\n";
				}
				else {
					$txt .= "<span class=\"nolink\">".$item[0]."</span>\n";
				}
			}
			else
			{
				if (isset ($item[2]) && $item[2] == 1) {
					$txt .= "<a class=\"active\" href=\"".JFilterOutput::ampReplace($item[1])."\">".$item[0]."</a>\n";
				}
				else {
					$txt .= "<a href=\"".JFilterOutput::ampReplace($item[1])."\">".$item[0]."</a>\n";
				}
			}
			// $txt .= "</li>\n";
		}

		$txt .= "</div>\n";

		return $txt;
	}
}