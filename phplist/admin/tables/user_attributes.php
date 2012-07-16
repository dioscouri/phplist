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

JLoader::import( 'com_phplist.tables._base', JPATH_ADMINISTRATOR.DS.'components' );

class TableUserAttributes extends PhplistTable
{
	function TableUserAttributes( &$db ) 
	{
		$tbl_key 	= 'id';
		$tbl_suffix = 'attributes';
		$this->set( '_suffix', $tbl_suffix );
		
		JLoader::import( 'com_phplist.helpers.phplist', JPATH_ADMINISTRATOR.DS.'components' );
		JLoader::import( 'com_phplist.helpers.attribute', JPATH_ADMINISTRATOR.DS.'components' );
		$database = PhplistHelperPhplist::getDatabase();		
		$tablename = PhplistHelperAttribute::getTableName_userattributes();
		
		parent::__construct( $tablename, $tbl_key, $database );			
	}
}

?>