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

class PhplistControllerSubscriptions extends PhplistController
{
	/**
	 * constructor
	 */
	function __construct() 
	{
		parent::__construct();
		
		$this->set('suffix', 'subscriptions');
		
		// Register Extra tasks
		$this->registerTask( 'confirmed.enable', 'boolean' );
		$this->registerTask( 'confirmed.disable', 'boolean' );
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
    function _setModelState()
    {
    	$state = parent::_setModelState();   	
		$app = JFactory::getApplication();
		$model = $this->getModel( $this->get('suffix') );
    	$ns = $this->getNamespace();
    	
        $state['order']     = $app->getUserStateFromRequest($ns.'.filter_order', 'filter_order', 'tbl.entered', 'cmd');
        $state['direction'] = $app->getUserStateFromRequest($ns.'.filter_direction', 'filter_direction', 'DESC', 'word');
    	
        $state['filter_email']   = $app->getUserStateFromRequest($ns.'email', 'filter_email', '', '');
        $state['filter_id_from']    = $app->getUserStateFromRequest($ns.'id_from', 'filter_id_from', '', '');
        $state['filter_id_to']      = $app->getUserStateFromRequest($ns.'id_to', 'filter_id_to', '', '');
        
        $state['filter_joomlaid_from']    = $app->getUserStateFromRequest($ns.'joomlaid_from', 'filter_joomlaid_from', '', '');
        $state['filter_joomlaid_to']      = $app->getUserStateFromRequest($ns.'joomlaid_to', 'filter_joomlaid_to', '', '');
        
      	$state['filter_listid'] 	= $app->getUserStateFromRequest($ns.'listid', 'filter_listid', '', '');
      	$state['filter_confirmed'] 	= $app->getUserStateFromRequest($ns.'confirmed', 'filter_confirmed', '', '');
      	
        $state['filter_date_from'] = $app->getUserStateFromRequest($ns.'date_from', 'filter_date_from', '', '');
        $state['filter_date_to'] = $app->getUserStateFromRequest($ns.'date_to', 'filter_date_to', '', '');
      	
    	foreach (@$state as $key=>$value)
		{
			$model->setState( $key, $value );	
		}
  		return $state;
    }
	
	/**
	 * 
	 * @return 
	 * @param $msg Object
	 */	
	function unsubscribe() 
	{	
		$error = false;
		$this->messagetype	= '';
		$this->message 		= '';
		$redirect = 'index.php?option=com_phplist&view='.$this->get('suffix');
		$redirect = JRoute::_( $redirect, false );
		
		$listid = JRequest::getVar( 'listid' );
		$phplistuserid = JRequest::getVar( 'phplistuserid' );
		
		if (!$listid || !$phplistuserid) 
		{
			$this->messagetype = 'notice';
			$this->message .= JText::_( "UNSUBSCRIBE FAILED" );
			$this->setRedirect( $redirect, $this->message, $this->messagetype );
			return;
		}
		
		$details = new JObject();
		$details->userid = $phplistuserid;
		$details->listid = $listid;
		$email = PhplistHelperUser::getUser($phplistuserid, '1', 'id')->email;
		
		$action = PhplistHelperSubscription::removeUserFrom( $details );

		if (!$action)
		{
			$this->messagetype = 'notice';
			$this->message .= ' - '.$action->errorMsg;
		}
		else
		{
			$this->messagetype = 'message';
			$this->message .= $email . ' ' .JText::_( "SUCCESSFULLY UNSUBSCRIBED FROM NEWSLETTER" ) . ' ' . $listid;
		}
		
		$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}
	
   	/*
     * Deletes record(s) and redirects to default layout
     */
    function delete()
    {
        $error = false;
        $this->messagetype  = '';
        $this->message      = '';
        $redirect = JRequest::getVar( 'return' ) ?  
        base64_decode( JRequest::getVar( 'return' ) ) : 'index.php?option=com_phplist&view='.$this->get('suffix');
        $redirect = JRoute::_( $redirect, false );

        $cids = JRequest::getVar('cid', array (0), 'request', 'array');
        foreach (@$cids as $cid)
        {
        	$getdetails = explode( ',', trim($cid) );
        	$details = new JObject();
			$details->userid = $getdetails[0];
			$details->listid = $getdetails[1];
        	$action = PhplistHelperSubscription::removeUserFrom( $details );
            if (!$action)
            {
                $this->message .= $cid.',';
                $this->messagetype = 'notice';
                $error = true;
            }
        }

        if ($error)
        {
            $this->message = JText::_('UNSUBSCRIBE FAILED') . " - " . $this->message;
        }
            else
        {
            $this->message = JText::_('SELECTED ITEMS SUCCESSFULLY UNSUBSCRIBED');
        }

        $this->setRedirect( $redirect, $this->message, $this->messagetype );
    }
}

?>