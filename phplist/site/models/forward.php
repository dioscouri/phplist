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

JLoader::import( 'com_phplist.models._base', JPATH_ADMINISTRATOR.DS.'components' );

class PhplistModelForward extends PhplistModelBase
{
	/**
	 * Constructor needs to set the DBO for the whole model
	 * @param $config
	 * @return unknown_type
	 */
	function __construct($config = array())
	{
		parent::__construct($config);
		
		// get the phplist DBO
			JLoader::import( 'com_phplist.helpers.phplist', JPATH_ADMINISTRATOR.DS.'components' );
			$database = PhplistHelperPhplist::getDatabase();
			// set the model's database object to the phplist db
			$this->setDBO( $database );
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
}

?>