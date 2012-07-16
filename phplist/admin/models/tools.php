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

class PhplistModelTools extends PhplistModelBase 
{	
    protected function _buildQueryWhere(&$query)
    {
       	$filter     = $this->getState('filter');

       	if ($filter) 
       	{
			$key	= $this->_db->Quote('%'.$this->_db->getEscaped( trim( strtolower( $filter ) ) ).'%');

			$where = array();
			$where[] = 'LOWER(tbl.id) LIKE '.$key;
			$where[] = 'LOWER(tbl.name) LIKE '.$key;
			$where[] = 'LOWER(tbl.element) LIKE '.$key;
			
			$query->where('('.implode(' OR ', $where).')');
       	}
       	
		$query->where("LOWER(tbl.folder) = 'phplist'");
    }
    	
	public function getList()
	{
		$list = parent::getList(); 
		foreach($list as $item)
		{
			$item->link = 'index.php?option=com_phplist&controller=tools&view=tools&task=view&id='.$item->id;
		}
		return $list;
	}
}

?>