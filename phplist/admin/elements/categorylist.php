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
/**
 * Renders a category element
 *
 * @package 	Joomla.Framework
 * @subpackage		Parameter
 * @since		1.5
 */

class JFakeElementCategorylist extends JFakeElementBase
{
	var	$_name = 'Categorylist';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$db = &JFactory::getDBO();

		$section	= $node->attributes('section');
		$class		= $node->attributes('class');
		$size = ( $node->attributes('size') ? $node->attributes('size') : 5 );
		if (!$class) {
			$class = "inputbox";
		}

		if (!isset ($section)) {
			// alias for section
			$section = $node->attributes('scope');
			if (!isset ($section)) {
				$section = 'content';
			}
		}

		if ($section == 'content') {
			// This might get a conflict with the dynamic translation - TODO: search for better solution
			$query = 'SELECT c.id, CONCAT_WS( "/",s.title, c.title ) AS title' .
				' FROM #__categories AS c' .
				' LEFT JOIN #__sections AS s ON s.id=c.section' .
				' WHERE c.published = 1' .
				' AND s.scope = '.$db->Quote($section).
				' ORDER BY s.title, c.title';
		} else {
			$query = 'SELECT c.id, c.title' .
				' FROM #__categories AS c' .
				' WHERE c.published = 1' .
				' AND c.section = '.$db->Quote($section).
				' ORDER BY c.title';
		}
		$db->setQuery($query);
		$options = $db->loadObjectList();
		array_unshift($options, JHTML::_('select.option', '0', '- '.JText::_('All Categories').' -', 'id', 'title'));

		return JHTML::_('select.genericlist',  $options, ''.$control_name.'['.$name.'][]', ' size="' . $size . '" ', 'id', 'title', $value, $control_name.$name );
	}
}

if(version_compare(JVERSION,'1.6.0','ge')) {
	class JFormFieldCategorylist extends JFakeElementCategorylist {}
} else {
	class JElementCategorylist extends JFakeElementCategorylist {}
}

?>