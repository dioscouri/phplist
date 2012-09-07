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

class PhplistControllerPreferences extends PhplistController 
{
	/**
	 * constructor
	 */
	function __construct() 
	{
		parent::__construct();
		$this->set('suffix', 'preferences');
	}
	/**
	 * save a record
	 * @return void
	 */
	function save() 
	{
        $model  = $this->getModel( $this->get('suffix') );        
        $row = $model->getTable();
        if (JFactory::getUser()->id) {
        	//load logged-in joomla user
        	$row->load( JFactory::getUser()->id , 'foreignkey' );
        }
        else {
        	$uid = JRequest::getVar( 'uid' );
			$phplistUser = PhplistHelperUser::getUser( $uid, '1', 'uid' );
			$row->load( $phplistUser->id);
		}
        
        $redirect = "index.php?option=com_phplist&view=newsletters" ;
        
        $row->htmlemail = JRequest::getVar( 'htmlemail' );
        $row->email = JRequest::getVar( 'email' );
        $uid = JRequest::getVar( 'uid' );
        if ( $row->save() ) 
        {
            $model->setId( $row->id );
            $this->messagetype  = 'message';
            $this->message      = JText::_( 'PREFERENCES_SAVED');
            
            $dispatcher = JDispatcher::getInstance();
            $dispatcher->trigger( 'onAfterSave'.$this->get('suffix'), array( $row ) );
        } 
            else 
        {
            $this->messagetype  = 'notice';         
            $this->message      = JText::_( 'SAVE_FAILED' )." - ".$row->getError();
        }
		
        // update attributes
	    $saveattributes = PhplistHelperAttribute::saveAttributes($row->id);
        
        $redirect = JRoute::_( PhplistUrl::siteLink($redirect), false );
        $this->setRedirect( $redirect, $this->message, $this->messagetype );

	}
	
	/**
	 * cancel redirects to main newsletters page
	 * @return void
	 */
	function cancel() 
	{		
		$this->messagetype = 'message';
		$this->message = JText::_( 'CANCEL_PREFS_MESSAGE' );
		$redirect = "index.php?option=com_phplist&view=newsletters";
		$redirect = JRoute::_( PhplistUrl::siteLink($redirect), false );
        $this->setRedirect( $redirect, $this->message, $this->messagetype );
	}
}

?>