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

class PhplistModelMessages extends PhplistModelBase
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
       	$messagestate = $this->getState('filter_messagestate');
        $filter_id_from = $this->getState('filter_id_from');
        $filter_id_to   = $this->getState('filter_id_to');
        $filter_subject    = $this->getState('filter_subject');
        $filter_date_from   = $this->getState('filter_date_from');
        $filter_date_to     = $this->getState('filter_date_to');
        $filter_datetime   = $this->getState('filter_datetime');

       	if ($filter) 
       	{
			$key	= $this->_db->Quote('%'.$this->_db->getEscaped( trim( strtolower( $filter ) ) ).'%');
			$where = array();
			$where[] = 'LOWER(tbl.id) LIKE '.$key;
			$where[] = 'LOWER(tbl.subject) LIKE '.$key;
			$where[] = 'LOWER(tbl.message) LIKE '.$key;
			$where[] = 'LOWER(tbl.textmessage) LIKE '.$key;
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
       	if ( $listid >= '0' )
       	{
       		$query->where('m2n.listid = '.$listid);
       	} 
       		elseif ( $listid == '-1' ) 
       	{
       		$query->where('m2n.listid IS NULL');
       	}
       	
       	if (strlen($messagestate))
       	{
       		$key	= $this->_db->Quote('%'.$this->_db->getEscaped( trim( strtolower( $messagestate ) ) ).'%');
       		$query->where('tbl.status LIKE '.$key);
       	}
        if (strlen($filter_subject))
        {
            $key    = $this->_db->Quote('%'.$this->_db->getEscaped( trim( strtolower( $filter_subject ) ) ).'%');
            $where = array();
            $where[] = 'LOWER(tbl.subject) LIKE '.$key;
            $query->where('('.implode(' OR ', $where).')');
        }
        if (strlen($filter_date_from))
        {
            $query->where("m2n.entered >= '".$filter_date_from."'");
        }
        if (strlen($filter_date_to))
        {
            $query->where("m2n.entered <= '".$filter_date_to."'");
        }
        if (strlen($filter_datetime))
        {
        	$query->where("tbl.entered = '".$filter_date_to."'");
        }
       	
    }
    
	protected function _buildQueryJoins(&$query)
	{
		parent::_buildQueryJoins($query);
        JLoader::import( 'com_phplist.helpers.newsletter', JPATH_ADMINISTRATOR.DS.'components' );
        $listid_tablename = PhplistHelperNewsletter::getTableNameListmessage();
        $query->join('LEFT', "{$listid_tablename} AS m2n ON tbl.id = m2n.messageid");
	}
	
    protected function _buildQueryGroup(&$query)
    {
        $query->group('tbl.id');
    }
    
	protected function _buildQueryFields(&$query)
	{
		parent::_buildQueryFields($query);
		
		$field = array();
		$field[] = " m2n.listid AS listid ";
		
		$query->select( $this->getState( 'select', 'tbl.*' ) );		
		$query->select( $field );		
	}
    
	public function getList($refresh = false)
	{	
		$list = parent::getList($refresh);
		if(empty($list)) { return array();}
		 
		foreach(@$list as $item)
		{
			$item->link = 'index.php?option=com_phplist&controller=messages&view=messages&task=edit&id='.$item->id;
			$item->link_view = PhplistHelperUrl::appendURL("index.php?option=com_phplist&view=messages&task=view&id=".$item->id."&newsletterid=".$item->listid);
			// get status link and link text
			if (strtolower($item->status) != 'submitted' && strtolower($item->status) != 'inprocess')
			{
				$item->link_status = 'index.php?option=com_phplist&controller=messages&task=addtoqueue&cid[]='.$item->id;
				$item->link_status_text = JText::_('ADD TO QUEUE');
			}

			if (strtolower($item->status) == 'submitted' || strtolower($item->status) == 'inprocess') {
				$item->link_status = 'index.php?option=com_phplist&controller=messages&task=suspend&cid[]='.$item->id;
				$item->link_status_text = JText::_('SUSPEND SENDING');						
			}
			
			// get message data
			$messagedata = PhplistHelperMessage::getData( $item->id );
			$item->timetosend = PhplistHelperMessage::timeToSend( $item->sendstart, $item->sent );
			$item->sent_processed = number_format( $item->processed, 0, '', ',');
			$item->sent_text = number_format( $item->astext, 0, '', ',');
			$item->sent_html = number_format( $item->ashtml + $item->astextandhtml, 0, '', ',');
			$item->sent_pdf = number_format( $item->aspdf + $item->astextandpdf, 0, '', ',');
			$item->sent_total = number_format( $item->astext + $item->ashtml + $item->astextandhtml + $item->aspdf + $item->astextandpdf, 0, '', ',');
			$item->to_process = number_format( @$messagedata['to process'], 0, '', ',');
			$item->eta = @$messagedata['ETA'];
			$item->mph = @$messagedata['msg/hr'];
			
			// get lists
			$thisItem_list = "";
			$newsletters = PhplistHelperMessage::getNewsletters( $item->id );
			for ($r=0; $r<count($newsletters); $r++)
			{
				$thisItem = $newsletters[$r];
				$title = $thisItem->name ? $thisItem->name : JText::_( 'Unnamed' )." ".$thisItem->id;
				$thisItem_list .= $title.'<br/>';
			}
			$item->newsletters = $thisItem_list;
		}
		return $list;
	}	
}
?>