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

Phplist::load( 'SynkHelperBase', 'helpers.base');

/**
 * @package	
 */
class PhplistHelperMessage  extends PhplistHelperBase
{
	/**
	 * Returns the phphlist message table name
	 * @return unknown_type
	 */
	function getTableName() 
	{
		$success = false;
		$phplist_prefix = PhplistHelperPhplist::getPrefix();
		$success = "{$phplist_prefix}_message";
		return $success;
	}
	
	/**
	 * Returns the phphlist message table name
	 * @return unknown_type
	 */
	function getTableNameData() 
	{
		$success = false;
		$phplist_prefix = PhplistHelperPhplist::getPrefix();
		$success = "{$phplist_prefix}_messagedata";
		return $success;
	}
	
	/**
	 * get message status
	 */
	function getStates()
	{
		$return = array();

			$row = new JObject();
			$row->id = 'draft';
			$row->title = "Draft";
		$return[] = $row;
			
			$row = new JObject();
			$row->id = 'submitted';
			$row->title = "Queued";
		$return[] = $row;
		
			$row = new JObject();
			$row->id = 'suspended';
			$row->title = "Suspended";
		$return[] = $row;
		
			$row = new JObject();
			$row->id = 'sent';
			$row->title = "Sent";
		$return[] = $row;
		
		return $return;
	}

	/**
	 * 
	 * @param $id
	 * @return unknown_type
	 */
	function getNewsletters( $id )
	{
		JLoader::import( 'com_phplist.helpers.newsletter', JPATH_ADMINISTRATOR.DS.'components' ); 
		$success = false;
		$database = PhplistHelperPhplist::getDBO();
		$tablename = PhplistHelperNewsletter::getTableNameMessage();
		$tablename_newsletters = PhplistHelperNewsletter::getTableName();
		
		// TODO Fix this query
		$query = "
			SELECT
				{$tablename_newsletters}.*
			FROM
				{$tablename_newsletters}
				LEFT JOIN {$tablename} ON {$tablename}.listid = {$tablename_newsletters}.id  
			WHERE
				{$tablename}.messageid = '{$id}'
		";
		$database->setQuery( $query );
		$data = $database->loadObjectList();
		$success = $data;

		return $success;
	}
	
	/**
	 * Finds out whether a message has specific data
	 * Returns boolean unless $returnObject is true, then returns db object
	 * 
	 * @param int		the message id
	 * @param int		the data name
	 * @param boolean	whether to return an object or not
	 * @return unknown_type
	 */
	function isNewsletter( $id, $newsletterid, $returnObject='0' )
	{
		JLoader::import( 'com_phplist.helpers.newsletter', JPATH_ADMINISTRATOR.DS.'components' );
		$success = false;
		$database = PhplistHelperPhplist::getDBO();
		$tablename = PhplistHelperNewsletter::getTableNameMessage();
		
		// TODO Fix this query
		$query = "
			SELECT
				*
			FROM
				$tablename
			WHERE
				`messageid` = '{$id}'
			AND
				`listid` = '{$newsletterid}'
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
	 * 
	 * @param object	$details->userid, $details->typeid
	 * @return boolean
	 */
	function addToNewsletter( $id, $newsletterid )
	{
		$success = false;
		JLoader::import( 'com_phplist.helpers.newsletter', JPATH_ADMINISTRATOR.DS.'components' ); 
		
		$database = PhplistHelperPhplist::getDBO();
		$tablename = PhplistHelperNewsletter::getTableNameMessage();
		
		$config =& JFactory::getConfig();
		$date = JFactory::getDate();
		$date->setOffset($config->getValue('config.offset'));
		$datetime = $date->toMySQL(true);
		
		$query = "
			INSERT INTO
				{$tablename}
			SET
				`messageid` = '{$id}',
				`listid` = '{$newsletterid}',
				`entered` = '{$datetime}'
		";
		$database->setQuery( $query );
		$success = $database->query();
		
		return $success;
	}

	/**
	 * 
	 * @param object	$details->userid, $details->typeid
	 * @return boolean
	 */
	function removeFromNewsletter( $id, $newsletterid )
	{
		$success = false;
		JLoader::import( 'com_phplist.helpers.newsletter', JPATH_ADMINISTRATOR.DS.'components' ); 
		
		$database = PhplistHelperPhplist::getDBO();
		$tablename = PhplistHelperNewsletter::getTableNameMessage();
				
		$query = "
			DELETE FROM
				{$tablename}
			WHERE
				`messageid` = '{$id}'
			AND
				`listid` = '{$newsletterid}'
			LIMIT 1			
		";
		$database->setQuery( $query );
		$success = $database->query();
		
		return $success;
	}

	/**
	 * Gets a messages specific data
	 * 
	 * @param int		the message id
	 * @return unknown_type
	 */
	function getData( $id )
	{
		$success = false;
		$database = PhplistHelperPhplist::getDBO();
		$tablename = PhplistHelperMessage::getTableNameData();
		
		$query = "
			SELECT
				*
			FROM
				$tablename
			WHERE
				`id` = '{$id}'
		";
		$database->setQuery( $query );
		$data = $database->loadObjectList();
		
		$success = array();
		for ($i=0; $i<count($data); $i++)
		{
			$d = $data[$i];
			$name = $d->name;
			$messagedata = $d->data;
			$success["$name"] = $messagedata;
		}
		
		return $success;
	}
	
	/**
	 * 
	 * @param $id
	 * @param $name
	 * @param $data
	 * @return unknown_type
	 */
	function storeData( $id, $name, $data )
	{
		$success = false;
		
		if (!is_numeric($id) || empty($name) )
		{
			return $success;
		}
		
		if ($is = PhplistHelperMessage::isData( $id, $name ))
		{
			$success = PhplistHelperMessage::updateData( $id, $name, $data );
		}
		else
		{
			$success = PhplistHelperMessage::insertData( $id, $name, $data );
		}
		
		return $success;
	}

	/**
	 * Finds out whether a message has specific data
	 * Returns boolean unless $returnObject is true, then returns db object
	 * 
	 * @param int		the message id
	 * @param int		the data name
	 * @param boolean	whether to return an object or not
	 * @return unknown_type
	 */
	function isData( $id, $name, $returnObject='0' )
	{
		$success = false;
		$database = PhplistHelperPhplist::getDBO();
		$tablename = PhplistHelperMessage::getTableNameData();
		
		// TODO Fix this query
		$query = "
			SELECT
				*
			FROM
				$tablename
			WHERE
				`id` = '{$id}'
			AND
				`name` = '{$name}'
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
	 * 
	 * @param object	$details->userid, $details->typeid
	 * @return boolean
	 */
	function insertData( $id, $name, $data )
	{
		$success = false;
		
		$database = PhplistHelperPhplist::getDBO();
		$tablename = PhplistHelperMessage::getTableNameData();
		$id = intval($id);
		$name = $database->getEscaped( $name );
		$data = $database->getEscaped( $data );
		
		$query = "
			INSERT INTO
				$tablename
			SET
				`id` = '{$id}',
				`name` = '{$name}',
				`data` = '{$data}'
		";
		$database->setQuery( $query );
		$success = $database->query();
		
		return $success;
	}

	/**
	 *
	 * @param object	$details->userid, $details->typeid
	 * @return boolean
	 */
	function updateData( $id, $name, $data )
	{
		$success = false;
		
		$database = PhplistHelperPhplist::getDBO();
		$tablename = PhplistHelperMessage::getTableNameData();
		$id = intval($id);
		$name = $database->getEscaped( $name );
		$data = $database->getEscaped( $data );
		
		$query = "
			UPDATE
				$tablename
			SET
				`data` = '{$data}'
			WHERE
				`id` = '{$id}'
			AND
				`name` = '{$name}'
			LIMIT 1			
		";
		$database->setQuery( $query );
		$success = $database->query();
				
		return $success;
	}

	/**
	 * 
	 * @param object	$details->userid, $details->typeid
	 * @return boolean
	 */
	function removeData( $id, $name )
	{
		$success = false;

		$database = PhplistHelperPhplist::getDBO();
		$tablename = PhplistHelperMessage::getTableNameData();
		$id = intval($id);
		$name = $database->getEscaped( $name );
		$data = $database->getEscaped( $data );
		
		$query = "
			DELETE FROM
				$tablename
			WHERE
				`id` = '{$id}'
			AND
				`name` = '{$name}'
			LIMIT 1			
		";
		$database->setQuery( $query );
		$success = $database->query();
		
		return $success;
	}
	
	/**
	 * Calculates and formats the diff between two times
	 * @param $time1
	 * @param $time2
	 * @return unknown_type
	 */
	function timeToSend( $time1, $time2 )
	{
		if (!$time1 || !$time2) {
			$return = JText::_( "UNKNOWN" );
			return $return;
		}
		
		$t1 = strtotime($time1);
		$t2 = strtotime($time2);

		if ($t1 < $t2) {
			$diff = $t2 - $t1;
		} else {
			$diff = $t1 - $t2;
		}
		
		if ($diff == 0)
		{
			$return = JText::_( "VERY LITTLE TIME" );
			return $return;
		}
    
		$hours = (int)($diff / 3600);
		$mins = (int)(($diff - ($hours * 3600)) / 60);
		$secs = (int)($diff - $hours * 3600 - $mins * 60);

		$return = '';
		if ($hours) 
		{
			$return = $hours." ".JText::_("HOURS"); 
		}
		if ($mins)
		{
			$return .= " ".$mins." ".JText::_("MINUTES");
		}
		if ($secs)
		{
			$return .= " ".$secs." ".JText::_("SECONDS");
		}
		
		return $return;
	}
	
	/**
	 * Gets the default message footer from phplist config
	 * 
	 * @param int		the message id
	 * @return unknown_type
	 */
	function getDefaultFooter()
	{
		$success = false;
		$database = PhplistHelperPhplist::getDBO();
		$phplist_prefix = PhplistHelperPhplist::getPrefix();
		$tablename = "{$phplist_prefix}_config";
		
		$query = "
			SELECT
				*
			FROM
				$tablename
			WHERE
				`item` = 'messagefooter'
		";
		$database->setQuery( $query );
		$success = $database->loadObject();
		
		return $success;
	}
	
	/**
	 * Remove anything within square brackets from message text
	 * 
	 * @param 
	 * @return unknown_type
	 */
	function stripPlaceholders($message)
	{
		$message = preg_replace('(\\[.*?\\])','' , $message);
		return $message;
	}
}

?>
