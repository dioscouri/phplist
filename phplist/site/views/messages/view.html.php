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

Phplist::load( 'PhplistViewBase', 'views._base', array( 'site'=>'site', 'type'=>'components', 'ext'=>'com_phplist' ) );

class PhplistViewMessages extends PhplistViewBase 
{
	/**
	 * 
	 * @param $tpl
	 * @return unknown_type
	 */
	function display($tpl=null) 
	{
		parent::display($tpl);
		$layout = $this->getLayout();
		switch(strtolower($layout))
		{
			case "view":
				$model = $this->getModel();
				$row = $model->getItem();
				break;
			case "form":
				$model = $this->getModel();
				$row = $model->getItem();
				break;
			case "default":
			default:
				break;
		}
    }
    
    function _default($tpl='')
	{
		parent::_default($tpl='');
		$model = JModel::getInstance( 'Newsletters', 'PhplistModel' );
		$id = JRequest::getVar( 'id' );
		$row = $model->getTable();
		$row->load(  $id );
		$this->assign('row', $row);
		$action = JRoute::_('index.php?option=com_phplist&view=messages&task=list&id='.$id, false);
		$this->assign('action', $action);
	} 
	
 	function _form($tpl='')
	{
		parent::_form($tpl='');
		
		$config = Phplist::getInstance();
		$message_template = $config->get('display_messagetemplate', '1');
		
		if ($this->row->template != 0 && $message_template == '1')
		{
			//load template
			$templateId =  $this->row->template;
			$model  = JModel::getInstance( 'Templates', 'PhplistModel' );
			$template = $model->getTable();
			$template->load( $templateId , 'id' );
			
			//get template html
			$templatehtml = $template->template;

			//insert content into template
			$this->row->message = str_replace("[CONTENT]", $this->row->message, $templatehtml);
		}
	}
}

?>