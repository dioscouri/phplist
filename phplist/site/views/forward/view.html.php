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

class PhplistViewForward extends PhplistViewBase
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
			case "default":
			default:
				$this->_default($tpl);
				break;
		}
		parent::display($tpl);
	}
	
	/**
	 *
	 * @return void
	 **/
	function _default($tpl = null)
	{
		parent::_default($tpl);
	
		$model = JModel::getInstance( 'Forward', 'PhplistModel' );
		$row = $model->getTable();
	
		$redirect = "index.php?option=com_phplist&view=newsletter";
		$this->messagetype  = 'notice';
	
		if ($uid =  JRequest::getVar( 'uid' )) {
			$phplistUser = PhplistHelperUser::getUser( $uid, '1', 'uid' );
	
			$this->assign('email',$phplistUser->email);
			$row->load( $phplistUser->id );
		}
		else
		{
			$this->assign('email',JFactory::getUser()->email);
			$row->load( JFactory::getUser()->id, 'foreignkey' );
		}
		$this->assign('row', $row);
	
		//TODO error if mid not in url.
		$messageid =  JRequest::getVar( 'mid' );
		$this->assign('messageid',$messageid);
	
		$message = PhplistHelperMessage::getMessage( $messageid );
		$this->assign('subject',$message->subject);
	}
}