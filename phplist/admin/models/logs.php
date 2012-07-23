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

class PhplistModelLogs extends PhplistModelBase
{
	function __construct($config = array())
	{
		parent::__construct($config);
		$database = PhplistHelperPhplist::setPhplistDatabase();
	}
	
 	protected function _buildQueryWhere(&$query)
    {
       	$filter     = $this->getState('filter');
       	$page 		= $this->getState('filter_page');
        $filter_id_from = $this->getState('filter_id_from');
        $filter_id_to   = $this->getState('filter_id_to');
        $filter_date_from   = $this->getState('filter_date_from');
        $filter_date_to     = $this->getState('filter_date_to');

       	if ($filter) 
       	{
			$key	= $this->_db->Quote('%'.$this->_db->getEscaped( trim( strtolower( $filter ) ) ).'%');

			$where = array();
			$where[] = 'LOWER(tbl.id) LIKE '.$key;
			$where[] = 'LOWER(tbl.entry) LIKE '.$key;
			$where[] = 'LOWER(tbl.page) LIKE '.$key;
			
			$query->where('('.implode(' OR ', $where).')');
       	}
        if (strlen($page)) 
        {
        	$key	= $this->_db->Quote('%'.$this->_db->getEscaped( trim( strtolower( $page ) ) ).'%');
          	$query->where('tbl.page LIKE '.$key);
       	}
        if (strlen($filter_id_from))
        {
            if (strlen($filter_id_to))
            {
                $query->where('tbl.id >= '.(int) $filter_id_from);
            }
            else
            {
                $query->where('tbl.id = '.(int) $filter_id_from);
            }
        }
        if (strlen($filter_id_to))
        {
            $query->where('tbl.id <= '.(int) $filter_id_to);
        }
        if (strlen($filter_date_from))
        {
            $query->where("tbl.entered >= '".$filter_date_from."'");
        }
        if (strlen($filter_date_to))
        {
            $query->where("tbl.entered <= '".$filter_date_to."'");
        }
    }

    protected function _buildQueryGroup(&$query)
    {
        if (strlen($select_group = $this->getState('select_group')))
        {
            $query->group($select_group);	
        }
    }
}

?>