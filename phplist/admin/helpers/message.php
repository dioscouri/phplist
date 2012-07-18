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
		$success = false;
		$database = PhplistHelperPhplist::getDBO();
		$tablename_listmessage = PhplistHelperNewsletter::getTableNameListmessage();
		$tablename_newsletters = PhplistHelperNewsletter::getTableName();
		Phplist::load( 'PhplistQuery', 'library.query' );
		JTable::addIncludePath( JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_phplist' . DS . 'tables' );
		
		$query = new PhplistQuery( );
		$query->select( "*" );
		$query->from( $tablename_newsletters . " AS tbl" );		
		$query->join( 'LEFT', $tablename_listmessage.' as listmsg ON listmsg.listid = tbl.id' );
		$query->where( 'listmsg.messageid = '.$id );
		$database->setQuery( ( string ) $query );
		$data = $database->loadObjectList();
		$success = $data;
		return $success;
	}
	
	/**
	 * Gets data from message table
	 * @param $id
	 * @return unknown_type
	 */
	function getMessage( $id )
	{
		JModel::addIncludePath( JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_phplist' . DS . 'models' );
		$model = JModel::getInstance( 'Messages', 'PhplistModel' );
		$model->setId($id);
		$items = $model->getItem();
		return $items;
	}
	
	/**
	 * Finds out whether a message is on a newsletter list
	 * Returns boolean unless $returnObject is true, then returns db object
	 * 
	 * @param int		the message id
	 * @param int		the data name
	 * @param boolean	whether to return an object or not
	 * @return unknown_type
	 */
	function isNewsletter( $id, $newsletterid )
	{
		$success = false;
		$database = PhplistHelperPhplist::getDBO();
		$tablename_listmessage = PhplistHelperNewsletter::getTableNameListmessage();
		Phplist::load( 'PhplistQuery', 'library.query' );
		JTable::addIncludePath( JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_phplist' . DS . 'tables' );
		
		$query = new PhplistQuery( );
		$query->select( "*" );
		$query->from( $tablename_listmessage . " AS tbl" );		
		$query->where( 'tbl.messageid = '.$id );
		$query->where( 'tbl.listid = '.$newsletterid );
		$database->setQuery( ( string ) $query );
		$data = $database->loadObject();
		$success = $data;
		return $success;
	}

	/**
	 * 
	 * @param object
	 * @return boolean
	 */
	function addToNewsletter( $id, $newsletterid )
	{
		$success = false;
		$database = PhplistHelperPhplist::getDBO();
		$tablename_listmessage = PhplistHelperNewsletter::getTableNameListmessage();
		Phplist::load( 'PhplistQuery', 'library.query' );
		JTable::addIncludePath( JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_phplist' . DS . 'tables' );
		
		$config =& JFactory::getConfig();
		$date = JFactory::getDate();
		$date->setOffset($config->getValue('config.offset'));
		$datetime = $date->toMySQL(true);
		
		$query = new PhplistQuery( );
		$query->insert($tablename_listmessage);
		$query->set( "messageid = ".$id );
		$query->set( "listid = ".$newsletterid );
		$query->set( "entered = '".$datetime."'" );
		
		$database->setQuery( (string) $query );
		$success = true;
		if ( !$database->query() )
		{
			$this->setError( $database->getErrorMsg( ) );
			$success = false;
		}
		return $success;
	}

	/**
	 * 
	 * @param object
	 * @return boolean
	 */
	function removeFromNewsletter( $id, $newsletterid )
	{
		$success = false;
		$database = PhplistHelperPhplist::getDBO();
		$tablename_listmessage = PhplistHelperNewsletter::getTableNameListmessage();
		Phplist::load( 'PhplistQuery', 'library.query' );
		JTable::addIncludePath( JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_phplist' . DS . 'tables' );
		
		$query = new PhplistQuery( );
		$query->delete();
		$query->from( $tablename_listmessage );
		$query->where( "messageid = ".$id );
		$query->where( "listid = ".$newsletterid );
		
		$database->setQuery( (string) $query );
		$success = true;
		if ( !$database->query() )
		{
			$this->setError( $database->getErrorMsg( ) );
			$success = false;
		}
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
		$tablename_data = PhplistHelperMessage::getTableNameData();
		Phplist::load( 'PhplistQuery', 'library.query' );
		JTable::addIncludePath( JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_phplist' . DS . 'tables' );
		
		$query = new PhplistQuery( );
		$query->select( "*" );
		$query->from( $tablename_data);		
		$query->where( 'id = '.$id );
		$database->setQuery( ( string ) $query );
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
		$phplistconfig = &PhplistConfigPhplist::getInstance();
		$success = $phplistconfig->get('messagefooter', '');
		
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
	
	/**
	 * Change all relative URLs and image links to Absolute, so they work from the emails.
	 * 
	 * @param 
	 * @return unknown_type
	 */
	
	function _relToAbs($text)
	{
		global $mainframe;
		$base = $mainframe->getSiteURL();
		$text = preg_replace("/(href|src)=\"(?!http|ftp|https|mailto)([^\"]*)\"/", "$1=\"$base\$2\"", $text);
		return $text;
	}
}

?>
