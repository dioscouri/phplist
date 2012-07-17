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

class PhplistViewNewsletters extends PhplistViewBase
{
	
	function _default($tpl=null)
    {
    	parent::_default($tpl);
		JLoader::import( 'com_phplist.helpers.newsletter', JPATH_ADMINISTRATOR.DS.'components' );	
    }
	
	function _defaultToolbar()
	{
		JToolBarHelper::custom('active.enable', "publish.png", "icon-32-publish.png", JText::_( 'PUBLISH' ), true);
		JToolBarHelper::custom('active.disable', "unpublish.png", "icon-32-unpublish.png", JText::_( 'UNPUBLISH' ), true);	
		JToolBarHelper::divider();
		parent::_defaultToolbar();
	}
}

?>