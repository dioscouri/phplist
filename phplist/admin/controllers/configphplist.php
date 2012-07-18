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

class PhplistControllerConfigPhplist extends PhplistController 
{
	/**
	 * constructor
	 */
	function __construct() 
	{
		parent::__construct();
		
		$this->set('suffix', 'configphplist');
	}
	
	/**
	 * saves the phplist config records
	 * @return void
	 */
	function save() 
	{
		$error = false;
		$errorMsg = "";
		$model 	= $this->getModel( $this->get('suffix') );
		$config = PhplistConfigPhplist::getInstance();
		$properties = $config->getProperties();
		 
		foreach (@$properties as $key => $value ) 
		{
			unset($row);
			$row = $model->getTable();
			$newvalue = JRequest::getVar( $key );
			$value_exists = array_key_exists( $key, $_POST );

			if ( $value_exists && !empty($key) ) 
			{ 
				// proceed if newvalue present in request. prevents overwriting for non-existent values.
				$row->load( $key );
				$row->item = $key;
				$row->value = $newvalue;
				if ( !$row->save() ) 
				{
					$error = true;
					$errorMsg .= $key." - ".$row->getError();	
				}
			}
		}
		
		if ( !$error ) 
		{
			$this->messagetype 	= 'message';
			$this->message  	= JText::_( 'CONFIGURATION SAVED' );
			
			$dispatcher = JDispatcher::getInstance();
			$dispatcher->trigger( 'onAfterSave'.$this->get('suffix'), array( $row ) );
		} 
			else 
		{
			$this->messagetype 	= 'notice';			
			$this->message 		= JText::_( 'SAVE FAILED' )." - ".$errorMsg;
		}
		
    	$redirect = "index.php?option=com_phplist";
    	$task = JRequest::getVar('task');
    	switch ($task)
    	{
    		default:
    			$redirect .= "&view=".$this->get('suffix');
    		  break;
    	}

    	$redirect = JRoute::_( $redirect, false );
		$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}
}
?>