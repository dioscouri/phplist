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

class PhplistViewBase extends JView 
{
	function display($tpl=null)
	{
		JLoader::import( 'com_phplist.library.menu', JPATH_ADMINISTRATOR.DS.'components' );
		$this->displaySubmenu();
		
		parent::display($tpl);
	}
	
	function displaySubmenu($selected='')
	{
		JLoader::import( 'com_phplist.library.url', JPATH_ADMINISTRATOR.DS.'components' );
		
		if (!JRequest::getInt('hidemainmenu')) 
		{
			jimport('joomla.html.toolbar');
			require_once( JPATH_ADMINISTRATOR.DS.'includes'.DS.'toolbar.php' );
			JLoader::import( 'com_phplist.helpers.user', JPATH_ADMINISTRATOR.DS.'components' );
			$view = strtolower( JRequest::getVar('view') );
			
			if (JRequest::getVar('task') == 'view')
			{
				JSubMenuHelper::addEntry(JText::_('RETURN TO LIST OF MESSAGES'), PhplistUrl::appendURL('index.php?option=com_phplist&view=messages&task=list&id=' . JRequest::getVar('newsletterid')), $view == 'messages' ? true : false );
			}
			if ($view != 'newsletters')
			{
				JSubMenuHelper::addEntry(JText::_('RETURN TO LIST OF NEWSLETTERS'), PhplistUrl::appendURL('index.php?option=com_phplist&view=newsletters'), $view == 'newsletters' ? true : false );
			}
			$isUser = '';
			//only display preferences link for logged in or valid uniqid users..
			if ($this->uid)
			{
				$isUser = PhplistHelperUser::getUser($this->uid, '0', 'uid');
			}
			if (JFactory::getUser()->id || $isUser)
			{
				if ($view != 'preferences')
				{
					JSubMenuHelper::addEntry(JText::_('EDIT PREFERENCES'), PhplistUrl::appendURL('index.php?option=com_phplist&view=preferences'), $view == 'preferences' ? true : false );	
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
		JLoader::import( 'com_phplist.library.select', JPATH_ADMINISTRATOR.DS.'components' );
		JLoader::import( 'com_phplist.library.grid', JPATH_ADMINISTRATOR.DS.'components' );
		JLoader::import( 'com_phplist.library.url', JPATH_ADMINISTRATOR.DS.'components' );
		
		$model = $this->getModel();
		
		// set the model state
			$this->assign( 'state', $model->getState() );
			
		// page-navigation
			$this->assign( 'pagination', $model->getPagination() );
		
		// list of items
			$this->assign('items', $model->getList());
			
		// form
			$validate = JUtility::getToken();
			$form = array();
			$controller = strtolower( $this->get( '_controller', JRequest::getVar('controller', JRequest::getVar('view') ) ) );
			$view = strtolower( $this->get( '_view', JRequest::getVar('view') ) );
			$action = $this->get( '_action', "index.php?option=com_phplist&controller={$controller}&view={$view}" );
			$form['action'] = PhplistUrl::appendURL($action);
			$form['validate'] = "<input type='hidden' name='{$validate}' value='1' />";
			$form['validation'] = $this->get( '_validation', "index.php?option=com_phplist&controller={$controller}&task=validate&format=raw" );
			$this->assign( 'form', $form );
				
			//get user for plugins
			$user = &JFactory::getUser();
			$this->assign( 'user', $user );

			//get uid
			$uid = JRequest::getVar( 'uid' );
			$this->assign( 'uid', $uid );
			
			// set the required image
			// TODO Fix this
			$required = new stdClass();
			$required->text = JText::_( 'Required' );
			$required->image = "<img src='".JURI::root()."/media/com_phplist/images/required_16.png' alt='{$required->text}' />";
			$this->assign('required', $required );
	}
	
	/**
	 * Basic methods for a form
	 * @param $tpl
	 * @return unknown_type
	 */
	function _form($tpl='')
	{
		JLoader::import( 'com_phplist.library.select', JPATH_ADMINISTRATOR.DS.'components' );
		JLoader::import( 'com_phplist.library.url', JPATH_ADMINISTRATOR.DS.'components' );
		
		$model = $this->getModel();
			
		// get the data
			$row = $model->getTable();
			$row->load( (int) $model->getId() );
			$this->assign('row', $row);
		
		// form
			$validate = JUtility::getToken();
			$form = array();
			$controller = strtolower( $this->get( '_controller', JRequest::getVar('controller', JRequest::getVar('view') ) ) );
			$view = strtolower( JRequest::getVar('view') );
			$form['action'] = PhplistUrl::appendURL("index.php?option=com_phplist&controller={$view}&view={$view}&layout=form&id=".$model->getId());
			$form['validate'] = "<input type='hidden' name='{$validate}' value='1' />";
			$form['validation'] = $this->get( '_validation', "index.php?option=com_phplist&controller={$controller}&task=validate&format=raw" );
			$form['id'] = $model->getId();
			$this->assign( 'form', $form );
			
		// set the required image
		// TODO Fix this
			$required = new stdClass();
			$required->text = JText::_( 'Required' );
			$required->image = "<img src='".JURI::root()."/media/com_phplist/images/required_16.png' alt='{$required->text}'>";
			$this->assign('required', $required );
				
			//get user for plugins
			$user = &JFactory::getUser();
			$this->assign( 'user', $user );

			//get uid
			$uid = JRequest::getVar( 'uid' );
			$this->assign( 'uid', $uid );
	}
}

?>