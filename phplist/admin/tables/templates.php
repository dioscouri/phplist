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

class PhplistTableTemplates extends DSCTable
{
	function PhplistTableTemplates( &$db ) 
	{
		$database = PhplistHelperPhplist::getDatabase();
		
		$tbl_key 	= 'id';
		$tbl_suffix = 'template';
		$this->set( '_suffix', $tbl_suffix );
		
		$tablename = PhplistHelperTemplate::getTableName();
		
		parent::__construct( $tablename, $tbl_key, $database );			
	}
}

?>