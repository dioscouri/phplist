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

class modPhplistSubscribeHelper 
{	
	/**
	 * 
	 * @return unknown_type
	 */
	function _isInstalled()
	{
		$success = false;
		
		jimport('joomla.filesystem.file');
		if (JFile::exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_phplist'.DS.'helpers'.DS.'phplist.php')) 
		{
			// Require Helpers
			require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_phplist'.DS.'defines.php' );
			JLoader::import( 'com_phplist.helpers.phplist', JPATH_ADMINISTRATOR.DS.'components' );
			JLoader::import( 'com_phplist.helpers.newsletter', JPATH_ADMINISTRATOR.DS.'components' );
			JLoader::import( 'com_phplist.helpers.user', JPATH_ADMINISTRATOR.DS.'components' );
			JLoader::import( 'com_phplist.helpers.subscription', JPATH_ADMINISTRATOR.DS.'components' );
			JLoader::import( 'com_phplist.helpers.attribute', JPATH_ADMINISTRATOR.DS.'components' );
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
     * Retrieves the Itemid
     *
     * @access public
     */
	function getItemid( $link = "index.php?option=com_phplist&view=newsletters" ) 
	{
		$id = "";
		$database = JFactory::getDBO();
		$link = $database->getEscaped( strtolower( $link ) );
		
		$query = "
			SELECT 
				* 
			FROM 
				#__menu
			WHERE 1 
				AND LOWER(`link`) = '".$link."'
				AND `published` > '0'
			ORDER BY 
				`link` ASC
		";
	
		$database->setQuery($query);
		if ( $data = $database->loadObject() ) 
		{
			$id = $data->id;		
		}

		return $id;
	}
	
	/**
	 * Method for 
	 * @param $params
	 * @return unknown_type
	 */
    function getReturnURL( $params )
    {
    	if ($params->get('result_page') == '1')
    	{
    		return 'result_page';
    	}
    	else
    	{
        	if ($url =  $params->get('redirect_url'))
        	{
        	    $url = JRoute::_($url);
        	}
        	    else
        	{
         	   // stay on the same page
         	   $uri = JFactory::getURI();
         	   $url = $uri->toString(array('path', 'query', 'fragment'));
        	}
        	return base64_encode( $url );
    	}
    }

    /**
     * 
     * @return unknown_type
     */
    function processForm()
    {
    	JRequest::checkToken() or die( 'Invalid Token' );
    	JLoader::import( 'com_phplist.helpers.phplist', JPATH_ADMINISTRATOR.DS.'components' );
		JLoader::import( 'com_phplist.helpers.newsletter', JPATH_ADMINISTRATOR.DS.'components' );
		JLoader::import( 'com_phplist.helpers.user', JPATH_ADMINISTRATOR.DS.'components' );
		JLoader::import( 'com_phplist.helpers.subscription', JPATH_ADMINISTRATOR.DS.'components' );
		JLoader::import( 'com_phplist.helpers.attribute', JPATH_ADMINISTRATOR.DS.'components' );
		JLoader::import( 'com_phplist.helpers.email', JPATH_ADMINISTRATOR.DS.'components' );
		
		jimport('joomla.mail.helper');
		
    	$html = '';
    	$vars = new JObject();
    	
    	//get config settings
    	$config = &Phplist::getInstance();
    	$htmlemail = $config->get( 'default_html', '1' );
    	$activation_email = $config->get( 'activation_email', '1' );
    	
    	//get form inputs
    	$cids = JRequest :: getVar('cid', array(0), 'request', 'array');
    	if (JRequest::getVar('subscriberemail'))
    	{
    		// logged in or uid email
    		$email = JRequest::getVar( 'subscriberemail' );
    	}
    	else {
    	// new subscriber email
    	$email = JRequest::getVar( 'subscriber2add' );
    	}
    	$redirect = JRequest::getVar( 'return' );
    	
		/* TODO check a newsletter is selected (if tickboxes)
		 * if (intval($cids['0']) == '0')
		{
			$vars->message = JText::_( "PLEASE SELECT A NEWSLETTER" );
			$this->messagetype	= 'notice';
			$this->setRedirect( $redirect, $this->message, $this->messagetype );
			return false;
		}*/
		
		
		/* TODO check for valid email address
		 * if (!$isEmailAddress = JMailHelper::isEmailAddress( $email ))
		{
			$this->message .= JText::_( "PLEASE ENTER A VALID EMAIL ADDRESS" );
			$this->messagetype	= 'notice';
			$this->setRedirect( $redirect, $this->message, $this->messagetype );
			return false;
		}*/
    	
    	$phplistUser = PhplistHelperUser::getUser( $email, '1', 'email' );
    	
		$details = new JObject();
		$details->id = '';
		$details->email = $email;
		$details->htmlemail = JRequest::getVar( 'htmlemail' );
		
		if (!$phplistUser)
		{
			$phplistUser = PhplistHelperUser::create( $details, '', 'true' );
		}
		
		$details->userid = $phplistUser->id;
		$details->uid = $phplistUser->uniqid;

		if ($activation_email == '1')
		{
			$send = PhplistHelperEmail::_sendConfirmationEmail($details, 'cids');
		}	
		
		$switch = PhplistHelperSubscription::switchSubscriptions($details, '1');
		$saveattributes = PhplistHelperAttribute::saveAttributes($details->userid);		
		
        // TODO Then output any message you want.  $vars will be present in the result.php*/
		
        $vars->message = $switch; // put any kind of success messages inside $vars
    	
        if ($redirect != 'result_page')
        {
        	$msg = new stdClass();
			$msg->type 		= "message";
			$msg->message 	= $vars->message;
			$msg->link 		= base64_decode( $redirect);
			$this->setRedirect( $msg->link, $msg->message, $msg->type );
        }
        else
        {
        	// get the template and default paths for the layout file
        	$app = JFactory::getApplication();
        	$templatePath = JPATH_SITE.DS.'templates'.DS.$app->getTemplate().DS.'html'.DS.'mod_phplist_subscribe'.DS.'result.php';
        	$defaultPath = JPATH_SITE.DS.'modules'.DS.'mod_phplist_subscribe'.DS.'tmpl'.DS.'result.php';

        	// if the site template has a layout override, use it, otherwise use the default
        	jimport('joomla.filesystem.file');
        	if (JFile::exists( $templatePath ))
        	{
        		$layout = $templatePath;
        	}
        	else
        	{
        		$layout = $defaultPath;
        	}
        	 
        	// load the template file and capture the html output
        	ob_start();
        	include($layout);
        	$html = ob_get_contents();
        	ob_end_clean();

        	return $html;
        }
    }
}