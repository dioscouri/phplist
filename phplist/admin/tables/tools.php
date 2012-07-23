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

class PhplistTableTools extends DSCTable 
{
	/**
	 * Could this be abstracted into the base?
	 * 
	 * @param $db
	 * @return unknown_type
	 */
	function PhplistTableTools ( &$db ) 
	{
	    if(version_compare(JVERSION,'1.6.0','ge')) {
            // Joomla! 1.6+ code here
            $tbl_key 	= 'extension_id';
            $tbl_suffix = 'extensions';
        } else {
            // Joomla! 1.5 code here
            $tbl_key 	= 'id';
            $tbl_suffix = 'plugins';
        }
		
		$this->set( '_suffix', $tbl_suffix );
		$name 		= "phplist";
		
		parent::__construct( "#__{$tbl_suffix}", $tbl_key, $db );	
	}
	
	function check()
	{		
		return true;
	}
}

?>