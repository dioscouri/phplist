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

Phplist::load( 'PhplistModelSubscriptions', 'models.subscriptions' );

class PhplistModelUnsubscribe extends PhplistModelSubscriptions
{
	function getTable($name='', $prefix='PhplistTable', $options = array())
	{
		// default table for this model is not Unsubscribe, but rather Subscriptions
		if (empty($name))
		{
			$name = 'Subscriptions';
		}
	
		if($table = &$this->_createTable( $name, $prefix, $options ))  {
			return $table;
		}
	
		JError::raiseError( 0, 'Table ' . $prefix . $name . ' not supported. File not found.' );
		$null = null;
		return $null;
	}
	
	protected function _buildQueryWhere(&$query)
    {				
        $user = JFactory::getUser();
        $phplistUser = null;
        if ($user->id > '0') 
        {
            $phplistUser = PhplistHelperUser::getUser( $user->id, '1', 'foreignkey' );
            if (!isset($phplistUser->id)) 
            {
                $phplistUser = PhplistHelperUser::create( $user );  
            }
        }
        elseif ($uid =  JRequest::getVar( 'uid' ))
        {
        	$phplistUser = PhplistHelperUser::getUser( $uid, '1', 'uid' );
        }
		
		$query->where('tbl.userid = ' .$phplistUser->id);
		
		//only allow users to unsubcribe from published newsletters
		$query->where('newsletter.active = 1');
    }
    
	protected function _buildQueryJoins(&$query)
	{
		parent::_buildQueryJoins($query);
		
		$newsletter_tablename = PhplistHelperNewsletter::getTableName();
		$query->join('LEFT', "{$newsletter_tablename} AS newsletter ON newsletter.id = tbl.listid");
	}
	
	protected function _buildQueryFields(&$query)
	{
		parent::_buildQueryFields($query);
		
		$field = array();
		$field[] = " newsletter.name AS newsletter_name ";
		$field[] = " newsletter.description AS newsletter_desc ";
		$field[] = " tbl.listid AS listid";
		
		$query->select( $this->getState( 'select', 'tbl.*' ) );		
		$query->select( $field );		
	}	
        
	public function getList()
	{
		$list = parent::getList();
		foreach(@$list as $item)
		{
			$item->link = JRoute::_('index.php?option=com_phplist&amp;view=messages&amp;task=list&amp;id='.$item->listid, false);
		}
		return $list;
	}
}

?>