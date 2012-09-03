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

class PhplistModelForward extends PhplistModelBase
{
	function __construct($config = array())
	{
		parent::__construct($config);
		$database = PhplistHelperPhplist::setPhplistDatabase();
	}
	/**
	 * Method to set the identifier
	 *
	 * @access	public
	 * @param	int identifier
	 * @return	void
	 */
	function setId($id)
	{
		// Set id and wipe data
		$this->_id		= $id;
		$this->_data	= null;
	}
	
	/**
	 * Retrieves the data
	 * @return object containing the record/data from the database
	 */
	function getData()
	{
		global $mainframe;
		// load the data if it doesn't already exist
		if (empty( $this->_data )) {
			$row = $this->getTable( 'message' );
			$row->load( $this->_id );			
			$this->_data = $row;
		}
		return $this->_data;
	}
	
	function getTable($name='', $prefix='PhplistTable', $options = array())
	{
		// default table for this model is not Forward, but rather Messages
		if (empty($name))
		{
			$name = 'Messages';
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