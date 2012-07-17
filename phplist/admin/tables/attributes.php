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

class PhplistTableAttributes extends DSCTable
{
	function PhplistTableAttributes( &$db ) 
	{
		$tbl_key 	= 'id';
		$tbl_suffix = 'attributes';
		$this->set( '_suffix', $tbl_suffix );
		
		JLoader::import( 'com_phplist.helpers.phplist', JPATH_ADMINISTRATOR.DS.'components' );
		JLoader::import( 'com_phplist.helpers.attribute', JPATH_ADMINISTRATOR.DS.'components' );
		$database = PhplistHelperPhplist::getDatabase();		
		$tablename = PhplistHelperAttribute::getTableName();
		
		parent::__construct( $tablename, $tbl_key, $database );			
	}
	
	/**
	 * Compacts the listorder sequence of the selected records
	 *
	 * @access public
	 * @param string Additional where query to limit listorder to a particular subset of records
	 */
	function reorder( $where='' )
	{
		$k = $this->_tbl_key;
		$order2 = '';
		
		$query = 'SELECT '.$this->_tbl_key.', listorder'
		. ' FROM '. $this->_tbl
		. ' WHERE 1' . ( $where ? ' AND '. $where : '' )
		. ' ORDER BY listorder'.$order2
		;
		$this->_db->setQuery( $query );
		if (!($orders = $this->_db->loadObjectList()))
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		// compact the listorder numbers
		for ($i=0, $n=count( $orders ); $i < $n; $i++)
		{
			if ($orders[$i]->listorder >= 0)
			{
				if ($orders[$i]->listorder != $i+1)
				{
					$orders[$i]->listorder = $i+1;
					$query = 'UPDATE '.$this->_tbl
					. ' SET listorder = '. (int) $orders[$i]->listorder
					. ' WHERE '. $k .' = '. $this->_db->Quote($orders[$i]->$k)
					;
					$this->_db->setQuery( $query);
					$this->_db->query();
				}
			}
		}

		return true;
	}
	
	function move($change, $where='')
	{
		if ( !in_array( 'listorder', array_keys( $this->getProperties() ) ) ) 
		{
			$this->setError( get_class( $this ).' does not support ordering');
			return false;
		}

		settype($change, 'int');

		if ($change !== 0)
		{
			$old = $this->listorder;
			$new = $this->listorder + $change;
			$new = $new <= 0 ? 1 : $new;

			$query =  ' UPDATE '.$this->getTableName().' ';

			if ($change < 0) {
				$query .= 'SET listorder = listorder+1 WHERE '.$new.' <= listorder AND listorder < '.$old;
				$query .= ($where ? ' AND '.$where : '');
			} else {
				$query .= 'SET listorder = listorder-1 WHERE '.$old.' < listorder AND listorder <= '.$new;
				$query .= ($where ? ' AND '.$where : '');
			}
			
			$this->_db->setQuery( $query );
			if (!$this->_db->query())
			{
				$err = $this->_db->getErrorMsg();
				JError::raiseError( 500, $err );
				return false;
			}

			$this->listorder = $new;
			return $this->save();
		}

		return $this;
	}
}

?>