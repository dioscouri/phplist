<?php
/**
* @version		1.5
* @package		Phplist
* @copyright	Copyright (C) 2009 DT Design Inc. All rights reserved.
* @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
* @link 		http://www.dioscouri.com
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class Phplist extends DSC
{
	protected $_name           = 'phplist';
	protected $_version        = '2.2.0';
	protected $_build          = '';
	protected $_versiontype    = '';
	protected $_copyrightyear  = '2012';
	protected $_min_php		= '5.2';

	public $show_linkback					= '1';
	public $show_linkback_phplist			= '1';
	public $activation_email				= '0';
	public $default_html					= '1';
	public $users_autocreate				= '0';
	public $frontend_attribs				= '0';
	public $display_submenu					= '1';
	public $display_newsletter_order		= 'lastsent';
	public $display_newsletter_order_dir	= 'ASC';
	public $display_messagetemplate			= '0';
	public $phplist_host					= 'localhost';
	public $phplist_database				= 'phplist';
	public $phplist_user					= 'phplist';
	public $phplist_password				= '';
	public $phplist_prefix					= 'phplist';
	public $phplist_user_prefix				= 'phplist_user';
	public $phplist_driver					= 'mysql';
	public $phplist_port					= '3600';
	
	//TODO Tooltips should be here...

	/**
	 * Returns the query
	 * @return string The query to be used to retrieve the rows from the database
	 */
	public function _buildQuery()
	{
		$query = "SELECT * FROM #__phplist_config";
		return $query;
	}
	
	/**
	 * Get component config
	 *
	 * @acces	public
	 * @return	object
	 */
	public static function getInstance()
	{
		static $instance;
	
		if (!is_object($instance)) {
			$instance = new Phplist();
		}
	
		return $instance;
	}
	
	/**
	 * Intelligently loads instances of classes in framework
	 *
	 * Usage: $object = Synk::getClass( 'SynkHelperCarts', 'helpers.carts' );
	 * Usage: $suffix = Synk::getClass( 'SynkHelperCarts', 'helpers.carts' )->getSuffix();
	 * Usage: $categories = Synk::getClass( 'SynkSelect', 'select' )->category( $selected );
	 *
	 * @param string $classname   The class name
	 * @param string $filepath    The filepath ( dot notation )
	 * @param array  $options
	 * @return object of requested class (if possible), else a new JObject
	 */
	public static function getClass( $classname, $filepath='controller', $options=array( 'site'=>'admin', 'type'=>'components', 'ext'=>'com_synk' )  )
	{
		return parent::getClass( $classname, $filepath, $options  );
	}
	
	/**
	 * Method to intelligently load class files in the framework
	 *
	 * @param string $classname   The class name
	 * @param string $filepath    The filepath ( dot notation )
	 * @param array  $options
	 * @return boolean
	 */
	public static function load( $classname, $filepath='controller', $options=array( 'site'=>'admin', 'type'=>'components', 'ext'=>'com_synk' ) )
	{
		return parent::load( $classname, $filepath, $options  );
	}
}
	
?>