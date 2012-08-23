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

Phplist::load( 'PhplistModelBase', 'models.base' );

class PhplistModelPreferences extends PhplistModelBase 
{
	function __construct($config = array())
	{
		parent::__construct($config);
		$database = PhplistHelperPhplist::setPhplistDatabase();
	}
	
	/**
	 * This model's default table is the phplistuser table
	 * @return unknown_type
	 */
    function getTable()
    {
        JTable::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_phplist'.DS.'tables' );
        $table = JTable::getInstance( 'PhplistUser', 'Table' );
        return $table;
    }
}

?>