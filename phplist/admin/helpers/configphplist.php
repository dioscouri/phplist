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

Phplist::load( 'PhplistHelperBase', 'helpers.base');

class PhplistHelperConfigPhplist extends PhplistHelperBase
{
	/**
	 * Returns the phphlist log table name
	 * @return unknown_type
	 */
	function getTableName() 
	{
		$success = false;
		$phplist_prefix = PhplistHelperPhplist::getPrefix();
		$success = "{$phplist_prefix}_config";
		return $success;
	}
}

?>
