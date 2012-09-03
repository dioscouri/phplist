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

jimport( 'joomla.application.component.view' );

class PhplistViewBase extends DSCViewSite 
{
	function display($tpl=null)
	{
		JHTML::_('stylesheet', 'menu.css', 'media/com_phplist/css/');
		
		$parentPath = JPATH_ADMINISTRATOR . '/components/com_phplist/helpers';
		DSCLoader::discover('PhplistHelper', $parentPath, true);
		
		$parentPath = JPATH_ADMINISTRATOR . '/components/com_phplist/library';
		DSCLoader::discover('Phplist', $parentPath, true);
		
		parent::display($tpl);
	}
	
	function displaySubmenu($selected='')
	{
		JLoader::import( 'com_phplist.library.url', JPATH_ADMINISTRATOR.DS.'components' );
		
		if (!JRequest::getInt('hidemainmenu')) 
		{
			jimport('joomla.html.toolbar');
			require_once( JPATH_ADMINISTRATOR.DS.'includes'.DS.'toolbar.php' );
			$view = strtolower( JRequest::getVar('view') );
			
			if (JRequest::getVar('layout') == 'view')
			{
				JSubMenuHelper::addEntry(JText::_('RETURN_TO_LIST_OF_MESSAGES'), PhplistUrl::siteLink('index.php?option=com_phplist&view=messages&task=list&id=' . JRequest::getVar('newsletterid')), $view == 'messages' ? true : false );
			}
			if ($view != 'newsletters')
			{
				JSubMenuHelper::addEntry(JText::_('RETURN_TO_LIST_OF_NEWSLETTERS'), PhplistUrl::siteLink('index.php?option=com_phplist&view=newsletters'), $view == 'newsletters' ? true : false );
			}
			$isUser = '';
			
			//only display preferences link for logged in or valid uniqid users..
			$isUser = PhplistHelperUser::getUid();
			
			if (JFactory::getUser()->id || $isUser)
			{
				if ($view != 'preferences')
				{
					JSubMenuHelper::addEntry(JText::_('EDIT_PREFERENCES'), PhplistUrl::siteLink('index.php?option=com_phplist&view=preferences'), $view == 'preferences' ? true : false );	
				}
			}			
		}
	}
	
	/**
	 * Basic commands for displaying a list
	 *
	 * @param $tpl
	 * @return unknown_type
	 */
	function _default($tpl='')
	{
		parent::_default();
	
		//get uid
		$uid = PhplistHelperUser::getUid();
		$this->assign( 'uid', $uid );
	}
	
	/**
	 * Basic methods for a form
	 * @param $tpl
	 * @return unknown_type
	 */
	function _form($tpl='')
	{
		parent::_form();
	
		//get uid
		$uid = PhplistHelperUser::getUid();
		$this->assign( 'uid', $uid );
	}
}

?>