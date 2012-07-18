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
defined( '_JEXEC' ) or die( 'Restricted access' );

JLoader::import( 'com_phplist.tables._base', JPATH_ADMINISTRATOR.DS.'components' );

class TableConfigPhplist extends PhplistTable 
{

	function TableConfigPhplist( &$db ) 
	{
		$tbl_key 	= 'item';
		$tbl_suffix = 'config';
		$this->set( '_suffix', $tbl_suffix );
		
		JLoader::import( 'com_phplist.helpers.configphplist', JPATH_ADMINISTRATOR.DS.'components' );
		$database = PhplistHelperPhplist::getDatabase();
		$tablename = PhplistHelperConfigPhplist::getTableName();
				
		parent::__construct( $tablename, $tbl_key, $database );			

	}
}

?>