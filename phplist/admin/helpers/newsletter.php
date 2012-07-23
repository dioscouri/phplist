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

Phplist::load( 'PhplistHelperBase', 'helpers.base');

class PhplistHelperNewsletter extends PhplistHelperBase
{
	/**
	 * Returns the phphlist newsletter table name
	 * @return unknown_type
	 */
	function getTableName() 
	{
		$success = false;
		$phplist_prefix = PhplistHelperPhplist::getPrefix();
		$success = "{$phplist_prefix}_list";
		return $success;
	}
	
	/**
	 * Returns the phphlist message table name
	 * @return unknown_type
	 */
	function getTableNameListmessage() 
	{
		$success = false;
		$phplist_prefix = PhplistHelperPhplist::getPrefix();
		$success = "{$phplist_prefix}_listmessage";
		return $success;
	}
			
	/**
	 * 
	 * @param $newsletterid
	 * @param $returnObject
	 * @return unknown_type
	 */
	function getLastMailing( $newsletterid )
	{
		$success = false;
		$database = PhplistHelperPhplist::getDBO();
		$tablename_newsletters = PhplistHelperNewsletter::getTableName();
		$tablename_listmessages = PhplistHelperNewsletter::getTableNameListmessage();
		$tablename_msg = PhplistHelperMessage::getTableName();
		
		$query = new PhplistQuery( );
		$query->select( "msg.*" );
		$query->from( $tablename_msg . " AS msg" );		
		$query->join( 'LEFT', $tablename_listmessages.' as listmsg ON msg.id = listmsg.messageid' );
		$query->where( 'listmsg.listid = '.$newsletterid );
		$query->where( "msg.status = 'sent'" );
		$query->order( "msg.sendstart DESC LIMIT 1");
		
		$database->setQuery( ( string ) $query );
		$data = $database->loadObject();
		$success = $data;
		return $success;

	}
	/**
	 * Returns a list of newsletters
	 * @param mixed Boolean
	 * @param mixed Boolean
	 * @return array
	 */
	function getNewsletters( $published='0' )
	{
		JModel::addIncludePath( JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_phplist' . DS . 'models' );
		$model = JModel::getInstance( 'Newsletters', 'PhplistModel' );
		if ($published  == '1')
		{
			$model->setState( 'filter_active', $published );
		}
		$model->setState( 'order', 'listorder' );
		$model->setState( 'direction', 'ASC' );
		$items = $model->getList();
		return $items;
	}
	/**
	 * Returns a newsletter Name
	 * @param mixed Boolean
	 * @param mixed Boolean
	 * @return array
	 */
	function getNewsletter( $listid )
	{
		JModel::addIncludePath( JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_phplist' . DS . 'models' );
		$model = JModel::getInstance( 'Newsletters', 'PhplistModel' );
		$model->setId($listid);
		$items = $model->getItem();
		return $items;
	}
	
	/**
	 * Returns number of subscribers for a newsletter
	 * @param mixed Boolean
	 * @param mixed Boolean
	 * @return array
	 */
	function getNumSubscribers( $listid )
	{
		$success = false;
		$database = PhplistHelperPhplist::getDBO();
		$tablename_subs = PhplistHelperSubscription::getTableName();

		$query = new PhplistQuery( );
		$query->select( "COUNT(userid)" );
		$query->from( $tablename_subs);		
		$query->where( 'listid = '.$listid );
		
		$database->setQuery( ( string ) $query );
		$data = $database->loadResult();
		$success = $data;
		return $success;
	}
}

?>
