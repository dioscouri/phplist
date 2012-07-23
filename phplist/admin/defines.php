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
	protected $_phplistrecommended = '2.10.12';

	public $show_linkback					= '1';
	public $show_linkback_phplist			= '1';
	public $activation_email				= '0';
	public $default_html					= '1';
	public $users_autocreate				= '0';
	public $frontend_attribs				= '0';
	public $default_template				= '0';
	public $default_fromemail				= '';
	public $display_submenu					= '1';
	public $display_search					= '1';
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
	public $page_tooltip_dashboard_disabled	= '0';
	public $page_tooltip_config_disabled		= '0';
	public $page_tooltip_users_disabled		= '0';
	public $page_tooltip_subscriptions_disabled	= '0';
	public $page_tooltip_newsletters_disabled		= '0';
	public $page_tooltip_messages_disabled			= '0';
	public $page_tooltip_attributes_disabled		= '0';
	public $page_tooltip_logs_disabled				= '0';
	public $page_tooltip_tools_disabled  			= '0';
	public $page_tooltip_phplistconfig_disabled  	= '0';
	
	/**
	 * Get recomended PHPList Version
	
	public function getPHPListRecomendedVersion()
	{
		return $this->get('_phplistrecommended');
	}
	
	/**
	 * Get actual PHPList Version
	 
	public static function getPHPListVersion()
	{
		$database = PhplistHelperPhplist::getDBO();
		if (isset($database->error))
		{
			return;
		}
	
		$config = PhplistConfigPhplist::getInstance();
		$_phplistversion = $config->get( 'version', '' );
	
		//get version from PHPlist config
		$recommended = self::getPHPListRecomendedVersion();
		$passes = false;
	
		if ($_phplistversion[0] >= $recommended[0]) {
			if ($_phplistversion[2] >= $recommended[2]) {
				if ($_phplistversion[5] >= $recommended[5]) {
					if ($_phplistversion[6] >= $recommended[6]) {
						$passes = true;
					}
				}
			}
		}
		//if it doesn't pass raise a Joomla Notice (only on Configuration page)
		if (!$passes && JRequest::getVar('view')== 'config') :
		JError::raiseNotice('VERSION_ERROR',sprintf(JText::_('ERROR_PHPLIST_VERSION'),$recommended,$_phplistversion));
		endif;
	
		//return recommended PHPList version
		return $_phplistversion;
	}
	
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
	public static function getClass( $classname, $filepath='controller', $options=array( 'site'=>'admin', 'type'=>'components', 'ext'=>'com_phplist' )  )
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
	public static function load( $classname, $filepath='controller', $options=array( 'site'=>'admin', 'type'=>'components', 'ext'=>'com_phplist' ) )
	{
		return parent::load( $classname, $filepath, $options  );
	}
}
class PhplistConfigPhplist extends DSC
{
	//TODO set up phplist config vars
	var $show_linkback					= '1';
	var $version					= '';


	/**
	 * constructor
	 * @return void
	 */
	function __construct() {
		parent::__construct();

		$this->setVariables();
	}

	/**
	 * Returns the query
	 * @return string The query to be used to retrieve the rows from the database
	 */
	function _buildQuery() {
		$tablename = PhplistHelperConfigPhplist::getTableName();
		$query = "SELECT * FROM {$tablename}";

		return $query;
	}

	/**
	 * Retrieves the data
	 * @return array Array of objects containing the data from the database
	 */
	function getData() {
		// load the data if it doesn't already exist
		if (empty( $this->_data )) {
			$database = PhplistHelperPhplist::getDatabase();
			$query = $this->_buildQuery();
			$database->setQuery( $query );
			$this->_data = $database->loadObjectList( );
		}

		return $this->_data;
	}

	/**
	 * Set Variables
	 *
	 * @acces	public
	 * @return	object
	 */
	function setVariables() {
		$success = false;

		if ( $data = $this->getData() ) {
			for ($i=0; $i<count($data); $i++) {
				$title = $data[$i]->item;
				$value = $data[$i]->value;
				if (isset($title)) {
					$this->$title = $value;
				}
			}
				
			$success = true;
		}

		return $success;
	}

	/**
	 * Get component config
	 *
	 * @acces	public
	 * @return	object
	 */
	function &getInstance() {
		static $instance;

		if (!is_object($instance)) {
			$instance = new PhplistConfigPhplist();
		}

		return $instance;
	}
}
	
?>