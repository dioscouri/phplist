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
	JLoader::register( "Phplist", JPATH_ADMINISTRATOR.DS."components".DS."com_phplist".DS."defines.php" );
}

Phplist::load( "PhplistHelperRoute", 'helpers.route' );

/**
 * Build the route
 * Is just a wrapper for PhplistHelperRoute::build()
 *
 * @param unknown_type $query
 * @return unknown_type
 */
function PhplistBuildRoute(&$query)
{
	return PhplistHelperRoute::build($query);
}

/**
 * Parse the url segments
 * Is just a wrapper for PhplistHelperRoute::parse()
 *
 * @param unknown_type $segments
 * @return unknown_type
 */
function PhplistParseRoute($segments)
{
	return PhplistHelperRoute::parse($segments);
}