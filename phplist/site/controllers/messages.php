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

class PhplistControllerMessages extends PhplistController 
{
	/**
	 * constructor
	 */
	function __construct()
	{
		parent::__construct();
		$this->set('suffix', 'messages');
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
    	
    	$listid = JRequest::getVar( 'id' );

    	$state['order']     = $app->getUserStateFromRequest($ns.'.filter_order', 'filter_order', 'tbl.sent', 'cmd');
        $state['direction'] = $app->getUserStateFromRequest($ns.'.filter_direction', 'filter_direction', 'DESC', 'word');
      	$state['filter_subject']   = $app->getUserStateFromRequest($ns.'subject', 'filter_subject', '', '');
        $state['filter_listid'] 	= $app->getUserStateFromRequest($ns.'listid', 'filter_listid', $listid, '');
      	$state['filter_messagestate'] 	= $app->getUserStateFromRequest($ns.'messagestate', 'filter_messagestate', 'sent', '');

    	foreach (@$state as $key=>$value)
		{
			$model->setState( $key, $value );	
		}
  		return $state;
    }
}

?>