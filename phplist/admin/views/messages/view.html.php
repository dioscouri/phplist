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
	/**
	 * 
	 * @param $tpl
	 * @return unknown_type
	 */
	function getLayoutVars($tpl=null)
	{
		$layout = $this->getLayout();
		switch(strtolower($layout))
		{
			case "view":
				$this->_form($tpl);
			  break;
			case "form":
			case "testemail":
				JRequest::setVar('hidemainmenu', '1');
				$this->_form($tpl);
			  break;
			case "default":
			default:
				$this->_default($tpl);
			  break;
		}
	}
    
    function _form($tpl=null)
    {
    	parent::_form($tpl);
		
    	//get newsletters
		$model = JModel::getInstance( 'Newsletters', 'PhplistModel' );
		$model->setState( 'order', 'name' );
		$model->setState( 'direction', 'ASC' );
		$items = $model->getList();
		$this->assign( 'newsletters', $items );
		
		// content article
		$elementArticleModel = JModel::getInstance( 'ElementArticle', 'PhplistModel' );
		$this->assign( 'elementArticleModel', $elementArticleModel );
		
		//get templates
		$model = JModel::getInstance( 'Templates', 'PhplistModel' );
		$templates = $model->getList();
		$this->assign( 'templates', $templates );
		
		//get default footer if one hasn't been entered.
		$footer = PhplistHelperMessage::getDefaultFooter();
		$this->assign( 'footer', $footer );
		
   		//if new message, get config params and set defaults
    	if (!JRequest::getVar('id'))
    	{
			$config = Phplist::getInstance();
			$this->row->fromfield = $config->get( 'default_fromemail', '1' );
			$this->row->template = $config->get( 'default_template', '1' );
    	}
    }
    
	/**
	 *
	 * @return
	 * @param object $name
	 * @param object $value[optional]
	 * @param object $node[optional]
	 * @param object $control_name[optional]
	 */
	function _fetchElement($name, $value='', $node='', $control_name='')
	{		
		$mainframe =JFactory::getApplication();

		$db			= JFactory::getDBO();
		$doc 		=& JFactory::getDocument();
		$template 	= $mainframe->getTemplate();
		$fieldName	= $control_name ? $control_name.'['.$name.']' : $name;
		$article =& JTable::getInstance('content');
		if ($value) {
			$article->load($value);
			$title = $article->title;
		} else {
			$title = JText::_('Select an Article');
		}
		
		$link = 'index.php?option='.'com_phplist'.'&task=elementArticle&tmpl=component&object='.$name;

		JHTML::_('behavior.modal', 'a.modal');
		$html = '<div class="button2-left"><div class="blank"><a class="modal" title="'.JText::_('Select and Article and it will be inserted where your cursor is placed').'"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 800, y: 500}}">'.JText::_('Select an Article').'</a></div></div>'."\n";
		$html .= "\n".'<input type="hidden" id="'.$name.'_id" name="'.$fieldName.'" value="'.(int)$value.'" />';

		return $html;
	}
	
	function _defaultToolbar()
	{
		JToolBarHelper::custom('send_test', "send.png", "icon-32-send.png", JText::_( 'SEND_TEST_EMAIL' ), true);
		JToolBarHelper::divider();
		parent::_defaultToolbar();
	}
	
	function _formToolbar()
	{
		$layout = $this->getLayout();
		if ($layout == 'testemail')
		{
			JToolBarHelper::cancel();
			JToolBarHelper::custom('send_test_email', "forward.png", "icon-32-forward.png", JText::_( 'SEND_TEST_EMAIL' ), false);
		}
		else
		{
			parent::_formToolbar();
		}
	}
}

?>