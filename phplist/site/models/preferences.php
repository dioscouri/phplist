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
	
    function getTable($name='', $prefix='PhplistTable', $options = array())
    {
    	// default table for this model is not Preferences, but rather Users
    	if (empty($name))
    	{
    		$name = 'Users';
    	}
    
    	if($table = &$this->_createTable( $name, $prefix, $options ))  {
    		return $table;
    	}
    
    	JError::raiseError( 0, 'Table ' . $prefix . $name . ' not supported. File not found.' );
    	$null = null;
    	return $null;
    }
}

?>