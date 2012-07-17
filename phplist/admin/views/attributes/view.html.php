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

class PhplistViewAttributes extends PhplistViewBase 
{
	function _defaultToolbar()
	{
		JToolBarHelper::custom('required.enable', "publish.png", "icon-32-publish.png", JText::_( 'Make Required' ), true);
		JToolBarHelper::custom('required.disable', "unpublish.png", "icon-32-unpublish.png", JText::_( 'Make Optional' ), true);		
	}
}

?>