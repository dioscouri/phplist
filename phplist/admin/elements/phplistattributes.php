<?php
/**
 * @version	1.5
 * @package	PHPList
 * @author 	Dioscouri Design
 * @link 	http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');

if (JFile::exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_phplist'.DS.'helpers'.DS.'phplist.php'))
{
	// Require Helpers
	require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_phplist'.DS.'defines.php' );
	JLoader::import( 'com_phplist.tables.attributes', JPATH_ADMINISTRATOR.DS.'components' );
	JLoader::import( 'com_phplist.helpers.attribute', JPATH_ADMINISTRATOR.DS.'components' );
	JLoader::import( 'com_phplist.library.select', JPATH_ADMINISTRATOR.DS.'components' );

	

if (!defined('PhplistHelperFileExists')) {
	DEFINE( "PhplistHelperFileExists", '1');
	}
}


class JElementPhplistAttributes extends JElement
{

	var	$_name = 'PhplistAttributes';
	
	function fetchElement($name, $value, &$node, $control_name)
	{	
		return PhplistSelect::attributes( @$value, $control_name.'['.$name.']',  @$attribs, 'id', true );
	}
}
?>