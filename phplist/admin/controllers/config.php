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

class PhplistControllerConfig extends PhplistController 
{
	/**
	 * constructor
	 */
	function __construct() 
	{
		parent::__construct();
		
		$this->set('suffix', 'config');
	}
	
	/**
	 * saves the config records
	 * @return void
	 */
	function save() 
	{
		$error = false;
		$errorMsg = "";
		$model 	= $this->getModel( $this->get('suffix') );
		$config = Phplist::getInstance();
		$properties = $config->getProperties();
		 
		foreach (@$properties as $key => $value ) 
		{
			unset($row);
			$row = $model->getTable( 'config' );
			$newvalue = JRequest::getVar( $key );
			$value_exists = array_key_exists( $key, $_POST );
			
			///remove trailing or preceding underscores from database prefix and usertable prefix
			if ($key == 'phplist_prefix' || $key == 'phplist_user_prefix')
			{
				$newvalue = preg_replace('/^_+/','',trim($newvalue));
				$newvalue = preg_replace('/_+$/','',$newvalue);
			}

			if ( $value_exists && !empty($key) ) 
			{ 
				// proceed if newvalue present in request. prevents overwriting for non-existent values.
				$row->load( $key );
				$row->title = $key;
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