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

// Check the registry to see if our Billets class has been overridden
if ( !class_exists('Phplist') ) {
    JLoader::register( "Phplist", JPATH_ADMINISTRATOR.DS."components".DS."com_phplist".DS."defines.php" );
}

// before executing any tasks, check the integrity of the installation
Phplist::getClass( 'PhplistHelperDiagnostics', 'helpers.diagnostics' )->checkInstallation();

// set the options array
$options = array( 'site'=>'site', 'type'=>'components', 'ext'=>'com_phplist' );

// Require the base controller
Phplist::load( 'PhplistController', 'controller', $options );

// Require specific controller if requested
$controller = JRequest::getWord('controller', JRequest::getVar( 'view' ) );
if (!Phplist::load( 'PhplistController'.$controller, "controllers.$controller", $options ))
    $controller = '';

if (empty($controller))
{
	// redirect to default
	$default_controller = new PhplistController();
	$redirect = "index.php?option=com_phplist&view=" . $default_controller->default_view;
	$redirect = JRoute::_( $redirect, false );
	JFactory::getApplication()->redirect( $redirect );
}

$doc = JFactory::getDocument();
$uri = JURI::getInstance();
$js = "var com_phplist = {};\n";
$js.= "com_phplist.jbase = '".$uri->root()."';\n";
$doc->addScriptDeclaration($js);

$parentPath = JPATH_ADMINISTRATOR . '/components/com_phplist/helpers';
DSCLoader::discover('PhplistHelper', $parentPath, true);

$parentPath = JPATH_ADMINISTRATOR . '/components/com_phplist/library';
DSCLoader::discover('Phplist', $parentPath, true);

// load the plugins
JPluginHelper::importPlugin( 'phplist' );

// Create the controller
$classname = 'PhplistController'.$controller;
$controller = Phplist::getClass( $classname );

// ensure a valid task exists
$task = JRequest::getVar('task');
if (empty($task))
{
    $task = 'display';  
}
JRequest::setVar( 'task', $task );

// Perform the requested task
$controller->execute( $task );

// Redirect if set by the controller
$controller->redirect();

?>