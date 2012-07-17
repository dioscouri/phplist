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

class PhplistHelperSubscription extends PhplistHelperBase
{

	/**
	 * Returns the phphlist newsletter table name
	 * @return unknown_type
	 */
	function getTableName() 
	{
		JLoader::import( 'com_phplist.helpers.phplist', JPATH_ADMINISTRATOR.DS.'components' ); 
		$success = false;
		$phplist_prefix = PhplistHelperPhplist::getPrefix();
		$success = "{$phplist_prefix}_listuser";
		return $success;
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
	function isUser( $userid, $typeid, $returnObject='0' )
	{
		$success = false;
		$database = PhplistHelperPhplist::getDBO();
		$tablename = PhplistHelperSubscription::getTableName();
		
		$query = "
			SELECT
				*
			FROM
				{$tablename}
			WHERE
				`listid` = '{$typeid}'
			AND
				`userid` = '{$userid}'
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
	 * Wrapper for modifying/creating a subscription for a user
	 * 
	 * @param object	$details->userid, $details->typeid
	 * @return boolean
	 */
	function storeUserTo( $details )
	{
		JLoader::import( 'com_phplist.helpers.subscription', JPATH_ADMINISTRATOR.DS.'components' );
		
		$success = false;
		
		if (!is_object($details) || empty($details->userid) || empty($details->listid) )
		{
			return $success;
		}
		
		$isUser = PhplistHelperSubscription::isUser( $details->userid, $details->listid );
		if ($isUser)
		{
			$success = true;
		}
		else
		{
			$success = PhplistHelperSubscription::addUserTo( $details );
		}
		
		return $success;
	}

	/**
	 * 
	 * @param object	$details->userid, $details->typeid
	 * @return boolean
	 */
	function addUserTo( $details )
	{
		JLoader::import( 'com_phplist.helpers.phplist', JPATH_ADMINISTRATOR.DS.'components' );
		$success = false;
		$database = PhplistHelperPhplist::getDBO();
		$tablename = PhplistHelperSubscription::getTableName();
		
		$config =& JFactory::getConfig();
		$date = JFactory::getDate();
		$date->setOffset($config->getValue('config.offset'));
		$datetime = $date->toMySQL(true);
		
		$query = "
			INSERT INTO
				$tablename
			SET 
				`listid` = '{$details->listid}',
				`userid` = '{$details->userid}',
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
	function removeUserFrom( $details )
	{
		JLoader::import( 'com_phplist.helpers.phplist', JPATH_ADMINISTRATOR.DS.'components' );
		$success = false;
		
		if (!is_object($details) || empty($details->userid) || empty($details->listid) )
		{
			return $success;
		}
		$database = PhplistHelperPhplist::getDBO();
		$tablename = PhplistHelperSubscription::getTableName();
		
		$query = "
			DELETE FROM
				$tablename
			WHERE 
				`listid` = '{$details->listid}'
			AND
				`userid` = '{$details->userid}'
			LIMIT 1
		";
		$database->setQuery( $query );
		$success = $database->query();
		
		return $success;
	}
	
		/**
	 *
	 * @param $details
	 * @param $task
	 * @return unknown_type
	 */
	function switchSubscriptions ($details, $returnObject = '0')
	{
		$success = false;
		JLoader::import( 'com_phplist.helpers.newsletter', JPATH_ADMINISTRATOR.DS.'components' );
		$task = JRequest::getVar( 'task' );

		$cids = JRequest :: getVar('cid', array(0), 'request', 'array');
		
		foreach ($cids as $cid)
		{
			$details->listid = $cid;
			$isSubscribed = PhplistHelperSubscription::isUser( $details->userid, $details->listid );
			$newslettername = PhplistHelperNewsletter::getName ($details->listid)->name;
			switch ($task) {
				case "unsubscribe_selected":
				case "unsubscribe":
					if(!$action = PhplistHelperSubscription::removeUserFrom( $details ))
					{
						// TODO why isn't this error showing?
						$this->message .= "<li>".JText::_( "YOU ARE NOT SUBSCRIBED TO" )." <b>" . $newslettername. "</b></li>";
						$this->messagetype 	= 'notice';
					}
					else
					{
						$this->message  .= "<li>" . JText::_( 'YOU HAVE BEEN UNSUBSCRIBED FROM' )." <b>" . $newslettername. "</b></li>";
						$this->messagetype 	= 'message';
					}
					break;
				case "subscribe_selected":
				case "subscribe_new":
				case "subscribe":
				case "subscribeModule":
					if (!$action = PhplistHelperSubscription::addUserTo( $details ))
					{
						$this->message .= "<li>" . JText::_( "YOU ARE ALREADY SUBSCRIBED TO" )." <b>" . $newslettername. "</b></li>";
						$this->messagetype 	= 'notice';
					}
					else
					{
						$this->message  .= "<li>" . JText::_( 'SUBSCRIPTION ADDED FOR' )." <b>" . $newslettername. "</b></li>";
						$this->messagetype 	= 'message';
					}
					break;
				default:
					if ($isSubscribed)
					{
						// unsubscribe
						if(!$action = PhplistHelperSubscription::removeUserFrom( $details ))
						{
							$this->message .= "<li>".JText::_( "UNSUBSCRIBE FROM NEWSLETTER FAILED:" ).": " . $cid . "</li>";
							$this->messagetype 	= 'notice';
						}
						else
						{
							$this->messagetype 	= 'message';
							$this->message  .= "<li>" . JText::_( 'YOU HAVE BEEN UNSUBSCRIBED FROM' )." <b>" . $newslettername. "</b></li>";
						}
					}
					else
					{
						// subscribe
						if (!$action = PhplistHelperSubscription::addUserTo( $details ))
						{
							$this->message .= "<li>".JText::_( "SUBSCRIBE TO NEWSLETTER FAILED" ).": " . $cid . "</li>";
							$this->messagetype 	= 'notice';
						}
						else
						{
							$this->messagetype 	= 'message';
							$this->message  .= "<li>" . JText::_( 'SUBSCRIPTION ADDED FOR' )." <b>" . $newslettername. "</b></li>";
						}
					}
					break;
			}
			$success = true;
		}
		if ($returnObject == '1')
		{
			$success = $this->message;
		}
		return $success;
	}
	
	function sendActivationEmail($details, $newsletterids = false)
	{
		JLoader::import( 'com_phplist.helpers.newsletter', JPATH_ADMINISTRATOR.DS.'components' );
		JLoader::import( 'com_phplist.helpers.subscription', JPATH_ADMINISTRATOR.DS.'components' );
		
		if ($newsletterids != false)
		{
			//if coming from user plugin
			$cids = explode( ',', trim($newsletterids) );
		}
		else
		{
			//if coming from newsletter page or subscribe module
			$cids = JRequest :: getVar('cid', array(0), 'request', 'array');
		}
		
		$newsletter_names = '';
		foreach ($cids as $cid)
		{
			$getname = PhplistHelperNewsletter::getName($cid);
			$newsletter_names .= '* ' .$getname->name."\n";
			$send = PhplistHelperUser::_sendConfirmationMail($details->email, '', $newsletter_names, $details->uid);
		}
		$this->message .= JText::_( "CONFIRMATION EMAIL SENT" );
	}
}

?>
