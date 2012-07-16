<?php
/**
 * @version 1.5
 * @package Phplist
 * @author  Dioscouri Design
 * @link    http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

JLoader::import( 'com_phplist.library.plugins.report', JPATH_ADMINISTRATOR.DS.'components' );

class plgPhplistAmbrasubsSubscribers extends PhplistReportPlugin 
{
    /**
     * @var $_element  string  Should always correspond with the plugin's filename, 
     *                         forcing it to be unique 
     */
    var $_element    = 'ambrasubssubscribers';
	
	/**
	 * Constructor 
	 *
	 * @param object $subject The object to observe
	 * @param 	array  $config  An array that holds the plugin configuration
	 * @since 1.5
	 */
	function plgPhplistAmbrasubsSubscribers(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage( '', JPATH_ADMINISTRATOR );
		$element = strtolower( 'com_Phplist' );
		$this->loadLanguage( $element, JPATH_BASE );
		$this->loadLanguage( $element, JPATH_ADMINISTRATOR );
	}

	/**
	 * Tells Component that this is a valid tool
	 * @return unknown_type
	 */
	function onListToolsPhplist( $row )
	{
		$success = false;
		if ($this->_isMe($row)) 
		{
			$success = true;
		}
		return $success;	
	}

	/**
	 * Allows the plugin to prevent itself from being run for whatever reason
	 * @param $row
	 * @return unknown_type
	 */
	function onBeforeDisplayToolPhplist( $row ) 
	{
		$success = null;
		if ($this->_isMe($row)) 
		{
			$success = true;
		}
		return $success;		
	}

	/**
	 * This displays the contents of the plugin
	 * @param $row
	 * @return unknown_type
	 */
	function onDisplayToolPhplist( $row ) 
	{
		$success = false;
		if (!$this->_isMe($row)) 
		{
			return $success;
		}
		
		// Display the form
			echo $this->_renderForm();

		// Display the results after submission
			echo $this->_renderView();
			
		return $success;		
	}
	
    /**
     * Prepares the 'view' tmpl layout
     * when viewing a report
     *  
     * @return unknown_type
     */
    function _renderView()
    {
        // Check for request forgeries
        // and that form has been submitted
        if (!JRequest::checkToken()) 
        {
            $html = "";
            return $html;
        }
        
        $vars = new JObject();
        $vars->items = $this->_executeTool(); 
        
        $html = $this->_getLayout('view', $vars);
        
        return $html;
    }

	/**
	 * This performs the action for the tool
	 *  
	 * @return 
	 */	
	function _executeTool() 
	{
		// get the id nums
		$ambrasubs_typeid = JRequest::getVar('ambrasubs_typeid');
		$phplist_newsletterid = JRequest::getVar('phplist_newsletterid');
		
        $query = "
            SELECT
                *
            FROM 
                #__ambrasubs_types AS tbl
            WHERE 
                tbl.id = '$ambrasubs_typeid'
            LIMIT 1
        ";
        
        $db = JFactory::getDBO();
        $db->setQuery($query);
        $type_table = $db->loadObject();
		
        JModel::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_phplist'.DS.'models' );
        $list_model = JModel::getInstance( 'Newsletters', 'PhplistModel' );
        $list_table = $list_model->getTable();
        $list_table->load( $phplist_newsletterid );
        
		// if both are valid, continue
		if (empty($list_table->id) || empty($type_table->id))
		{
			$v = JText::_("Invalid entries");
			return $v;
		}
				
		// select all the ambrasubs subscribers
		$query = "
            SELECT
                u.id, u.email
            FROM 
                #__ambrasubs_users2types AS u2t
            LEFT JOIN 
                #__users AS u ON u2t.userid = u.id
            WHERE 
                u2t.typeid = '$ambrasubs_typeid'
            ORDER BY 
                u.email
		";
		$db = JFactory::getDBO();
		$db->setQuery($query);
		$list = $db->loadObjectList();
		
		// foreach one, check if they're a subscriber to the phplist newsletter
		$added = array();
		JLoader::import( 'com_phplist.helpers.subscription', JPATH_ADMINISTRATOR.DS.'components' );
		JLoader::import( 'com_phplist.helpers.user', JPATH_ADMINISTRATOR.DS.'components' );
		foreach ($list as $user)
		{
			$phplistUser = PhplistHelperUser::create( $user );
			if (!$isUser = PhplistHelperSubscription::isUser( $phplistUser->id, $phplist_newsletterid ))
			{
	            // if not, add them
	            // track the email & count
				$details = new JObject();
				$details->userid = $phplistUser->id;
				$details->listid = $phplist_newsletterid;
				$details->juserid = $user->id;
				$details->email = $user->email;
				$addUser = PhplistHelperSubscription::storeUserTo( $details );
				$added[] = $details;
			}
		}
        
		return $added;
	}
}
