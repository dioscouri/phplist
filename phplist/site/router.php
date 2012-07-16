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

	/**
	 *
	 * @return
	 * @param $query Object
	 */
	function PhplistBuildRoute(&$query)
	{
		$segments = array();
		
		if(isset($query['controller']))
		{
			$segments[] = $query['controller'];
			unset($query['controller']);
		}
		
		if(isset($query['view']))
		{
			$segments[] = $query['view'];
			unset($query['view']);
		};
		
		if(isset($query['layout']))
		{
			$segments[] = $query['layout'];
			unset($query['layout']);
		};
		
		if(isset($query['task']))
		{
			$segments[] = $query['task'];
			unset($query['task']);
		};

		if(isset($query['id']))
		{
			$segments[] = $query['id'];
			unset($query['id']);
		};
		
		if(isset($query['newsletterid']))
		{
			$segments[] = $query['newsletterid'];
			unset($query['newsletterid']);
		};
		
		return $segments;
	}


	/**
	 *
	 * @return array Environment variables
	 * @param $segments Object
	 */
	function PhplistParseRoute($segments)
	{

		$vars = array();
		switch($segments[0])
		{
			case 'messages':
				$vars['view'] = 'messages';
				$vars['task'] = isset($segments['1']) ? $segments['1'] : '0';
				$vars['id'] 	= isset($segments['2']) ? $segments['2'] : '0';
				$vars['newsletterid'] = isset($segments['3']) ? $segments['3'] : '0';
			  break;
			case 'newsletters':
				$vars['view'] = 'newsletters';
			  break;
			  case 'preferences':
				$vars['view'] = 'preferences';
			  break;
			default:
				$vars['view'] = 'newsletters';
			  break;
		}
		   
		return $vars;
	}