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

JLoader::import( 'com_phplist.views._base', JPATH_SITE.DS.'components' );

class PhplistViewMessages extends PhplistViewBase 
{
	/**
	 * 
	 * @param $tpl
	 * @return unknown_type
	 */
	function display($tpl=null) 
	{
		$layout = $this->getLayout();
		switch(strtolower($layout))
		{
			case "view":
				$this->_form($tpl);
			break;
			case "list":
			case "default":
			default:
				$this->_default($tpl);
			  break;
		}
		parent::display($tpl);
    }
    
    function _default($tpl='')
	{
		parent::_default($tpl='');
		$model = JModel::getInstance( 'Newsletters', 'PhplistModel' );
		$id = JRequest::getVar( 'id' );
		$row = $model->getTable();
		$row->load(  $id );
		$this->assign('row', $row);
		$action = PhplistUrl::appendURL('index.php?option=com_phplist&view=messages&task=list&id='.$id);
		$this->assign('action', $action);
	} 
	
 	function _form($tpl='')
	{
		parent::_form($tpl='');
		
		$config = Phplist::getInstance();
		$message_template = $config->get('display_messagetemplate', '1');
		print_r($message_template);
		
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