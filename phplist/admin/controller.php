<?php
/**
 * @version 0.1
 * @package Phplist
 * @author  Dioscouri Design
 * @link    http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

class PhplistController extends DSCControllerAdmin
{
	/**
	 * default view
	 */
	public $default_view = 'dashboard';
}

/* TODO The check below needs putting somewhere..
 *
*  // check that PHPlist database is configured
JLoader::import( 'com_phplist.helpers.phplist', JPATH_ADMINISTRATOR.DS.'components' );
$database = PhplistHelperPhplist::getDBO();
if (isset($database->error))
{
$view = JRequest::getVar('view');
$controller = JRequest::getWord('controller', $view);
if (!$controller || $controller == 'config'|| $view == '')
{
// if config view, display notice
JError::raiseNotice( 'Database Not Configured', JText::_( "PLEASE CONFIGURE PHPLIST DATABASE CONNECTION" ) );
}
else
{
// redirect to config
$link = 'index.php?option=com_phplist&view=config';
$app = JFactory::getApplication();
$app->redirect( $link );
}
}
*/

?>