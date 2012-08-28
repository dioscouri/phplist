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

if ( !class_exists('Phplist') ) {
    JLoader::register( "Phplist", JPATH_ADMINISTRATOR.DS."components".DS."com_phplist".DS."defines.php" );
}

if(!class_exists('JFakeElementBase')) {
	if(version_compare(JVERSION,'1.6.0','ge')) {
		class JFakeElementBase extends JFormField {
			// This line is required to keep Joomla! 1.6/1.7 from complaining
			public function getInput() {
			}
		}
	} else {
		class JFakeElementBase extends JElement {}
	}
}
jimport('joomla.filesystem.file');


if (JFile::exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_phplist'.DS.'helpers'.DS.'phplist.php'))
{
	// Require Helpers
	require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_phplist'.DS.'defines.php' );
	JLoader::import( 'com_phplist.tables.newsletters', JPATH_ADMINISTRATOR.DS.'components' );
	JLoader::import( 'com_phplist.helpers.phplist', JPATH_ADMINISTRATOR.DS.'components' );
	JLoader::import( 'com_phplist.helpers.message', JPATH_ADMINISTRATOR.DS.'components' );
	JLoader::import( 'com_phplist.helpers.newsletter', JPATH_ADMINISTRATOR.DS.'components' );
	JLoader::import( 'com_phplist.library.select', JPATH_ADMINISTRATOR.DS.'components' );

	

if (!defined('PhplistHelperFileExists')) {
	DEFINE( "PhplistHelperFileExists", '1');
	}
}


class JFakeElementPhplistNewsletter extends JFakeElementBase
{

	var	$_name = 'PhplistNewsletter';
	
	public function getInput()
	{
		return PhplistSelect::newsletter( $this->value, $this->options['control'].$this->name, ' size="5" ', 'id', true );
	}
	
	function fetchElement($name, $value, &$node, $control_name)
	{	
		return PhplistSelect::newsletter( @$value, $control_name.'['.$name.']', @$attribs, 'id', true );
	}
}

if(version_compare(JVERSION,'1.6.0','ge')) {
	class JFormFieldPhplistNewsletter extends JFakeElementPhplistNewsletter {}
} else {
	class JElementPhplistNewsletter extends JFakeElementPhplistNewsletter {}
}
?>