<?php
/**
* Author: Dioscouri Design - www.dioscouri.com
* @package Phplist
* @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

// Import library dependencies
jimport('joomla.plugin.plugin');

/**
 * Phplist User Plugin
 *
 * @package		Joomla
 * @subpackage	JFramework
 * @since 		1.5
 */
class plgUserPhplist extends JPlugin 
{
	/**
	 * @var $_element  string  Should always correspond with the plugin's filename,
	 *                         forcing it to be unique
	 */
	var $_element    = 'user_phplist';
	
	function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage( '', JPATH_ADMINISTRATOR );
		$this->loadLanguage('com_phplist');
		$language = JFactory::getLanguage();
		$language -> load('plg_'.$this->_element, JPATH_ADMINISTRATOR, 'en-GB', true);
		$language -> load('plg_'.$this->_element, JPATH_ADMINISTRATOR, null, true);
		
	}

	/**
	 * Confirms the extension is installed completely and adds helper files
	 * @return unknown_type
	 */
	function _isInstalled()
	{
		$success = false;
		
		jimport('joomla.filesystem.file');
		if (JFile::exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_phplist'.DS.'defines.php' )) 
		{	
			if ( !class_exists('Phplist') ) {
				JLoader::register( "Phplist", JPATH_ADMINISTRATOR.DS."components".DS."com_phplist".DS."defines.php" );
			}
			Phplist::load( 'PhplistHelperUser', 'helpers.user' );
			Phplist::load( 'PhplistHelperSubscription', 'helpers.subscription' );
			Phplist::load( 'PhplistHelperPhplist', 'helpers.phplist' );
			Phplist::load( 'PhplistHelperAttribute', 'helpers.attribute' );
			Phplist::load( 'PhplistHelperEmail', 'helpers.email' );
			
			$success = true;
			
		}
		
		if ($success == true) {
			// Also check that DB is setup
			$database = PhplistHelperPhplist::getDBO();
			if (!isset($database->error)) 
			{
				$success = true;
			}
		}
		return $success;
	}
	
	/**
	 * gets user information and checks through plugin parameters for actions required
	 */
	function _runPlugin($joomlaUser, $isnew = false)
	{
		$success = false;
		if ( !$this->_isInstalled() ) {
			return $success;
		}
		
		///check if user exists in PHPList
		$phplistUser = PhplistHelperUser::getUser( $joomlaUser['email'], '1', 'email' );
		
		//check PHPlist confirmation email config setting
		$config = &Phplist::getInstance();
		$activation_email = $config->get( 'activation_email', '1' );
		
		//check Joomla! config Activation email setting
		$jusersConfig = &JComponentHelper::getParams( 'com_users' );
		$jactivation = $jusersConfig->get( 'useractivation' );
		
		if (!$phplistUser)
		{
			//if not, create a new PHPList User
			$details = new JObject();
			$details->email = $joomlaUser['email'];
			$details->id = $joomlaUser['id'];
						
			$newPhplistUser = PhplistHelperUser::create( $details, '', 'false', $jactivation );
			$phplistUser = PhplistHelperUser::getUser( $newPhplistUser->id, '1', 'id' );
			
			if ($activation_email == '1')
			{
				//send activation email if required
				$phplistUser->uid = $phplistUser->uniqid;
				$send = PhplistHelperEmail::_sendConfirmationEmail($phplistUser, $this->params->get( 'newsletterids', '0' ));
			}			
		}
				
		if (!$isnew)
		{
			if (!isset($joomlaUser['dontchangeemail'])) {
				// change PHPList user email
				$changeemail = PhplistHelperUser::changeUserEmail( $phplistUser->id, $joomlaUser['email']);
			}
			
			//FOR JOOMLA ACTIVIATION EMAIL SET TO 'on'
			//if phplist activation email 'off' and joomla activation email 'on' and Joomla user activated but phplist user not confirmed...
			// then confirm user
			if ($activation_email == '0' && $jactivation == '1' && $phplistUser->confirmed != '1' && $joomlaUser['activation'] == '' && $joomlaUser['block'] == '0')
			{
				$confirmuser = PhplistHelperUser::confirmUser( $phplistUser->uniqid);
			}
		}
		
		/// Automatically Subscribe users to Newsletter(s)
		if ($newsletterids = $this->params->get( 'newsletterids', '0' ))
		{
			$run = plgUserPhplist::_subscribeUser($phplistUser, $newsletterids);
		}
		
		/// store Joomla! 'Name' as PHPList attribute
		if ($nameattribid = $this->params->get( 'nameattribid', '0' ))
		{
			$run = plgUserPhplist::_storeAttributeName($phplistUser, $joomlaUser);
		}
		
		/// store Joomla! 'User Name' as PHPList attribute
		if ($nameattribid = $this->params->get( 'usernameattribid', '0' ))
		{
			$run = plgUserPhplist::_storeAttributeUsername($phplistUser, $joomlaUser);
		}
	}
	
	/**
	 * Execute Add
	 *
	 * @access public
	 * @param array holds the user data
	 * @param array holds the item 
	 * @return boolean True on success
	 * @since 1.5
	 */
	function _subscribeUser( $phplistUser, $newsletterids ) 
	{
		$success = false;
		$cids = explode( ',', trim($newsletterids) );

		$details = new JObject();
		$details->userid = $phplistUser->id;
		$errorMsg = "";
		$error = false;
		for ($i=0; $i<count($cids); $i++)
		{
			$listid = $cids[$i];
			$details->listid = $listid;
			if ( !$store = PhplistHelperSubscription::storeUserTo( $details ) ) {
				$errorMsg.= "<li>".JText::_( 'Subscribe Failed' )." - {$listid}</li>";
				$error = true;
			}
		}	
	
		$success = true;
		return $success;
	}

	/**
	 * Stores Joomla! name as chosen Attribute
	 */
	function _storeAttributeName($phplistUser, $joomlaUser)
	{	
		$success = false;
		$attribId = $this->params->get( 'nameattribid', '0' );
		if ($insert = PhplistHelperAttribute::insertAttributeValue( $phplistUser->id, $attribId, $joomlaUser['name'] ))
		{
			$success = true;
		}
		return $success;
	}
	
	/**
	 * Stores Joomla! username as chosen Attribute
	 */
	function _storeAttributeUsername($phplistUser, $joomlaUser)
	{
		$success = false;
		$attribId = $this->params->get( 'usernameattribid', '0' );
		if ($insert = PhplistHelperAttribute::insertAttributeValue( $phplistUser->id, $attribId, $joomlaUser['username'] ))
		{
			$success = true;
		}
		return $success;
	}
	/**
	 * This method should handle any login logic and report back to the subject
	 *
	 * @access	public
	 * @param 	array 	holds the user data
	 * @param 	array    extra options
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function onLoginUser( $user, $options ) 
	{
		$success = null;
		// assign the userid to user['id'] (onLogin doesn't populate this field in the array) 
		$user['id'] = intval(JUserHelper::getUserId($user['username']));
		$details = JFactory::getUser($user['id']);
		$user['name'] = $details->name;
		
		//fix for clash with emailasusername plugin
		$user['dontchangeemail'] = true;
		
		// then execute the auto-add
		if ($run = plgUserPhplist::_runPlugin( $user ))
		{
			$success = true;
		}
		return $success;
	}
	/**
	 * for Joomla 2.5
	 */
	function onUserLogin( $user, $options )
	{
		$success = null;
		// assign the userid to user['id'] (onLogin doesn't populate this field in the array)
		$user['id'] = intval(JUserHelper::getUserId($user['username']));
		$details = JFactory::getUser($user['id']);
		$user['name'] = $details->name;
	
		//fix for clash with emailasusername plugin
		$user['dontchangeemail'] = true;
	
		// then execute the auto-add
		if ($run = plgUserPhplist::_runPlugin( $user ))
		{
			$success = true;
		}
		return $success;
	}
	/**
	 * Example store user method
	 *
	 * Method is called after user data is stored in the database
	 *
	 * @param 	array		holds the new user data
	 * @param 	boolean		true if a new user is stored
	 * @param	boolean		true if user was succesfully stored in the database
	 * @param	string		message
	 */
	function onAfterStoreUser($user, $isnew, $succes, $msg) 
	{	
		$success = null;
		if ($run = plgUserPhplist::_runPlugin( $user, $isnew ))
		{
			$success = true;
		}
		return $success;
	}
	
	/** for Joola 2.5
	*/
	function onUserAfterSave($user, $isnew, $succes, $msg)
	{
		$success = null;
		if ($run = plgUserPhplist::_runPlugin( $user, $isnew ))
		{
			$success = true;
		}
		return $success;
	}

	/**
	 * Example store user method
	 *
	 * Method is called after user data is deleted from the database
	 *
	 * @param 	array		holds the user data
	 * @param	boolean		true if user was succesfully stored in the database
	 * @param	string		message
	 */
	function onAfterDeleteUser($user, $succes, $msg) 
	{	
		$success = null;		
		if ( !$this->_isInstalled() )
		{
			return $success;
		}
		
		$joomlaUser = JFactory::getUser( $user['id'] );
		
		if (!$this->params->get( 'enable_autodelete', '1' ))
		{
			$deleteuser = PhplistHelperUser::deleteForeignkey( $user['id'] );
			return $success;
		}
		else
		{
			$deleteuser = PhplistHelperUser::deleteUser( $user['id'] );
		}

		return $success;
	}
	/**
	 for Joomla 2.5
	 */
	function onUserAfterDelete($user, $succes, $msg)
	{
		$success = null;
		if ( !$this->_isInstalled() )
		{
			return $success;
		}
	
		$joomlaUser = JFactory::getUser( $user['id'] );
	
		if (!$this->params->get( 'enable_autodelete', '1' ))
		{
			$deleteuser = PhplistHelperUser::deleteForeignkey( $user['id'] );
			return $success;
		}
		else
		{
			$deleteuser = PhplistHelperUser::deleteUser( $user['id'] );
		}
	
		return $success;
	}
	
}

?>