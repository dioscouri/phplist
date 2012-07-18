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

class PhplistHelperEmail extends PhplistHelperBase
{
	/**
	 * Returns the phphlist user attributes table name
	 * @return unknown_type
	 */
	function getTableName_forward()
	{
		JLoader::import( 'com_phplist.helpers.phplist', JPATH_ADMINISTRATOR.DS.'components' ); 
		$success = false;
		$phplist_user_prefix = PhplistHelperPhplist::getUserTablePrefix();
		$success = "{$phplist_user_prefix}_message_forward";
		return $success;
	}

	/**
	 * Sends email to new Joomla! users created by 'auto-create'.
	 * Returns yes/no
	 * @param object
	 * @param mixed Boolean
	 * @return array
	 */
	function _sendJoomlaUserEmail( &$user, $details, $useractivation ) {
		global $mainframe;

		$db		=& JFactory::getDBO();

		// get user details
		$name 		= $user->get('name');
		$email 		= $user->get('email');
		$username 	= $user->get('username');
		$activation	= $user->get('activation');
		$password 	= $details['password2']; // using the original generated pword for the email

		// get Joomla! User config settings
		$usersConfig 	= &JComponentHelper::getParams( 'com_users' );
		// $useractivation = $usersConfig->get( 'useractivation' );
		$sitename 		= $mainframe->getCfg( 'sitename' );
		$mailfrom 		= $mainframe->getCfg( 'mailfrom' );
		$fromname 		= $mainframe->getCfg( 'fromname' );
		if ( ! $mailfrom  || ! $fromname )
		{
			$fromname = $rows[0]->name;
			$mailfrom = $rows[0]->email;
		}
		
		$siteURL = JURI::base();

		$subject = sprintf ( JText::_( 'Account details for' ), $name, $sitename);
		$subject = html_entity_decode($subject, ENT_QUOTES);

		if ( $useractivation == 1 ){
			$message = sprintf ( JText::_( 'Email Message Activation' ), $sitename, $siteURL, $username, $password, $activation );
		} else {
			$message = sprintf ( JText::_( 'Email Message' ), $sitename, $siteURL, $username, $password );
		}

		$message = html_entity_decode($message, ENT_QUOTES);
		
		$success = PhplistHelperEmail::_sendEmail($mailfrom, $fromname, $email, $subject, $message);

		//get all super administrator
		/*
		$query = 'SELECT name, email, sendEmail' .
				' FROM #__users' .
				' WHERE LOWER( usertype ) = "super administrator"';
		$db->setQuery( $query );
		$rows = $db->loadObjectList();
		
		 // Send notification to all administrators
		 $subject2 = sprintf ( JText::_( 'Account details for' ), $name, $sitename);
		 $subject2 = html_entity_decode($subject2, ENT_QUOTES);

		 // get superadministrators id
		 foreach ( $rows as $row )
		 {
		 if ($row->sendEmail)
		 {
		 $message2 = sprintf ( JText::_( 'SEND_MSG_ADMIN' ), $row->name, $sitename, $name, $email, $username);
		 $message2 = html_entity_decode($message2, ENT_QUOTES);
		 JUtility::sendMail($mailfrom, $fromname, $row->email, $subject2, $message2);
		 }
		 }
		 */

		return $success;
	}

	/**
	 * Returns yes/no
	 * @param object
	 * @param mixed Boolean
	 * @return array
	 */
	function _sendConfirmationEmail($details, $newsletters = 'cids')
	{
		$success = false;
		JLoader::import( 'com_phplist.helpers.newsletter', JPATH_ADMINISTRATOR.DS.'components' );
		
		if ($newsletters != 'cids')
		{
			//if coming from user plugin
			$cids = explode( ',', trim($newsletters) );
		}
		else
		{
			//if coming from newsletter page or subscribe module
			$cids = JRequest :: getVar('cid', array(0), 'request', 'array');
		}
		
		$newsletter_names = '';
		foreach ($cids as $newsletter)
		{
			$getname = PhplistHelperNewsletter::getName($newsletter);
			$newsletter_names .= '* ' .$getname->name."\n";
		}
		
		global $mainframe;
		$sitename 		= $mainframe->getCfg( 'sitename' );
		$mailfrom 		= $mainframe->getCfg( 'mailfrom' );
		$fromname 		= $mainframe->getCfg( 'fromname' );
		$siteURL		= JURI::base();

		$link = $siteURL . 'index.php?option=com_phplist&view=newsletters&task=confirm&uid='. $details->uid;

		$subject 	= JText::_( 'PLEASE CONFIRM YOUR SUBSCRIPTION' ) . '-'. $sitename ;
		$subject 	= html_entity_decode($subject, ENT_QUOTES);

		$message = sprintf( JText::_( 'EMAIL MESSAGE CONFIRMATION' ), $name, $newsletter_names, $link, $sitename);
		$message = html_entity_decode($message, ENT_QUOTES);

		$success = PhplistHelperEmail::_sendEmail($mailfrom, $fromname, $details->email, $subject, $message);
		
		if ($success == true)
		{
			$this->message .= JText::_( "CONFIRMATION EMAIL SENT" );
		}
		return $success;
	}
	
	/**
	 * For sending forwarded and test messages (via joomla mailer) ONLY. 
	 * NOT to be used to send out email newsletters (done by PHPList process queue via cron job)
	 * Returns yes/no
	 * @param object
	 * @param mixed Boolean
	 * @return array
	 */
	function _sendMessage($message, $task, $toemail, $useremail = '')
	{
		$success = false;
		
		JLoader::import( 'com_phplist.library.url', JPATH_ADMINISTRATOR.DS.'components' );
		$phplistconfig = &PhplistConfigPhplist::getInstance();
		$config = &Phplist::getInstance();
		
		//default text or html emails
		if ($config->get('default_html', '') == '1')
		{ $mode = 'html'; } 
		else $mode = 'text';
		
		$domain = $phplistconfig->get('domain', '');
		
		$fromfield = $message->fromfield;
		
		//taken from phplist admin/sendmaillib.php 39, TODO replace depreciated ereg with preg.
		if (ereg("([^ ]+@[^ ]+)",$fromfield,$regs)) {
			# if there is an email in the from, rewrite it as "name <email>"
			$fromfield = ereg_replace($regs[0],"",$fromfield);
			$mailfrom = $regs[0];
			# if the email has < and > take them out here
			$mailfrom = ereg_replace("<","",$mailfrom);
			$mailfrom = ereg_replace(">","",$mailfrom);
			# make sure there are no quotes around the name
			$fromname = ereg_replace('"',"",ltrim(rtrim($fromfield)));
		} elseif (ereg(" ",$fromfield,$regs)) {
			# if there is a space, we need to add the email
			$fromname = $fromfield;
			$mailfrom = "listmaster@$domain";
		} else {
			$mailfrom = $fromfield . "@$domain";
		}	

		$htmlmessage = $message->message;
		$textmessage = $message->textmessage;
		
		switch ($task)
		{
			case "forward":
				$subject = 'Fwd: ';
				$subject .= $message->subject;
				$footer = $phplistconfig->get( 'forwardfooter', '' );
				
				//remove placeholders
				## remove any existing placeholders
  				$htmlmessage = eregi_replace("\[[A-Z\. ]+\]","",$htmlmessage);
  				$textmessage = eregi_replace("\[[A-Z\. ]+\]","",$textmessage);
				
				//set subscribe url placeholder to newsletter page
				$subscribeurl = JURI::base().PhplistUrl::addItemid("index.php?option=com_phplist&view=newsletters");
				
				//add breaks above footer (as phplist does)
				$footer = "\n\n".$footer;
				
				if ($mode == 'html')
				{
					$subscribeurl = '<a href="'.$subscribeurl.'">';
					$subscribeurl .= JText::_('CLICK HERE');
					$subscribeurl .= '</a>';
				}
				// Replace placeholders in forwardfooter
				$footer = str_replace("[FORWARDEDBY]", $useremail, $footer);
				$footer = str_replace("[SUBSCRIBE]", $subscribeurl, $footer);
			break;
			case "test":
				$subject = 'Test Message: ';
				$subject .= $message->subject;
				$footer = '\n\n** TEST MESSAGE (the placeholders below will show as links when this message is sent out to the list) **\n\n';
				$footer .= $message->footer;
			break;
		}
		
		if ($mode == 'html')
		{
			$footer = nl2br($footer);
			// construct content for messages with no template assigned
			if ($message->template == ('0' || null || ''))
			{
				//get settings from PHPList Configuration
				//CSS for HTML messages without a template
				$html_email_style = $phplistconfig->get( 'html_email_style', '' );
				//Charset for HTML messages
				$html_charset = $phplistconfig->get( 'html_charset', '' );
				//Include CSS and charset in <head>
				$header = '<head><meta content="text/html;charset='.$html_charset.'" http-equiv="Content-Type"><title></title><style type="text/css">'.$html_email_style.'</style></head>';
				//Append Footer to message
				$content = $header . $htmlmessage . $footer;
			}
			else
			// construct content for messages with a template
			{
				//load template
				$templateId =  $message->template;
				$model  = $this->getModel( 'templates' );
				$template = $model->getTable();
				$template->load( $templateId , 'id' );
					
				//get template html
				$templatehtml = $template->template;
					
				//insert content into template
	 			$content = str_replace("[CONTENT]", $htmlmessage, $templatehtml);
	 			$content = str_replace("[FOOTER]", $footer, $content);
			}
		}
		else
		//if mode textonly
		{
			$content = $textmessage.$footer;
		}

		$success = PhplistHelperEmail::_sendEmail($mailfrom, $fromname, $toemail, $subject, $content, NULL, $mode);
		
		return $success;
	}

	/**
	 *
	 * @return unknown_type
	 */
	function _sendEmail( $from, $fromname, $recipient, $subject, $body, $actions=NULL, $mode=NULL, $cc=NULL, $bcc=NULL, $attachment=NULL, $replyto=NULL, $replytoname=NULL )
	{
		$success = false;

		$message =& JFactory::getMailer();
		$message->addRecipient( $recipient );
		$message->setSubject( $subject );
		$message->setBody( $body );
		$sender = array( $from, $fromname );
		$message->setSender($sender);
		
		switch ($mode)
		{
			case "html":
				$message->IsHTML(true);
			 break;
			case "text":
			default:
				$message->IsHTML(false);
			break;
		}
		
		$sent = $message->send();
		if ($sent == '1') {
			$success = true;
		}

		return $success;
	}

}