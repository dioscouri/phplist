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

class PhplistModelSubscriptions extends PhplistModelBase
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
	
	protected function _buildQueryWhere(&$query)
    {
       	$filter     = $this->getState('filter');
       	$listid 	= $this->getState('filter_listid');
       	$confirmed 	= $this->getState('filter_confirmed');
        $filter_id_from = $this->getState('filter_id_from');
        $filter_id_to   = $this->getState('filter_id_to');
        $filter_joomlaid_from = $this->getState('filter_joomlaid_from');
        $filter_joomlaid_to   = $this->getState('filter_joomlaid_to');
        $filter_email    = $this->getState('filter_email');
        $filter_date_from   = $this->getState('filter_date_from');
        $filter_date_to     = $this->getState('filter_date_to');

       	if ($filter) 
       	{
			$key	= $this->_db->Quote('%'.$this->_db->getEscaped( trim( strtolower( $filter ) ) ).'%');

			$where = array();
			$where[] = 'LOWER(user.foreignkey) LIKE '.$key;
			$where[] = 'LOWER(user.email) LIKE '.$key;
			
			$query->where('('.implode(' OR ', $where).')');
       	}
        if (strlen($filter_joomlaid_from))
        {
            if (strlen($filter_joomlaid_to))
            {
                $query->where('user.foreignkey >= '.(int) $filter_joomlaid_from);
            }
            else
            {
                $query->where('user.foreignkey = '.(int) $filter_joomlaid_from);
            }
        }
        if (strlen($filter_joomlaid_to))
        {
            $query->where('user.foreignkey <= '.(int) $filter_joomlaid_to);
        }

 	   if (strlen($filter_id_from))
        {
            if (strlen($filter_id_to))
            {
                $query->where('user.id >= '.(int) $filter_id_from);
            }
            else
            {
                $query->where('user.id = '.(int) $filter_id_from);
            }
        }
        if (strlen($filter_id_to))
        {
            $query->where('user.id <= '.(int) $filter_id_to);
        }
       	if ( $listid >= '0' )
       	{
       		$query->where('tbl.listid = '.$listid);
       	} 
        elseif ( $listid == '-1' ) 
       	{
       		$query->where('tbl.listid IS NULL');
       	}
		
    	if ( $confirmed == '1' )
		{
			$query->where("user.confirmed = '1'");
		}
		elseif (strlen($confirmed) && $confirmed == '0')
		{
			$query->where("(user.confirmed IS NULL OR user.confirmed = '' OR user.confirmed = '0')");
		}
        if (strlen($filter_email))
        {
            $key    = $this->_db->Quote('%'.$this->_db->getEscaped( trim( strtolower( $filter_email ) ) ).'%');
            $where = array();
            $where[] = 'LOWER(user.email) LIKE '.$key;
            $query->where('('.implode(' OR ', $where).')');
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
    
	protected function _buildQueryJoins(&$query)
	{
		parent::_buildQueryJoins($query);
		
		JLoader::import( 'com_phplist.helpers.user', JPATH_ADMINISTRATOR.DS.'components' );
		$user_tablename = PhplistHelperUser::getTableName();
		$query->join('LEFT', "{$user_tablename} AS user ON user.id = tbl.userid");
		
		JLoader::import( 'com_phplist.helpers.newsletter', JPATH_ADMINISTRATOR.DS.'components' );
		$newsletter_tablename = PhplistHelperNewsletter::getTableName();
		$query->join('LEFT', "{$newsletter_tablename} AS newsletter ON newsletter.id = tbl.listid");
	}
	
	protected function _buildQueryFields(&$query)
	{
		parent::_buildQueryFields($query);
		
		$field = array();
		$field[] = " newsletter.name AS newsletter_name ";
		$field[] = " user.email AS user_email ";
		$field[] = " user.entered AS user_entered ";
		$field[] = " user.confirmed AS user_confirmed ";
		$field[] = " user.foreignkey AS foreignkey ";
		$field[] = " user.id AS user_id ";
		
		$query->select( $this->getState( 'select', 'tbl.*' ) );		
		$query->select( $field );		
	}
	
	public function getList($refresh = false)
	{
		$list = parent::getList($refresh);
		if(empty($list)) { return array(); }
		
		foreach(@$list as $item)
		{
			$item->link = 'index.php?option=com_phplist&controller=subscriptions&task=unsubscribe&listid='.$item->listid.'&phplistuserid='.$item->userid;
			$item->edit_link = 'index.php?option=com_phplist&controller=users&view=users&task=edit&id=' . $item->user_id;
			$item->veiw_link = 'index.php?option=com_phplist&controller=users&view=users&filter_email=' . $item->user_email;
			if ($item->foreignkey == '' || $item->foreignkey == 'NULL') 
			{
				$item->foreignkey = '---';
			}
		}
		return $list;
	}
}

?>