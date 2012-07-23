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

Phplist::load( 'PhplistViewBase', 'views.base' );

class PhplistViewMessages extends PhplistViewBase
{
    
    function _form($tpl=null)
    {
    	parent::_form($tpl);

    	//get newsletters
		$model = JModel::getInstance( 'Newsletters', 'PhplistModel' );
		$model->setState( 'order', 'name' );
		$model->setState( 'direction', 'ASC' );
		$items = $model->getList();
		$this->assign( 'newsletters', $items );
		
		//get templates
		$model = JModel::getInstance( 'Templates', 'PhplistModel' );
		$templates = $model->getList();
		$this->assign( 'templates', $templates );
		
		//get default footer if one hasn't been entered.
		$footer = PhplistHelperMessage::getDefaultFooter();
		$this->assign( 'footer', $footer->value );
    }
}

?>