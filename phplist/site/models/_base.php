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

jimport( 'joomla.application.component.model' );
JLoader::import( 'com_phplist.library.query', JPATH_ADMINISTRATOR.DS.'components' );

class PhplistModelBase extends JModel 
{
	/**
	 * Gets a property from the model's state, or the entire state if no property specified 
	 * @param $property
	 * @param $default
	 * @return unknown_type
	 */
	public function getState( $property=null, $default=null )
	{
		return $property === null ? $this->_state : $this->_state->get($property, $default);
	}
	
	/**
	 * Gets the model's query, building it if it doesn't exist
	 * @return valid query object
	 */
	public function getQuery()
	{
		if (empty( $this->_query ) )
		{
			$this->_query = $this->_buildQuery(); 
		}
		return $this->_query;
	}
	
	/**
	 * Sets the model's query
	 * @param $query	A valid query object
	 * @return valid query object
	 */
	public function setQuery( $query )
	{
		$this->_query = $query;
		return $this->_query;		
	}
	
 	/**
     * Get the default states
     */
    public function getDefaultState()
    {
		$app 	= JFactory::getApplication();
		$state	= array();
		
    	// Get the namespace
    	$ns  	= $app->getName().'::'.'com.'.Phplist::getName().'.model.'.$this->getTable()->get('_suffix');

		//$limitstart		= JRequest::getInt('limitstart');
		//$limitstart 	= ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		
        $state['limit']  	= $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
        $state['limitstart'] = $app->getUserStateFromRequest($ns.'limitstart', 'limitstart', 0, 'int');
        $state['order']     = $app->getUserStateFromRequest($ns.'.filter_order', 'filter_order', 'tbl.'.$this->getTable()->getKeyName(), 'cmd');
        $state['direction'] = $app->getUserStateFromRequest($ns.'.filter_direction', 'filter_direction', 'ASC', 'word');
        $state['filter']    = $app->getUserStateFromRequest($ns.'.filter', 'filter', '', 'string');
        $state['id']        = JRequest::getVar('id', array('post', 'get'), '', 'int');

        // TODO santize the filter
        // $state['filter']   	= 

  		return $state;
    }
    
    /**
     * 
     * @return unknown_type
     */
    public function getFilters()
    {
    	$filters = array();
    	
		$filters['limit']       = $this->getState('limit');
		$filters['limitstart']   = $this->getState('limitstart');
		$filters['order']       = $this->getState('order');
		$filters['direction']   = $this->getState('direction');
		$filters['filter']      = $this->getState('filter');
		
		return $filters;    		
    }
    
	/**
	 * Retrieves the data for a paginated list
	 * @return array Array of objects containing the data from the database
	 */
	function getList() 
	{
		if (empty( $this->_list )) 
		{
			$query = $this->getQuery();
			$this->_list = $this->_getList( (string) $query, $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_list;
	}
	
	/**
	 * Gets an item for displaying (as opposed to saving, which requires a JTable object)
	 * using the query from the model
	 * 
	 * @return database->loadObject() record
	 */
	function getItem()
	{
		if (empty( $this->_item )) 
		{
			$query = $this->getQuery();
			$keyname = $this->getTable()->getKeyName();
			$value	= $this->_db->Quote( $this->getId() );
			$query->where( "tbl.$keyname = $value" );
			$this->_db->setQuery( (string) $query );
			$this->_item = $this->_db->loadObject();
		}
		return $this->_item;
	}
	
	/**
	 * Retrieves the data for an un-paginated list
	 * @return array Array of objects containing the data from the database
	 */
	function getAll()
	{
		if (empty( $this->_all )) 
		{
			$query = $this->getQuery();
			$this->_all = $this->_getList( (string) $query, 0, 0 );
		}
		return $this->_all;		
	}

	/**
	 * Paginates the data
	 * @return array Array of objects containing the data from the database
	 */
	function getPagination() 
	{
		if (empty($this->_pagination)) 
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_pagination;
	}

	/**
	 * Retrieves the count 
	 * @return array Array of objects containing the data from the database
	 */
	function getTotal() 
	{
		if (empty($this->_total)) 
		{
			$query = $this->getQuery();
			$this->_total = $this->_getListCount( (string) $query);
		}
		return $this->_total;
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
	 * Gets the identifier, setting it if it doesn't exist
	 * @return unknown_type
	 */
	function getId()
	{
		if (empty($this->_id))
		{
			$id = JRequest::getVar( 'id', JRequest::getVar( 'id', '0', 'post', 'int' ), 'get', 'int' );
			$array = JRequest::getVar('cid', array( $id ), 'post', 'array');
			$this->setId( (int) $array[0] );			
		}

		return $this->_id;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	function save()
	{
		$error = false;
		
		// bind the entry
	    $row = $this->getTable();
	    $row->load( $this->getId() );
		$row->bind( $_POST );
		
		if ( !$row->check() ) 
		{
        	$this->setError( $row->getError() );
        	return false;
		}

   		// Store the entry
    	if (!$row->store()) 
    	{
        	$this->setError( $row->getError() );
        	return false;
    	}
    	
    	$this->setId( $row->id );
    	
		// fix non-incremental orders
		$row->reorder();
		
    	return $row;
	}
	
	/**
	 * Method to delete record(s)
	 *
	 * @access    public
	 * @return    boolean    True on success
	 */
	function delete()
	{
		$cids = JRequest::getVar('cid', array (0), 'post', 'array');
		$row = $this->getTable();

		foreach ($cids as $cid)
		{
			if (!$row->delete($cid))
			{
				$msg->message .= $row->getError(); 
				$msg->type = 'notice';
			}
		}

		return true;
	}
	
	/**
	 * 
	 *
	 * @access    public
	 * @return    boolean    True on success
	 */
	function enable()
	{
		$success = true;
		$cids 	= JRequest::getVar('cid', array (0), 'post', 'array');
		$row 	= $this->getTable();
		
		$task = JRequest::getVar( 'task' );
		switch (strtolower($task))
		{
			case "switch":
			case "switch_publish":
			case "switch_enable":
				$switch = '1';
			  break;
			case "unpublish":
			case "disable":
				$enable = '0';
				$switch = '0';
			  break;
			case "publish":
			case "enable":
			default:
				$enable = '1';
				$switch = '0';
			  break;
		}

		foreach ($cids as $cid)
		{
			$row->load( $cid );
			
			switch ($switch)
			{
				case "1":
					// do switch
					$row->enabled = $row->enabled ? '0' : '1';
				  break;
				case "0":
				default:
					$row->enabled = $enable;
				  break;
			}
			
			if (!$row->check())
			{
				$this->setError( $row->getError() );
				$success = false;
			}
			else 
			{
				if (!$row->store())
				{
					$this->setError( $row->getError() );
					$success = false;
				}
			}
		}

		return $success;
	}
	
	/**
	 * 
	 *
	 * @access    public
	 * @return    boolean    True on success
	 */
	function boolean()
	{
		$success = true;
		$cids 	= JRequest::getVar('cid', array (0), 'post', 'array');
		$row 	= $this->getTable();
		
		$task = JRequest::getVar( 'task' );
		$vals = explode('_', $task);
		
		$field = $vals['0'];
		$action = $vals['1'];		
		
		switch (strtolower($action))
		{
			case "switch":
				$switch = '1';
			  break;
			case "disable":
				$enable = '0';
				$switch = '0';
			  break;
			case "enable":
				$enable = '1';
				$switch = '0';
			  break;
			default:
				$this->setError( 'Invalid Task' );
				return false;
			  break;
		}

		foreach ($cids as $cid)
		{
			$row->load( $cid );
			
			switch ($switch)
			{
				case "1":
					$row->$field = $row->$field ? '0' : '1';
				  break;
				case "0":
				default:
					$row->$field = $enable;
				  break;
			}
			
			if (!$row->check())
			{
				$this->setError( $row->getError() );
				$success = false;
			}
			else 
			{
				if (!$row->store())
				{
					$this->setError( $row->getError() );
					$success = false;
				}
			}
		}

		return $success;
	}
	
	/**
	 *
	 * @access    public
	 * @return    boolean    True on success
	 */
	function order()
	{
		$success 	= true;

		$change 	= JRequest::getVar('order_change', '0', 'post', 'int');
		$row 		= $this->getTable();
		$row->load( $this->getId() );
		
		if (empty($row->id))
		{
			$this->setError( "Invalid Item" );
			return false;	
		}
		
		$row->move( $change );
		
		return $row;
	}
	
	/**
	 *
	 * @access    public
	 * @return    boolean    True on success
	 */
	function ordering()
	{
		$success 	= true;
		
		$cids 		= JRequest::getVar('cid', array(0), 'post', 'array');
		$ordering 	= JRequest::getVar('ordering', array(0), 'post', 'array');
		$row 		= $this->getTable();
		
		foreach ($cids as $cid)
		{
			$row->load( $cid );
			$row->ordering = @$ordering[$cid];
			
			if (!$row->store())
			{
				$msg->message .= $row->getError(); 
				$msg->type = 'notice';
				$success = false;
			}
		}
		
		// fix non-incremental orders
		$row->reorder();

		return $success;
	}
	
    /**
     * Builds a generic SELECT query
     *
     * @return  string  SELECT query
     */
    protected function _buildQuery()
    {
    	if (!empty($this->_query))
    	{
    		return $this->_query;
    	}
    	
    	$query = new PhplistQuery();
    	
        $this->_buildQueryFields($query);
        $this->_buildQueryFrom($query);
        $this->_buildQueryJoins($query);
        $this->_buildQueryWhere($query);
        $this->_buildQueryGroup($query);
        $this->_buildQueryHaving($query);
        $this->_buildQueryOrder($query);

		return $query;
    }

 	/**
     * Builds a generic SELECT COUNT(*) query
     */
    protected function _buildCountQuery()
    {
    	$query = new PhplistQuery();
    	$query->select( $this->getState( 'select', 'COUNT(*)' ) );
    	
        $this->_buildQueryFrom($query);
        $this->_buildQueryJoins($query);
        $this->_buildQueryWhere($query);

        //        $this->_buildQueryGroup($query);
		//        $this->_buildQueryHaving($query);
		//        $this->_buildQueryOrder($query);
        
        return $query;
    }

    /**
     * Builds SELECT fields list for the query
     */
    protected function _buildQueryFields(&$query)
    {
		$query->select( $this->getState( 'select', 'tbl.*' ) );
    }

	/**
     * Builds FROM tables list for the query
     */
    protected function _buildQueryFrom(&$query)
    {
    	$name = $this->getTable()->getTableName();
    	$query->from($name.' AS tbl');
    }

    /**
     * Builds JOINS clauses for the query
     */
    protected function _buildQueryJoins(&$query)
    {
    }

    /**
     * Builds WHERE clause for the query
     */
    protected function _buildQueryWhere(&$query)
    {
    }

    /**
     * Builds a GROUP BY clause for the query
     */
    protected function _buildQueryGroup(&$query)
    {
    }

    /**
     * Builds a HAVING clause for the query
     */
    protected function _buildQueryHaving(&$query)
    {
    }


    /**
     * Builds a generic ORDER BY clasue based on the model's state
     */
    protected function _buildQueryOrder(&$query)
    {
    	$order      = $this->_db->getEscaped( $this->getState('order') );
       	$direction  = $this->_db->getEscaped( strtoupper( $this->getState('direction') ) );
    	if ($order) 
    	{
    		$query->order("$order $direction");
    	}

		if (in_array('ordering', $this->getTable()->getColumns())) 
		{
    		$query->order('ordering ASC');
    	}
    }
}

?>