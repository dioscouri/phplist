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

class PhplistTableSubscriptions extends DSCTable
{
	
	function PhplistTableSubscriptions( &$db )
	{
		$database = PhplistHelperPhplist::getDatabase();
		
		$tbl_key 	= 'userid';
		$tbl_suffix = 'subscriptions';
		$this->set( '_suffix', $tbl_suffix );
		
		$tablename = PhplistHelperSubscription::getTableName();
		
		parent::__construct( $tablename, $tbl_key, $database );			
	}
}

?>