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

class PhplistGrid extends DSCGrid 
{ 
	public static function required()
	{
		$html = '<img src="'.Phplist::getUrl( 'images' ).'required_16.png" alt="'.JText::_('COM_PHPLIST_REQUIRED').'">';
		return $html;
	}
}
?>