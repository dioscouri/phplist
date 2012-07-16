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
		JHTML::_('stylesheet', 'phplist_admin.css', 'media/com_phplist/css/');
		
		$this->displayTitle( $this->get('title') );
		
		if (!JRequest::getInt('hidemainmenu') && empty($this->hidemenu))
		{
			$this->displayMenubar();
		}
		
		$modules = JModuleHelper::getModules("phplist_left");
		if ($modules && !JRequest::getInt('hidemainmenu') || isset($this->leftMenu))
		{
			$this->displayWithLeftMenu($tpl=null);
		}
			else
		{
			parent::display($tpl);			
		}
	}
	
	function displayTitle( $text = '' )
	{
		$title = $text ? JText::_($text) : JText::_( ucfirst(JRequest::getVar('view')) );
		JToolBarHelper::title( $title, Phplist::getName() );
	}
	
	function getMenubar()
	{
		$views  = array();
		
		$views['dashboard']			= 'Dashboard';
        $views['users']				= 'Users';
		$views['subscriptions']  	= 'Subscriptions';
		$views['newsletters']		= 'Newsletters';
		$views['messages']			= 'Messages';
		$views['attributes']		= 'Attributes';
		$views['logs']				= 'Logs';
		$views['tools']				= 'Tools';
		$views['config']			= 'Configuration';
		
		return $views;
	}
	
	function displayMenubar()
	{
		$this->getMenubar();
		$views = $this->getMenubar();
		
		$left = array();
		if (isset($this->leftMenu)) { $left = $this->getLeftMenubar(); }
		
		foreach($views as $view => $title)
		{
			$current = strtolower( JRequest::getVar('view') );
			$active = ($view == $current );
			if (array_key_exists($current, $left) && $view == 'localization' ) { $active = true; }
			JSubMenuHelper::addEntry(JText::_($title), 'index.php?option=com_phplist&view='.$view, $active );
		}
	}
	
	function getLeftMenubar()
	{
		$views  = array();
		return $views;
	}
	
	function displayLeftMenubar($name='leftmenu')
	{
		JLoader::import( 'com_phplist.library.menu', JPATH_ADMINISTRATOR.DS.'components' );

		$views = $this->getLeftMenubar();
                   
		foreach($views as $view => $title)
		{	
			$active = ($view == strtolower( JRequest::getVar('view') ) );
			PhplistMenu::addEntry(JText::_($title), 'index.php?option=com_phplist&view='.$view, $active, $name );
		}
		
		echo PhplistMenu::display( $name, "{$name}_admin.css" );
	}
	
	/**
	 * 
	 * @param $tpl
	 * @return unknown_type
	 */
    public function displayWithLeftMenu($tpl=null)
    {
    	// TODO This is an ugly, quick hack - fix it
    	echo "<table width='100%'>";
    		echo "<tr>";
	    		echo "<td style='width: 180px; padding-right: 5px; vertical-align: top;' >";
	    		
					$this->displayLeftMenubar();
					
					$modules = JModuleHelper::getModules("phplist_left");
					$document	= &JFactory::getDocument();
					$renderer	= $document->loadRenderer('module');
					$attribs 	= array();
					$attribs['style'] = 'xhtml';
					foreach ( @$modules as $mod ) 
					{
						echo $renderer->render($mod, $attribs);
					}
					
	    		echo "</td>";
	    		echo "<td style='vertical-align: top;' >";
	    			parent::display($tpl);	
	    		echo "</td>";
    		echo "</tr>";
    	echo "</table>";
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
		$model = $this->getModel();

		// set the model state
			$this->assign( 'state', $model->getState() );
		
		// check config
			$this->assign( 'config', PhplistConfig::getInstance() );
		
		// add toolbar buttons
			$this->_defaultToolbar();
			
		// page-navigation
			$this->assign( 'pagination', $model->getPagination() );
		
		// list of items
			$this->assign('items', $model->getList());
			
		// form
			$validate = JUtility::getToken();
			$form = array();
			$view = strtolower( JRequest::getVar('view') );
			$form['action'] = "index.php?option=com_phplist&controller={$view}&view={$view}";
			$form['validate'] = "<input type='hidden' name='{$validate}' value='1' />";
			$this->assign( 'form', $form );
	}
	
	/**
	 * Basic methods for a form
	 * @param $tpl
	 * @return unknown_type
	 */
	function _form($tpl='')
	{
		JLoader::import( 'com_phplist.library.select', JPATH_ADMINISTRATOR.DS.'components' );
		JLoader::import( 'com_phplist.library.grid', JPATH_ADMINISTRATOR.DS.'components' );
		$model = $this->getModel();

		// set the model state
			$this->assign( 'state', $model->getState() );
			
		// check config
			$this->assign( 'config', PhplistConfig::getInstance() );
			
		// get the data
			// not using getItem here to enable ->checkout (which requires JTable object)
			$row = $model->getTable();
			$row->load( (int) $model->getId() );
			// TODO Check if the item is checked out and if so, setlayout to view

		// set toolbar
			$layout = $this->getLayout();
			$isNew = ($row->id < 1);
			switch(strtolower($layout))
			{
				case "view":
					$this->_viewToolbar($isNew);
				  break;
				case "form":
				default:
					// Checkout the item if it isn't already checked out
					$row->checkout( JFactory::getUser()->id );
					$this->_formToolbar($isNew);
				  break;
			}
			$view = strtolower( JRequest::getVar('view') );
			$this->displayTitle( 'Edit '.$view );
		
		// form
			$validate = JUtility::getToken();
			$form = array();
			$controller = strtolower( $this->get( '_controller', JRequest::getVar('controller', JRequest::getVar('view') ) ) );
			$view = strtolower( $this->get( '_view', JRequest::getVar('view') ) );
			$action = $this->get( '_action', "index.php?option=com_phplist&controller={$controller}&view={$view}&layout=form&id=".$model->getId() );
			$validation = $this->get( '_validation', "index.php?option=com_phplist&controller={$controller}&task=validate&format=raw" );
			$form['action'] = $action;
			$form['validation'] = $validation;
			$form['validate'] = "<input type='hidden' name='{$validate}' value='1' />";
			$form['id'] = $model->getId();
			$this->assign( 'form', $form );
			$this->assign('row', $model->getItem() );
			
		// set the required image
		// TODO Fix this
			$required = new stdClass();
			$required->text = JText::_( 'Required' );
			$required->image = "<img src='".JURI::root()."/media/".'com_phplist'."/images/required_16.png' alt='{$required->text}'>";
			$this->assign('required', $required );	
	}

	/**
	 *
	 *
	 */
	function _defaultToolbar()
	{
		JToolBarHelper::editList();
		JToolBarHelper::deleteList( JText::_( 'VALIDDELETEITEMS' ) );
		JToolBarHelper::addnew();
	}
	
	/**
	 *
	 *
	 */
	function _formToolbar( $isNew=null )
	{
		JToolBarHelper::custom('savenew', "savenew", "savenew", JText::_( 'Save + New' ), false);
		JToolBarHelper::save('save');
		JToolBarHelper::apply('apply');
		
		if ($isNew) 
		{
			JToolBarHelper::cancel();
		} 
			else 
		{
			JToolBarHelper::cancel( 'close', JText::_( 'Close' ) );
		}
	}

	/**
	 *
	 *
	 */
	function _viewToolbar( $isNew=null )
	{
		JToolBarHelper::cancel( 'close', JText::_( 'Close' ) );
	}
	
	/**
	 * Finds any plugins meant to extend the form and adds them if so 
	 * @return void
	 */
	function _getFormPlugins()
	{
		$view = strtolower( JRequest::getVar('view') );
		
        // Get plugins
        $filtered_sliders = array();
        JLoader::import( 'com_phplist.library.tools', JPATH_ADMINISTRATOR.DS.'components' );
        $items = PhplistTools::getPlugins();
		for ($i=0; $i < count($items); $i++) 
		{
			$item = &$items[$i];
			// Check if they have an event
			if (PhplistTools::hasEvent( $item, "getFormSliders{$view}" )) 
			{
				// add item to filtered array
				$filtered_sliders[] = $item;
			}
		}
		$items_sliders = $filtered_sliders;
        $this->assign( 'items_sliders', $items_sliders );
	}
}