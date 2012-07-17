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

class PhplistViewConfig extends PhplistViewBase 
{
	/**
	 * 
	 * @return void
	 **/
	function _default($tpl = null) 
	{
		parent::_default($tpl);
		JLoader::import( 'com_phplist.library.tools', JPATH_ADMINISTRATOR.DS.'components' );
		JLoader::import( 'com_phplist.library.select', JPATH_ADMINISTRATOR.DS.'components' );

		// check config
			$row = Phplist::getInstance();
			$this->assignRef( 'row', $row );
		
		// plugins
        	$filtered = array();
	        $items = PhplistTools::getPlugins();
			for ($i=0; $i<count($items); $i++) 
			{
				$item = &$items[$i];
				// Check if they have an event
				if ($hasEvent = PhplistTools::hasEvent( $item, 'onListConfigPhplist' )) {
					// add item to filtered array
					$filtered[] = $item;
				}
			}
			$items = $filtered;
			$this->assignRef( 'items_sliders', $items );
			
		// Add pane
			jimport('joomla.html.pane');
			$sliders = JPane::getInstance( 'sliders' );		
			$this->assignRef('sliders', $sliders);
			
		// set the required image
		// TODO Fix this to use defines
			$required = new stdClass();
			$required->text = JText::_( 'REQUIRED' );
			$required->image = "<img src='".JURI::root()."/media/com_phplist/images/required_16.png' alt='{$required->text}'>";
			$this->assignRef('required', $required );
    }
    
	function _defaultToolbar()
	{
		JToolBarHelper::save('save');
		// TODO make cancel button redirect to dashboard?
		//JToolBarHelper::cancel( 'close', JText::_( 'CLOSE' ) );
	}
}

?>