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

class PhplistViewPreferences extends PhplistViewBase 
{
	
	/**
	 * 
	 * @return void
	 **/
	function _default($tpl = null) 
	{
		parent::_default($tpl);
		
		$model = JModel::getInstance( 'Preferences', 'PhplistModel' );
		$row = $model->getTable();
		
		$redirect = "index.php?option=com_phplist&view=newsletter";
		$this->messagetype  = 'notice';
		
		if ($uid = PhplistHelperUser::getUid()) {
			$phplistUser = PhplistHelperUser::getUser( $uid, '1', 'uid' );
			$this->assign('email',$phplistUser->email);
			$row->load( $phplistUser->id);
		}
		else
		{
			$this->assign('email',JFactory::getUser()->email);
			$row->load(  JFactory::getUser()->id, 'foreignkey' );
		}
		$this->assign('row', $row);
		$attributes = PhplistHelperAttribute::getUserAttributes($row->id, $frontend='1');
		$this->assign( 'attributes', $attributes );
		$this->assign( 'uid', $uid );
		
    }
}

?>