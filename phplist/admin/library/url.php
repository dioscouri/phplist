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
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

class PhplistUrl extends DSCUrl
{

}

/**
 * TODO - this needs to go somewhere to integrate phplistuser uid.
 * 
 * Wrapper that adds the current Itemid and UID if present to the URL
 *
 * @param	string $string The string to translate
 *

 function &appendURL( $url, $logout='0' )
 {
 JLoader::import( 'com_phplist.helpers.user', JPATH_ADMINISTRATOR.DS.'components' );
 /// add itemid to url
 $return = PhplistUrl::addItemid($url);

 if ($logout == '0')
 {
 // add uid to url if valid user
 $uid = JRequest::getVar( 'uid' );
 $isUser = '';
 if ($uid)
 {
 $isUser = PhplistHelperUser::getUser($uid, '0', 'uid');
 }
 if ($isUser)
 {
 $return.= "&uid=".$uid;
 }
 }
 return $return;
 }
 */