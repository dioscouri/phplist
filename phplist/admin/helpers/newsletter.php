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
	function getTableNameMessage() 
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
	function getLastMailing( $newsletterid, $returnObject='0' )
	{
        JLoader::import( 'com_phplist.helpers.message', JPATH_ADMINISTRATOR.DS.'components' );
		$success = false;
		$database = PhplistHelperPhplist::getDBO();
		$tablename_letter = PhplistHelperNewsletter::getTableName();
		$tablename_lettermsg = PhplistHelperNewsletter::getTableNameMessage();
		$tablename_msg = PhplistHelperMessage::getTableName();
		
		$query = "
			SELECT
				msg.*
			FROM
				$tablename_msg AS msg
			LEFT JOIN 
				$tablename_lettermsg AS lettermsg ON msg.id = lettermsg.messageid
			WHERE
				lettermsg.listid = '{$newsletterid}'
			AND
				msg.status = 'sent'
			ORDER BY
				msg.sendstart DESC
			LIMIT 1
		";
		$database->setQuery( $query );
		$data = $database->loadObject();
		if ($data)
		{
			$success = true;
			if ($returnObject == '1')
			{
				$success = $data;
			}
		}
		
		return $success;
	}
	/**
	 * Returns a list of types
	 * @param mixed Boolean
	 * @param mixed Boolean
	 * @return array
	 */
	function &getTypes( $published='0' )
	{
		JLoader::import( 'com_phplist.helpers.newsletter', JPATH_ADMINISTRATOR.DS.'components' );
		
		$database = PhplistHelperPhplist::getDBO();
		$tablename = PhplistHelperNewsletter::getTableName();
		$where = "";
		if ($published  == '1')
		{
			$where .= ' WHERE tbl.active = 1';
		}
		
		$query = "
			SELECT
				tbl.*
			FROM
				{$tablename} AS tbl
				{$where}
			ORDER BY
				tbl.listorder ASC
		";

		$database->setQuery( $query );
		$data = $database->loadObjectList();
		
		return $data;
	}
	/**
	 * Returns a newsletter Name
	 * @param mixed Boolean
	 * @param mixed Boolean
	 * @return array
	 */
	function getName( $listid )
	{
		JLoader::import( 'com_phplist.helpers.newsletter', JPATH_ADMINISTRATOR.DS.'components' );
		JLoader::import( 'com_phplist.helpers.phplist', JPATH_ADMINISTRATOR.DS.'components' );
		
		$database = PhplistHelperPhplist::getDBO();
		$tablename = PhplistHelperNewsletter::getTableName();
		
		$query = "
			SELECT
				*
			FROM
				{$tablename}
			WHERE
			id = '{$listid}'
		";

		$database->setQuery( $query );
		$data = $database->loadObject();
		
		return $data;
	}
	
	/**
	 * Returns number of subscribers for a newsletter
	 * @param mixed Boolean
	 * @param mixed Boolean
	 * @return array
	 */
	function getNumSubscribers( $listid )
	{
		JLoader::import( 'com_phplist.helpers.subscription', JPATH_ADMINISTRATOR.DS.'components' );
		JLoader::import( 'com_phplist.helpers.phplist', JPATH_ADMINISTRATOR.DS.'components' );
		
		$database = PhplistHelperPhplist::getDBO();
		$tablename = PhplistHelperSubscription::getTableName();
		
		$query = "
			SELECT 
                COUNT(userid)
            FROM
				{$tablename} AS subscriptions 
            WHERE 
                subscriptions.listid = {$listid}
		";

		$database->setQuery( $query );
		$count = $database->loadResult();
		
		return $count;
	}
}

?>
