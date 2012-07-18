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

class PhplistUrl extends PhplistHelperBase
{
	
	/**
	 * Wrapper that adds the current Itemid to the URL
	 * @param	string $string The string to translate
	 *
	 */
	function &_( $url, $text, $params='', $xhtml=true, $ssl=null, $addItemid='1' ) {
		if ($addItemid == '1') { $url = PhplistUrl::addItemid($url); }
		$return = "<a href='".JRoute::_($url, $xhtml, $ssl)."' ".addslashes($params)." >".$text."</a>";
		return $return;			
	}

	/**
	 * Wrapper that adds the current Itemid to the URL
	 * @param	string $string The string to translate
	 *
	 */
	function &addItemid( $url ) {
		global $Itemid;
		$return = $url;
		$return.= "&Itemid=".$Itemid;
		return $return;			
	}

	/**
	 * Wrapper that adds the current Itemid to the URL
	 * @param	string $string The string to translate
	 *
	 */
	function &popup( $url, $text, $width=640, $height=480, $top=0, $left=0, $class='' ) {
		$html = "";
		JHTML::_('behavior.modal');
		
		$doTask	= $url;

		$html	= "<a class=\"modal\" href=\"$doTask\" rel=\"{handler: 'iframe', size: {x: $width, y: $height}}\">\n";
		$html 	.= "<span class=\"$class\" title=\"$text\">\n";
		$html 	.= "</span>\n";
		$html	.= "$text\n";
		$html	.= "</a>\n";
		
		return $html;
	}
	
    /**
     * Retrieves the Itemid
     *
     * @access public
     */
	function getItemid( $link='index.php?option=com_phplist' ) {
		$id = "";
		$database = JFactory::getDBO();
				
		$query = "
			SELECT 
				* 
			FROM 
				#__menu
			WHERE 
				`link` LIKE '%".$link."%'
			AND
				`published` > '0'
			ORDER BY 
				`link` ASC 
		";
	
		$database->setQuery($query);
		if ( $item = $database->loadObject() ) {
			$id = $item->id;		
		}
		return $id;		
	}

}