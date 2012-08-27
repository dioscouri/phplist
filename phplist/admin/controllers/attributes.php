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

class PhplistControllerAttributes extends PhplistController
{
	/**
	 * constructor
	 */
	function __construct() 
	{
		parent::__construct();
		
		$this->set('suffix', 'attributes');	
		$this->registerTask( 'required.enable', 'boolean' );
		$this->registerTask( 'required.disable', 'boolean' );
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

        $state['order']     = $app->getUserStateFromRequest($ns.'.filter_order', 'filter_order', 'tbl.listorder', 'cmd');
        $state['direction'] = $app->getUserStateFromRequest($ns.'.filter_direction', 'filter_direction', 'ASC', 'word');
      	$state['filter_type'] 	= $app->getUserStateFromRequest($ns.'type', 'filter_type', '', '');
        $state['filter_id_from']    = $app->getUserStateFromRequest($ns.'id_from', 'filter_id_from', '', '');
        $state['filter_id_to']      = $app->getUserStateFromRequest($ns.'id_to', 'filter_id_to', '', '');
     	$state['filter_name']   = $app->getUserStateFromRequest($ns.'name', 'filter_name', '', '');
     	$state['filter_active'] 	= $app->getUserStateFromRequest($ns.'required', 'filter_active', '', '');
     	
    	foreach (@$state as $key=>$value)
		{
			$model->setState( $key, $value );	
		}
  		return $state;
    }
    
	/**
	 * save ordering
	 * @return void
	 */
	function ordering() 
	{
		$error = false;
		$this->messagetype	= '';
		$this->message 		= '';
		$redirect = 'index.php?option=com_phplist&view='.$this->get('suffix');
		$redirect = JRoute::_( $redirect, false );
				
		$model = $this->getModel($this->get('suffix'));
		$row = $model->getTable();
		
		$ordering = JRequest::getVar('ordering', array(0), 'post', 'array');
		$cids = JRequest::getVar('cid', array (0), 'post', 'array');
		foreach (@$cids as $cid)
		{
			$row->load( $cid );
			$row->listorder = @$ordering[$cid];
			
			if (!$row->store())
			{
				$this->message .= $row->getError();
				$this->messagetype = 'notice';
				$error = true;
			}
		}
		
		$row->reorder();
		
		if ($error)
		{
			$this->message = JText::_('ORDERING_FAILED') . " - " . $this->message;
		}
		else
		{
			$this->message = JText::_('ITEMS_ORDERED');
		}
		
		$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}
}

?>