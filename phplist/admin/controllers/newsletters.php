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

class PhplistControllerNewsletters extends PhplistController
{	
	/**
	 * constructor 
	 */
	function __construct()
	{
		parent::__construct();
		
		$this->set('suffix', 'newsletters');
		$this->registerTask( 'active.enable', 'boolean' );
		$this->registerTask( 'active.disable', 'boolean' );
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
    	$state['filter_id_from']    = $app->getUserStateFromRequest($ns.'id_from', 'filter_id_from', '', '');
        $state['filter_id_to']      = $app->getUserStateFromRequest($ns.'id_to', 'filter_id_to', '', '');
      	$state['filter_active'] 	= $app->getUserStateFromRequest($ns.'active', 'filter_active', '', '');
        $state['filter_name']   = $app->getUserStateFromRequest($ns.'name', 'filter_name', '', '');
        
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
			$this->message = JText::_('ORDERING FAILED') . " - " . $this->message;
		}
			else
		{
			$this->message = JText::_('ITEMS ORDERED');
		}
		
		$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}
	
    /**
     * Saves an item and redirects based on task
     * @return void
     */
    function save()
    {
        $model  = $this->getModel( $this->get('suffix') );

        $row = $model->getTable();
        $row->load( $model->getId() );
        $row->bind( $_POST );
        
        $config =& JFactory::getConfig();
		$date = JFactory::getDate();
		$date->setOffset($config->getValue('config.offset'));
        $row->entered = $date->toMySQL(true);
        $row->owner = '0';
        
    	// Stripslashes - for magic quotes on
   		if (get_magic_quotes_gpc()) {
       		$row->name = stripslashes($row->name);
       		$row->description = stripslashes($row->description);
   		}

        if ( $row->save() )
        {
            $model->setId( $row->id );
            $this->messagetype  = 'message';
            $this->message      = JText::_( 'SAVED' );

            $dispatcher = JDispatcher::getInstance();
            $dispatcher->trigger( 'onAfterSave'.$this->get('suffix'), array( $row ) );
        }
            else
        {
            $this->messagetype  = 'notice';
            $this->message      = JText::_( 'SAVE FAILED' )." - ".$row->getError();
        }

        $redirect = "index.php?option=com_phplist";
        $task = JRequest::getVar('task');
        switch ($task)
        {
            case "savenew":
                $redirect .= '&view='.$this->get('suffix').'&task=add';
              break;
            case "apply":
                $redirect .= '&view='.$this->get('suffix').'&task=edit&id='.$model->getId();
              break;
            case "save":
            default:
                $redirect .= "&view=".$this->get('suffix');
              break;
        }
    

        $redirect = JRoute::_( $redirect, false );
        $this->setRedirect( $redirect, $this->message, $this->messagetype );
    }
}

?>