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

class PhplistHelperPhplist extends PhplistHelperBase 
{	
	/**
	 * Just a wrapper for getDatabase()
	 * @return unknown_type
	 */
	function getDBO()
	{
		$database = PhplistHelperPhplist::getDatabase();
		return $database;
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
	 * Returns the phplist database object
	 * @return unknown_type
	 */
	function getDatabase( $refresh='' ) 
	{
		$success = false;
		static $instance;
		
		if (!is_object($instance) || isset($instance->error) || isset($instance->_errorNum) || $refresh == '1' ) {

			// check that config is complete
			$config = &Phplist::getInstance();
			// need host, user, password, database, and prefix to be complete
			$host		= $config->get( 'phplist_host', '' );
			$database	= $config->get( 'phplist_database', '' );
			$user		= $config->get( 'phplist_user', '' );
			$password	= $config->get( 'phplist_password', '' );			
			$prefix		= $config->get( 'phplist_prefix', '' );
			$user_prefix = $config->get( 'phplist_user_prefix', '' );
			$driver		= $config->get( 'phplist_driver', 'mysql' );
			$port		= $config->get( 'phplist_port', '3600' );

			if (!$host || !$database || !$user || !$password || !$prefix || !$user_prefix) 
			{
				$instance = new stdClass();
				$instance->error = true;
				$instance->_errorNum = '-1';
				$instance->_errorMsg = JText::_( 'The Phplist configuration settings are incomplete' );
				return $instance;
			}
			
			$option = array();
			// create and verify connection
			$option['driver']   = $driver;     // Database driver name
			$option['host']     = $host;    	// Database host name
			if ($port != '3306') 
			{ 
				$option['host'] .= ":".$port;	// alternative ports 
			} 
			$option['user']     = $user;		// User for database authentication
			$option['password'] = $password;	// Password for database authentication
			$option['database'] = $database;	// Database name
			$option['prefix']   = $prefix;     // Database prefix (may be empty)
			
			$newdatabase = & JDatabase::getInstance( $option );
			
			$instance = $newdatabase;
			
			// check that $newdatabase is_object and has method setQuery
			if (!is_object($newdatabase) || !method_exists($newdatabase, 'setQuery'))
			{
				$instance = new stdClass();
				$instance->error = true;
				$instance->_errorNum = '-2';
				$instance->_errorMsg = JText::_( 'Could not create phplist database instance using Joomla DB connector' );
				return $instance;
			}
			
			$newdatabase->setQuery(" SELECT NOW(); ");
			if (!$result = $newdatabase->loadResult()) 
			{
				$instance = new stdClass();
				$instance->error = true;
				$instance->_errorNum = '-3';
				$instance->_errorMsg = JText::_( 'Could not properly query the phplist database' );
			}
			
			//check user table prefix is correct
			$newdatabase->setQuery(" SHOW TABLES LIKE '{$user_prefix}_user'; ");
			if (!$result = $newdatabase->loadResult()) 
			{
				$instance = new stdClass();
				$instance->error = true;
				$instance->_errorNum = '-4';
				$instance->_errorMsg = JText::_( 'Could not find user table' );
			}
			
		}
		
		return $instance;
	}

	/**
	 * Returns the phplist database prefix
	 * @return unknown_type
	 */
	function getPrefix() 
	{
		$success = false;
		$config = &Phplist::getInstance();
		$success = $config->get( 'phplist_prefix', 'phplist' );
		return $success;
	}
	
	/**
	 * Returns the phplist user table prefix
	 * @return unknown_type
	 */
	function getUserTablePrefix() 
	{
		$success = false;
		$config = &Phplist::getInstance();
		$success = $config->get( 'phplist_user_prefix', 'phplist_user' );
		return $success;
	}
	
}

?>
