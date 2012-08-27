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

class PhplistViewBase extends DSCViewAdmin
{
    function display($tpl=null)
    {
        JHTML::_('stylesheet', 'common.css', 'media/dioscouri/css/');
        JHTML::_('stylesheet', 'admin.css', 'media/com_phplist/css/');
    
        $parentPath = JPATH_ADMINISTRATOR . '/components/com_phplist/helpers';
        DSCLoader::discover('PhplistHelper', $parentPath, true);
    
        $parentPath = JPATH_ADMINISTRATOR . '/components/com_phplist/library';
        DSCLoader::discover('Phplist', $parentPath, true);
        
        // check that PHPlist database is configured
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
    
        parent::display($tpl);
    }
}