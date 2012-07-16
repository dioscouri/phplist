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

class PhplistModelUnsubscribe extends PhplistModelBase 
{
	/**
	 * This model's default table is the listuser table
	 * @return unknown_type
	 */
    function getTable()
    {
        JTable::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_phplist'.DS.'tables' );
        $table = JTable::getInstance( 'Subscriptions', 'Table' );
        return $table;
    }
    
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
        JLoader::import( 'com_phplist.helpers.user', JPATH_ADMINISTRATOR.DS.'components' );
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
		
		JLoader::import( 'com_phplist.helpers.newsletter', JPATH_ADMINISTRATOR.DS.'components' );
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
			$item->link = PhplistUrl::appendURL('index.php?option=com_phplist&amp;view=messages&amp;task=list&amp;id='.$item->listid);
		}
		return $list;
	}
}

?>