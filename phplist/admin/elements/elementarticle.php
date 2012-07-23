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

if ( !class_exists('Phplist') ) {
    JLoader::register( "Phplist", JPATH_ADMINISTRATOR.DS."components".DS."com_mediamanager".DS."defines.php" );
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

class JFakeElementPhplistArticle extends JFakeElementBase
{
	var	$_name = 'PhplistArticle';

	public function getInput() 
	{
		$html = "";
		
		JModel::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_mediamanager/models' );
		$model = JModel::getInstance( 'ElementArticle', 'PhplistModel' );
		$html = $model->fetchElement($this->id, (int) $this->value, '', '', $this->name);
		
		return $html;
	}
	
	public function fetchElement($name, $value, &$node, $control_name)
	{
		$html = "";
		
		JModel::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_mediamanager/models' );
		$model = JModel::getInstance( 'ElementArticle', 'PhplistModel' ); 
		$html = $model->fetchElement($name, $value, $control_name );
		return $html;
	}
}

if(version_compare(JVERSION,'1.6.0','ge')) {
	class JFormFieldPhplistArticle extends JFakeElementPhplistArticle {}
} else {
	class JElementPhplistArticle extends JFakeElementPhplistArticle {}
}
?>