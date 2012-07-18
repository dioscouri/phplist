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

Phplist::load( 'PhplistViewBase', 'views.base' );

class PhplistViewUsers extends PhplistViewBase 
{
	function _form($tpl=null)
    {
    	parent::_form($tpl);
    	
    	JLoader::import( 'com_phplist.helpers.subscription', JPATH_ADMINISTRATOR.DS.'components' );
    	
    	/// get newsletters list
		$model = JModel::getInstance( 'Newsletters', 'PhplistModel' );
		$model->setState( 'order', 'name' );
		$model->setState( 'direction', 'ASC' );
		$items = $model->getList();
		$this->assign( 'newsletters', $items );
		
		// check if adding new user
		if (!@$this->row->id)
		{
			$this->assign( 'new', true );
		}
		else
		{
			$this->assign( 'new', false );
		}
		
		// Get default values from config
		$config = Phplist::getInstance();
		$this->assign( 'default_html', $config->get( 'default_html', '1' ) );
    	$activation_email = $config->get( 'activation_email', '1' );
		if ($activation_email == '1') 
		{
			$confirmed = '0';
		}
		else
		{
			$confirmed = '1';
		}
		$this->assign( 'activation_email', $confirmed );
		
		// Add pane
		jimport('joomla.html.pane');
		$sliders = JPane::getInstance( 'sliders' );		
		$this->assignRef('sliders', $sliders);		
    }

	function _defaultToolbar()
	{
		JToolBarHelper::custom('sync', "refresh_window.png", "refresh_window.png", JText::_( 'SYNC USERS' ), false);
		JToolBarHelper::custom('confirmed.enable', "publish.png", "icon-32-publish.png", JText::_( 'CONFIRM' ), true);
		JToolBarHelper::custom('confirmed.disable', "unpublish.png", "icon-32-unpublish.png", JText::_( 'UNCONFIRM' ), true);
		JToolBarHelper::custom('enroll_flex', "book_add.png", "book_add.png", JText::_( 'FLEX' )." +", true);
		JToolBarHelper::custom('withdraw_flex', "book_remove.png", "book_remove.png", JText::_( 'FLEX' )." -", true);
		JToolBarHelper::custom('withdraw_all', "paste_remove.png", "paste_remove.png", JText::_( 'WITHDRAW ALL' ), true);
		JToolBarHelper::editList();
		JToolBarHelper::custom('delete', "delete.png", "icon-32-delete.png", JText::_( 'DELETE' ), true);
		JToolBarHelper::addnew();
	}
}

?>