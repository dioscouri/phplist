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

class PhplistModelNewsletters extends PhplistModelBase
{

	function __construct($config = array())
	{
		parent::__construct($config);
		$database = PhplistHelperPhplist::setPhplistDatabase();
	}
	
	protected function _buildQueryWhere(&$query)
    {
       	$filter     = $this->getState('filter');
       	$enabled 	= $this->getState('filter_active');
        $filter_id_from = $this->getState('filter_id_from');
        $filter_id_to   = $this->getState('filter_id_to');
        $filter_name    = $this->getState('filter_name');

       	if ($filter) 
       	{
			$key	= $this->_db->Quote('%'.$this->_db->getEscaped( trim( strtolower( $filter ) ) ).'%');

			$where = array();
			$where[] = 'LOWER(tbl.id) LIKE '.$key;
			$where[] = 'LOWER(tbl.name) LIKE '.$key;
			$where[] = 'LOWER(tbl.description) LIKE '.$key;
			
			$query->where('('.implode(' OR ', $where).')');
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
       	if (strlen($enabled))
       	{
       		$query->where('tbl.active = '.$enabled);
       	}
        if (strlen($filter_name))
        {
            $key    = $this->_db->Quote('%'.$this->_db->getEscaped( trim( strtolower( $filter_name ) ) ).'%');
            $where = array();
            $where[] = 'LOWER(tbl.name) LIKE '.$key;
            $query->where('('.implode(' OR ', $where).')');
        }
       	
    }

    protected function _buildQueryFields(&$query)
    {
        $field = array();

        // load the date of the last sent message for the newsletter
        $tablename_lettermsg = PhplistHelperNewsletter::getTableNameListmessage();
        $tablename_msg = PhplistHelperMessage::getTableName();
        
        $field[] = "
            (
            SELECT
                msg.sendstart
            FROM
                $tablename_msg AS msg
            LEFT JOIN 
                $tablename_lettermsg AS lettermsg ON msg.id = lettermsg.messageid
            WHERE
                lettermsg.listid = tbl.id
            AND
                msg.status = 'sent'
            ORDER BY
                msg.sendstart DESC
            LIMIT 1
            ) 
        AS lastsent
        ";
        
        $query->select( $this->getState( 'select', 'tbl.*' ) );     
        $query->select( $field );
    }
    
	public function getList($refresh = false)
	{
		$list = parent::getList($refresh);
		if(empty($list)) { return array(); }
		
		foreach(@$list as $item)
		{
			$item->link = 'index.php?option=com_phplist&controller=newsletters&view=newsletters&task=edit&id='.$item->id;
			$item->link_subscribers = 'index.php?option=com_phplist&view=subscriptions&filter_listid='.$item->id;
			$item->link_messages = 'index.php?option=com_phplist&amp;view=messages&amp;task=list&amp;id='.$item->id;
		    $item->link_switch = "index.php?option=com_phplist&controller=newsletters&task=switch_subscription&cid[]={$item->id}";
			
			// get last mailing
            unset($lastMailing);
            $item->lastMailingDate = JText::_( "NO_MESSAGES_SENT" );
            if ($lastMailing = PhplistHelperNewsletter::getLastMailing( $item->id)) 
            {
                $item->lastMailingDate = JHTML::_( "date", $lastMailing->sendstart, JText::_('DATE_FORMAT_LC2') );
            }
		}
		return $list;
	}
}

?>