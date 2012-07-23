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

class PhplistModelUsers extends PhplistModelBase 
{
	function __construct($config = array())
	{
		parent::__construct($config);
		$database = PhplistHelperPhplist::setPhplistDatabase();
	}
	
    protected function _buildQueryWhere(&$query)
    {
       	$filter     			= $this->getState('filter');
        $filter_id_from 		= $this->getState('filter_id_from');
        $filter_id_to  			 = $this->getState('filter_id_to');
        $filter_foreignkey_from = $this->getState('filter_foreignkey_from');
        $filter_foreignkey_to   = $this->getState('filter_foreignkey_to');
		$filter_joomla_user    	= $this->getState('filter_joomla_user');
        $filter_email    		= $this->getState('filter_email');
        $filter_html    		= $this->getState('filter_html');
        $confirmed 				= $this->getState('filter_confirmed');
       	
       	if ($filter) 
       	{
			$key	= $this->_db->Quote('%'.$this->_db->getEscaped( trim( strtolower( $filter ) ) ).'%');

			$where = array();
			$where[] = 'LOWER(tbl.id) LIKE '.$key;
			$where[] = 'LOWER(tbl.email) LIKE '.$key;
			$where[] = 'LOWER(tbl.foreignkey) LIKE '.$key;
			
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
    if (strlen($filter_foreignkey_from))
        {
            if (strlen($filter_foreignkey_to))
            {
                $query->where('tbl.foreignkey >= '.(int) $filter_foreignkey_from);
            }
            else
            {
                $query->where('tbl.foreignkey = '.(int) $filter_foreignkey_from);
            }
        }
        if (strlen($filter_foreignkey_to))
        {
            $query->where('tbl.foreignkey <= '.(int) $filter_foreignkey_to);
        }
        if (strlen($filter_email))
        {
            $key    = $this->_db->Quote('%'.$this->_db->getEscaped( trim( strtolower( $filter_email ) ) ).'%');
            $where = array();
            $where[] = 'LOWER(tbl.email) LIKE '.$key;
            $query->where('('.implode(' OR ', $where).')');
        }
    	if ( $confirmed == '1' )
		{
			$query->where("tbl.confirmed = '1'");
		}
		elseif (strlen($confirmed) && $confirmed == '0')
		{
			$query->where("(tbl.confirmed IS NULL OR tbl.confirmed = '' OR tbl.confirmed = '0')");
		}
   		 if ( $filter_html == '1' )
		{
			$query->where("tbl.htmlemail = '1'");
		}
		elseif (strlen($filter_html) && $filter_html == '0')
		{
			$query->where("(tbl.htmlemail IS NULL OR tbl.htmlemail = '' OR tbl.htmlemail = '0')");
		}
    }
    	
	public function getList($refresh = false)
	{
		
		// Message if Joomla! users not in PHPList database
        // TODO Unfortunately, this causes a memory error for large sites, like Dioscouri, with 65,000+ users
		$missingUsers = PhplistHelperUser::syncJoomlaUsers('0');
		if ($missingUsers != false)
		{
			if ($missingUsers->missingusers != '')
			{
				JError::raiseNotice(JText::_( 'INTEGRATION NOT COMPLETE' ), sprintf(JText::_( "MISSING USERS DESC"), $missingUsers->missingusers));
			}
			if ($missingUsers->nonexistantusers != '')
			{
				JError::raiseNotice(JText::_( 'INTEGRATION NOT COMPLETE'), sprintf(JText::_( "NOT IN SYNC DESC"), $missingUsers->nonexistantusers));
			}
		}	
		
		// get list
		$list = parent::getList($refresh);
		if(empty($list)) { return array(); }
		
		foreach($list as $item)
		{
			$item->link = 'index.php?option=com_phplist&controller=users&view=users&task=edit&id='.$item->id;
			
			// get email type (HTML or Text)
			if ($item->htmlemail == '1')
			{
				$item->html = JText::_( "HTML");
			}
			else
			{
				$item->html = JText::_( "TEXT");
			}
			
			// get list of newsletters subscribed to
			$subscriptions_list = "";
			$subscriptions = array();
			if (isset($item->id))
			{
				$subscriptions = PhplistHelperUser::getSubs($item->id);
				for ($r=0; $r<count($subscriptions); $r++) 
				{
					$sub = $subscriptions[$r];
					$title = $sub->name;
					$subscriptions_list .= $title."<br/>";
				}
				if (count($subscriptions) == 0)
				{
					$subscriptions_list = '<span style="color:red;">';
					$subscriptions_list .= '--' . JText::_('NO SUBSCRIPTIONS') . '--';
					$subscriptions_list .= '</span>';
				}
			}
			$item->subscriptions = $subscriptions_list;
			
			// get Joomla! User info
			$item->joomlaName = '---';
			$item->joomlaUsername = '---';
			if (is_numeric($item->foreignkey)) 
			{
				$item_user = JFactory::getUser($item->foreignkey);
				if ($item_user->id) 
				{
					$item->joomlaName = $item_user->name;
					$item->joomlaUsername = $item_user->username;
				} 
			}
	
			// Get User Attributes
			JLoader::import( 'com_phplist.helpers.attribute', JPATH_ADMINISTRATOR.DS.'components' );
			$attributes_list = "";
			$attributes = array();
			if (isset($item->id))
			{
				$attributes = PhplistHelperAttribute::getUserAttributes($item->id);
				for ($r=0; $r<count($attributes); $r++) 
				{
					$attr = $attributes[$r];
					$linkedAttrib = PhplistHelperAttribute::getAttributeListValues($attr->id);
					if ($linkedAttrib != false)
					{
						//TODO get linked values for checkboxgroup array
						
						foreach ($linkedAttrib as $linkedAttrib)
						{
							if ($linkedAttrib->id == $attr->value)
							{
								$attr->value = $linkedAttrib->name;
							}
						}	
					}
					
					$title = strtoupper($attr->name);
					$value = $attr->value;
					if ($value == NULL)
					{
						$attributes_list .= '';
					} 
					else
					{
						$attributes_list .= "[" .$title."] : <b>" . $value . "</b><br/>";
					}
				}
			}
			$item->attributes_list = $attributes_list;
			
			// add dashes if no Joomla! user ID
			if (!is_numeric($item->foreignkey)) $item->foreignkey = "---";
		}
		return $list;
	}
}

?>