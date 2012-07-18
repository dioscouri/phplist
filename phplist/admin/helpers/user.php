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

class PhplistHelperUser extends PhplistHelperBase
{
		/**
	 * Returns the phphlist users table name
	 * @return unknown_type
	 */
	function getTableName() 
	{
		$success = false;
		$phplist_user_prefix = PhplistHelperPhplist::getUserTablePrefix();
		$success = "{$phplist_user_prefix}_user";
		return $success;
	}
	
	/**
	 * Creates a new PHPList user and Joomla user if auto-create set to yes.
	 * @param $joomlaUserObject
	 * @return unknown_type
	 */
	function create( $joomlaUserObject, $phplistUserDetails='', $UserLogin='false', $jactivation = '0' )
	{
		$success = false;
		$config =& JFactory::getConfig();
		$date = JFactory::getDate();
		$date->setOffset($config->getValue('config.offset'));
		//if the DBO fails, don't do anything
		$database = PhplistHelperPhplist::getDBO();
		if (isset($database->error))
		{
			return $success;
		}
		
		JTable::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_phplist'.DS.'tables' );
		$row = &JTable::getInstance( 'phplistuser', 'Table' );
		$config = &PhplistConfig::getInstance();
			
		// First try to get the user by their email address in case they have a phplistUser account already
		if ($getUser = PhplistHelperUser::getUser( $joomlaUserObject->email, '1', 'email' )) {
			$row->load( $getUser->id );
			$row->foreignkey 	= $joomlaUserObject->id;
		} else {
			$row->foreignkey 	= $joomlaUserObject->id;
			$row->email 		= $joomlaUserObject->email;
			$row->confirmed 	= '1';
			$row->uniqid 		= PhplistHelperUser::getUniqid();
			$row->entered 		= $date->toMySQL(true);
		}
		
		// set HTMLEmail in phplist
		if (isset($phplistUserDetails->htmlemail) && $phplistUserDetails->htmlemail == '0')
		{
			$row->htmlemail = '0';
		} else 
		{
			$row->htmlemail = '1';
		}
		
		$activation_email = $config->get( 'activation_email', '1' );
		if ($activation_email == '1' || $jactivation == '1') 
		{
			$row->confirmed = '0';
			//TODO put sendactivation email here, and remove from newsletters/module functions.
		}
		
		if ($row->store()) {
			$success = $row;
		}

		// Auto Create Joomla! User	
		
		$users_autocreate = $config->get( 'users_autocreate', '0' );
		
		if ($users_autocreate == '1') 
		{
			// first check that jUser doesn't already exist
			if (PhplistHelperUser::emailExists( $joomlaUserObject->email, '0' ) != true)
			{
				jimport('joomla.user.helper');
			
				$username_array = explode( '@', $joomlaUserObject->email );
				$username = @$username_array['0'];
				$details['name'] 		= $joomlaUserObject->email;
				$details['username'] 	= PhplistHelperUser::createNewUsername( $username );
				$details['email'] 		= $joomlaUserObject->email;
				$details['password'] 	= JUserHelper::genRandomPassword();
				$details['password2'] 	= $details['password'];
			
				$newuser = PhplistHelperUser::createNewUser( $details, '0' );
				if ($UserLogin = 'true')
				{
					$login = PhplistHelperUser::login( $details, '1' );
				}
			}
		}	

		return $success;
	}
	
	/**
	 * Returns a truly unique uniqid for the phplis user table
	 * 
	 * @return unknown_type
	 */
	function getUniqid() 
	{
		jimport('joomla.user.helper');
		$isUnique = false;
		
		$database = PhplistHelperPhplist::getDBO();
		if (isset($database->error))
		{
			// RaiseError?
			return $isUnique;
		}
		$tablename = PhplistHelperUser::getTableName();
		
		while (!$isUnique) {
			$id = md5( JUserHelper::genRandomPassword() );
			$query = "
				SELECT
					*
				FROM
					$tablename
				WHERE
					`uniqid` = '{$id}'
				LIMIT 1
			";
			
			$database->setQuery( $query );
			if (!$data = $database->loadObject()) {
				$isUnique = true;
			}		
		}
		
		return $id;
	}
	
	/**
	 * Finds out whether user is subscribed to a specific newsletter
	 * Returns boolean unless $returnObject is true, then returns db object
	 * 
	 * @param int		the user id
	 * @param int		the newsletter id
	 * @param boolean	whether to return an object or not
	 * @return unknown_type
	 */
	function getSubs( $userid )
	{
		$success = false;
		$database = PhplistHelperPhplist::getDBO();
		JLoader::import( 'com_phplist.helpers.subscription', JPATH_ADMINISTRATOR.DS.'components' );
		$tablename = PhplistHelperSubscription::getTableName();
		JLoader::import( 'com_phplist.helpers.newsletter', JPATH_ADMINISTRATOR.DS.'components' );
		$tablename_letter = PhplistHelperNewsletter::getTableName();
		
		$query = "
			SELECT
				*
			FROM
				{$tablename}
				LEFT JOIN {$tablename_letter} ON {$tablename}.listid = {$tablename_letter}.id   
			WHERE
				{$tablename}.userid = '{$userid}'
		";
		$database->setQuery( $query );
		$data = $database->loadObjectList();
		$success = $data;
		
		return $success;
	}
	
	/**
	 * 
	 * @param $value	value to search for
	 * @param $by		foreignkey, id, email
	 * @param $returnObject
	 * @return unknown_type
	 */
	function getUser( $value, $returnObject='0', $by='id' )
	{
		$success = false;
		
		switch ($by) 
		{
			case "uid": 
				$success = PhplistHelperUser::getUserByUid( $value, $returnObject );
			  break;
			case "foreignkey": 
				$success = PhplistHelperUser::getUserByForeignKey( $value, $returnObject );
			  break;
			case "email":
				$success = PhplistHelperUser::getUserByEmail( $value, $returnObject ); 
			  break;
			case "id": 
			default: 
				$success = PhplistHelperUser::getUserById( $value, $returnObject );
			  break;	
		}
		
		return $success;
	}

	/**
	 * 
	 * @param $value
	 * @param $returnObject
	 * @return unknown_type
	 */
	function getUserByUid( $value, $returnObject='0' )
	{
		$success = false;
		
		$database = PhplistHelperPhplist::getDBO();
		if (isset($database->error)) 
		{
			return $success;
		}
		$tablename = PhplistHelperUser::getTableName();
		$value = $database->getEscaped( $value );
		
		$query = "
			SELECT
				*
			FROM
				$tablename
			WHERE
				`uniqid` = '{$value}'
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
	 * @param $value
	 * @param $returnObject
	 * @return unknown_type
	 */
	function getUserByForeignKey( $value, $returnObject='0' )
	{		
		$success = false;
		
		$database = PhplistHelperPhplist::getDBO();
		if (isset($database->error)) 
		{
			return $success;
		}
		$tablename = PhplistHelperUser::getTableName();
		$value = $database->getEscaped( $value );
		
		$query = "
			SELECT
				*
			FROM
				$tablename
			WHERE
				`foreignkey` = '{$value}'
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
	 * @param $value
	 * @param $returnObject
	 * @return unknown_type
	 */
	function getUserById( $value, $returnObject='0' )
	{		
		$success = false;
		
		$database = PhplistHelperPhplist::getDBO();
		if (isset($database->error)) 
		{
			return $success;
		}
		$tablename = PhplistHelperUser::getTableName();
		$value = intval( $value );
		
		$query = "
			SELECT
				*
			FROM
				$tablename
			WHERE
				`id` = '{$value}'
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
	 * @param $value
	 * @param $returnObject
	 * @return unknown_type
	 */
	function getUserByEmail( $value, $returnObject='0' )
	{		
		$success = false;
		
		$database = PhplistHelperPhplist::getDBO();
		if (isset($database->error)) 
		{
			return $success;
		}
		$tablename = PhplistHelperUser::getTableName();
		$value = $database->getEscaped( $value );
		
		$query = "
			SELECT
				*
			FROM
				$tablename
			WHERE
				`email` = '{$value}'
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
	 * check if joomla username already exists
	 * @param $string
	 * @return unknown_type
	 */
	function usernameExists( $string, $returnObject='0' ) 
	{
		$success = false;
		$database = JFactory::getDBO();

		$string = $database->getEscaped($string);
		$query = "
			SELECT 
				*
			FROM 
				#__users
			WHERE 1
			AND 
				`username` = '{$string}'
			LIMIT 1
		";
		$database->setQuery($query);
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
	 * check if joomla email already exists
	 * @param $string
	 * @return unknown_type
	 */
	function emailExists( $string, $returnObject='0' ) 
	{
		$success = false;
		$database = JFactory::getDBO();

		$string = $database->getEscaped($string);
		$query = "
			SELECT 
				*
			FROM 
				#__users
			WHERE 1
			AND 
				`email` = '{$string}'
			LIMIT 1
		";
		$database->setQuery($query);
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
	 * Given a string, this will generate a new unique username 
	 * @param $string
	 * @return unknown_type
	 */
	function createNewUsername( $string )
	{
		if (!$exists = PhplistHelperUser::usernameExists( $string )) {
			return $string;
		}
		
		$n=1;
		$testString = $string.'_'.$n;
		while ($exists = PhplistHelperUser::usernameExists( $testString )) {
			$n++;
			$testString = $string.'_'.$n;
		}
		
		return $testString;
	}
	
	/**
	 * Returns yes/no
	 * @param mixed Boolean
	 * @param mixed Boolean
	 * @return array
	 */
	function &createNewUser( $details, $useractivation='0' ) {
		
		JLoader::import( 'com_phplist.helpers.newsletter', JPATH_ADMINISTRATOR.DS.'components' );
		
		global $mainframe;
		$success = false;

		// Get required system objects
		$user 		= clone(JFactory::getUser());
		$pathway 	=& $mainframe->getPathway();
		$config		=& JFactory::getConfig();
		$authorize	=& JFactory::getACL();
		$document   =& JFactory::getDocument();

		$usersConfig = &JComponentHelper::getParams( 'com_users' );

		// Initialize new usertype setting
		$newUsertype = $usersConfig->get( 'new_usertype' );
		if (!$newUsertype) { $newUsertype = 'Registered'; }

		// Bind the post array to the user object
		if (!$user->bind( $details )) {
			return $success;
		}

		// Set some initial user values
		$user->set('id', 0);
		$user->set('usertype', '');
		$user->set('gid', $authorize->get_group_id( '', $newUsertype, 'ARO' ));

		$config =& JFactory::getConfig();
		$date = JFactory::getDate();
		$date->setOffset($config->getValue('config.offset'));
		$user->set('registerDate', $date->toMySQL(true));

		// If user activation is turned on, we need to set the activation information
		// $useractivation = $usersConfig->get( 'useractivation' );
		if ($useractivation == '1') {
			jimport('joomla.user.helper');
			$user->set('activation', md5( JUserHelper::genRandomPassword() ) );
			$user->set('block', '1');
		}

		// If there was an error with registration, set the message and display form
		if ( !$user->save() ) {
			// JError::raiseWarning('', JText::_( $user->getError()));
			// $this->register();
			return $success;
		}

		// Send registration confirmation mail
		// $password = JRequest::getString('password', '', 'post', JREQUEST_ALLOWRAW);
		// $password = preg_replace('/[\x00-\x1F\x7F]/', '', $password); // Disallow control chars in the email
	$sendemail = PhplistHelperEmail::_sendJoomlaUserEmail( $user, $details, $useractivation );
		
		return $user;
	}
	
	/**
	 * change phplist email address
	 */
	function changeUserEmail( $id, $newemail)
	{
		$success = false;
		
		$database = PhplistHelperPhplist::getDBO();
		if (isset($database->error)) 
		{
			return $success;
		}
		$tablename = PhplistHelperUser::getTableName();
		$id = intval( $id );
		
		$query = "
			UPDATE $tablename
				SET `email` = '{$newemail}'
			WHERE
				`id` = '{$id}'
		";
		$database->setQuery( $query );
		$update = $database->query();
		return $success;
	}

	/**
	 * Returns yes/no
	 * @param array [username] & [password]
	 * @param mixed Boolean
	 * 
	 * @return array
	 */	
	function login( $credentials, $remember='', $return='' ) {
		global $mainframe;

		if (strpos( $return, 'http' ) !== false && strpos( $return, JURI::base() ) !== 0) {
			$return = '';
		}

		// $credentials = array();
		// $credentials['username'] = JRequest::getVar('username', '', 'method', 'username');
		// $credentials['password'] = JRequest::getString('passwd', '', 'post', JREQUEST_ALLOWRAW);
		
		$options = array();
		$options['remember'] = $remember;
		$options['return'] = $return;

		//preform the login action
		$success = $mainframe->login($credentials, $options);

		if ( $return ) {
			$mainframe->redirect( $return );
		}
		
		return $success;
	}

	/**
	 * Returns yes/no
	 * @param mixed Boolean
	 * @return array
	 */
	function logout( $return='' ) {
		global $mainframe;

		//preform the logout action
		$success = $mainframe->logout();

		if (strpos( $return, 'http' ) !== false && strpos( $return, JURI::base() ) !== 0) {
			$return = '';
		}

		if ( $return ) {
			$mainframe->redirect( $return );
		}
		
		return $success;		
	}

	/**
	 * Set User confirmed state to 1
	 */
	function confirmUser( $uniqueid )
	{
		$success = false;
		
		$database = PhplistHelperPhplist::getDBO();
		$tablename = PhplistHelperUser::getTableName();
		
		$query = "
			UPDATE $tablename
				SET `confirmed` = '1'
			WHERE
				`uniqid` = '{$uniqueid}'
		";
		$database->setQuery( $query );
		$update = $database->query();
		return $success;
	}
	
	/**
	 * 
	 * Deletes user completely from PHPList database
	 */
	function deleteUser( $id, $source='foreignkey' )
	{
		$success = false;
		JLoader::import( 'com_phplist.helpers.user', JPATH_ADMINISTRATOR.DS.'components' );
		
		$phplist_prefix = PhplistHelperPhplist::getPrefix();
		$phplist_user_prefix = PhplistHelperPhplist::getUserTablePrefix();
		$database = PhplistHelperPhplist::getDBO();
		if (isset($database->error)) 
		{
			return $success;
		}
		
		if ($source == 'foreignkey')
		{
			// Get PHPList User ID from Joomla user
			$phplistUserId = PhplistHelperUser::getUser( $id, '1', 'foreignkey' );
			$phplistUserId = $phplistUserId->id;
		}
		else
		{
			$phplistUserId = $id;
		}
		
		// Delet user from all tables (same as occurs in PHPlist when deleting user)
		// TODO Make into one query
		$query = "
			DELETE
			FROM
				{$phplist_user_prefix}_user
			WHERE
				`id` = {$phplistUserId}
		";
		
		$database->setQuery( $query );
		$delete = $database->query();
		
		$query2 = "
			DELETE
			FROM
				{$phplist_prefix}_listuser
			WHERE
				`userid` = {$phplistUserId}
		";
		
		$database->setQuery( $query2 );
		$delete2 = $database->query();
		
		$query3 = "
			DELETE
			FROM
				{$phplist_user_prefix}_user_attribute
			WHERE
				`userid` = {$phplistUserId}
		";
		
		$database->setQuery( $query3 );
		$delete3 = $database->query();
		
		$query4 = "
			DELETE
			FROM
				{$phplist_prefix}_usermessage
			WHERE
				`userid` = {$phplistUserId}
		";
		
		$database->setQuery( $query4 );
		$delete4 = $database->query();
		
		$query5 = "
			DELETE
			FROM
				{$phplist_prefix}_user_message_bounce
			WHERE
				`user` = {$phplistUserId}
		";
		
		$database->setQuery( $query5 );
		$delete5 = $database->query();
		
		$query6 = "
			DELETE
			FROM
				{$phplist_prefix}_user_userhistory
			WHERE
				`userid` = {$phplistUserId}
		";
		
		$database->setQuery( $query6 );
		$delete6 = $database->query();
		$success = true;
		return $success;
	}
	
	/**
	 * 
	 * Just delete the foreign key and leave user in PHPList database
	 */
	
	function deleteForeignkey( $foreignkey )
	{
		$success = false;
		JLoader::import( 'com_phplist.helpers.user', JPATH_ADMINISTRATOR.DS.'components' );
		
		$phplist_user_prefix = PhplistHelperPhplist::getUserTablePrefix();
		$phplist_prefix = PhplistHelperPhplist::getPrefix();
		$database = PhplistHelperPhplist::getDBO();
		
		if (isset($database->error)) 
		{
			return $success;
		}
		
		$query = "
			UPDATE
				{$phplist_user_prefix}_user
			SET
				`foreignkey` = ''
			WHERE `foreignkey` = {$foreignkey}
		";
		
		$database->setQuery( $query );
		$delete = $database->query();
		$success = true;
	}
	
	/**
	 * checks joomla and phplist are in sync
	 */
	function syncJoomlaUsers($returnObject = '1')
	{
		$success = false;
		JLoader::import( 'com_phplist.helpers.user', JPATH_ADMINISTRATOR.DS.'components' );
		
		// Get the list of all foreignkeys
		$phplist_user_prefix = PhplistHelperPhplist::getUserTablePrefix();
		$phplist_prefix = PhplistHelperPhplist::getPrefix();
		$phplistAll_database = PhplistHelperPhplist::getDBO();
		$phplistAll_query = "
			SELECT 
				foreignkey
			FROM 
				{$phplist_user_prefix}_user
			WHERE `foreignkey` != 'NULL' 
			AND `foreignkey` != '' 
		";
		$phplistAll_database->setQuery($phplistAll_query);
		$phplistAll_data = $phplistAll_database->loadObjectList();
		
		// create CSV of foreignkeys
		$csv = 0;
		foreach ($phplistAll_data as $key)
		{
			$csv .= ',' .$key->foreignkey;
		}
		
		// get id's of all Joomla! users
		$joomlaAll_database = JFactory::getDBO();
		$joomlaAll_query = "
			SELECT 
				id
			FROM 
				#__users
		";
		$joomlaAll_database->setQuery($joomlaAll_query);
		$joomlaAll_data = $joomlaAll_database->loadObjectList();
		
		// create CSV of Joomla! ID's
		$csv2 = 0;
		foreach ($joomlaAll_data as $key2)
		{
			$csv2 .= ',' .$key2->id;
		}
		
		// get Foreignkeys which are NOT Joomla! Id's
		$phplist_database = PhplistHelperPhplist::getDBO();
		$phplist_query = "
			SELECT 
				foreignkey
			FROM 
				{$phplist_user_prefix}_user
			WHERE foreignkey NOT IN ({$csv2})
		";
		$phplist_database->setQuery($phplist_query);
		$phplist_database->query();
		$numrows_phplist = $phplist_database->getNumRows();
		$phplist_data = $phplist_database->loadObjectList();
		
		// get Joomla! users not in PHPList
		$joomla_database = JFactory::getDBO();
		$joomla_query = "
			SELECT 
				id
			FROM 
				#__users
			WHERE id NOT IN ({$csv})
		";
		$joomla_database->setQuery($joomla_query);
		$joomla_database->query();
		$numrows_joomla = $joomla_database->getNumRows();
		$joomla_data = $joomla_database->loadObjectList();
		
		$numrows = $numrows_phplist + $numrows_joomla;
		
		if ($joomla_data || $phplist_data)
		{
			// Tidying up - set 'null' foreign keys to '' so that user admin ordering on joomla id works
			$tidy_database = PhplistHelperPhplist::getDBO();
			$tidy_query = "
			UPDATE 
				{$phplist_user_prefix}_user
			SET foreignkey = '' 
			WHERE foreignkey = 'NULL' 
			";
			$tidy_database->setQuery($tidy_query);
			$tidy_database->query();
		
			//return number of missing users
			$success->missingusers = $numrows_joomla;
			
			//return number of stray foreignkeys (from Joomla! users who no longer exist)
			$success->nonexistantusers = $numrows_phplist;
			
			if ($returnObject == '1')
			{
				// create PHPList users for missing Joomla! Users
				foreach ($joomla_data as $joomla_data)
				{
					$joomlaUser = JFactory::getUser( (int) $joomla_data->id );
					PhplistHelperUser::create( $joomlaUser );
				}
			// remove foreignkeys of non-existant Joomla! Users
				foreach ($phplist_data as $phplist_data)
				{
					PhplistHelperUser::deleteForeignkey( $phplist_data->foreignkey );
				}

			}

		}
		return $success;	
	}
	
}