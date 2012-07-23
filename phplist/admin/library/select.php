<?php
/**
* @version		0.1.0
* @package		Phplist
* @copyright	Copyright (C) 2009 DT Design Inc. All rights reserved.
* @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
* @link 		http://www.dioscouri.com
*/

class PhplistSelect extends DSCSelect
{

	/**
	 * @param $selected
	 * @param $name
	 * @param $attribs
	 * @param $idtag
	 * @param $allowAny
	 * @return unknown_type
	 */
	public static function newsletters_orderby($selected, $name = 'display_newsletter_order', $attribs = array('class' => 'inputbox', 'size' => '1'), $idtag = null, $allowAny = false, $title = 'Select')
 	{
        $list = array();
		
       	$list[] =  self::option( 'tbl.listorder', JText::_('Ordering'), 'id', 'title' );
       	$list[] =  self::option( 'lastsent', JText::_('Last message sent'), 'id', 'title' );
       	$list[] =  self::option( 'tbl.name', JText::_('Alphabetical (on newsletter name)'), 'id', 'title' );

		return self::genericlist($list, $name, $attribs, 'id', 'title', $selected, $idtag );
 	}
 	
	/**
	*
	* @param string The value of the HTML name attribute
	* @param string Additional HTML attributes for the <select> tag
	* @param mixed The key that is selected
	* @returns string HTML for the radio list
	*/
	public static function newsletters_orderdir( $selected, $name = 'display_newsletter_order_dir', $attribs = array('class' => 'inputbox', 'size' => '1'), $idtag = null, $allowAny = false, $title='Select Order', $yes = 'Ascending', $no = 'Decending' )
	{
	    $list = array();
		if($allowAny) {
			$list[] =  self::option('', "- ".JText::_( $title )." -" );
		}

		$list[] = JHTML::_('select.option',  'DESC', JText::_( $no ) );
		$list[] = JHTML::_('select.option',  'ASC', JText::_( $yes ) );
		
		return self::genericlist($list, $name, $attribs, 'value', 'text', $selected, $idtag );
	}
	
	/**
	 * 
	 * @param $selected
	 * @param $name
	 * @param $attribs
	 * @param $idtag
	 * @param $allowAny
	 * @return unknown_type
	 */
	public static function page($selected, $name = 'filter_page', $attribs = array('class' => 'inputbox', 'size' => '1'), $idtag = null, $allowAny = false, $title = 'Select Page')
 	{
        $list = array();
		if($allowAny) {
			$list[] =  self::option('', "- ".JText::_( $title )." -", 'id', 'title' );
		}
		
 		JModel::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.'com_phplist'.DS.'models' );
        $model = JModel::getInstance('Logs', 'PhplistModel');
        $model->setState( 'select', 'tbl.page AS title, tbl.page as id');
        $model->setState( 'select_group', 'tbl.page' );
		$model->setState( 'order', 'tbl.page' );
		$model->setState( 'direction', 'ASC' );
		$items = $model->getList();
        foreach (@$items as $item)
        {
        	$list[] =  self::option( $item->title, JText::_($item->title), 'id', 'title' );
        }

		return self::genericlist($list, $name, $attribs, 'id', 'title', $selected, $idtag );
 	}
 	
	/**
	 * 
	 * @param $selected
	 * @param $name
	 * @param $attribs
	 * @param $idtag
	 * @param $allowAny
	 * @return unknown_type
	 */
	public static function templates($selected, $name = 'templates', $attribs = array('class' => 'inputbox', 'size' => '1'), $idtag = null, $allowAny = false, $title = 'Select Template')
 	{
        $list = array();
		if($allowAny) {
			$list[] =  self::option('', "- ".JText::_( $title )." -", 'id', 'title' );
		}
		
 		JModel::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.'com_phplist'.DS.'models' );
        $model = JModel::getInstance('Templates', 'PhplistModel');
        $model->setState( 'select', 'tbl.title AS title, tbl.id as id');
        $model->setState( 'select_group', 'tbl.id' );
		$model->setState( 'order', 'tbl.id' );
		$model->setState( 'direction', 'ASC' );
		$items = $model->getList();
		$list[] =  self::option( '0', JText::_('No Template'), 'id', 'title' );
        foreach (@$items as $item)
        {
        	$list[] =  self::option( $item->id, JText::_($item->title), 'id', 'title' );
        }

		return self::genericlist($list, $name, $attribs, 'id', 'title', $selected, $idtag );
 	}
 	
	/**
	 * 
	 * @param $selected
	 * @param $name
	 * @param $attribs
	 * @param $idtag
	 * @param $allowAny
	 * @return unknown_type
	 */
 	public static function attributes($selected, $name = 'filter_attributes', $attribs = array('class' => 'inputbox', 'size' => '1'), $idtag = null, $allowAny = false, $title = 'Select Attributes')
 	{
 		$list = array();
 		if($allowAny) {
 			$list[] =  self::option('', "- ".JText::_( $title )." -", 'id', 'title' );
 		}
 	
 		JModel::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.'com_phplist'.DS.'models' );
 		$model = JModel::getInstance('Attributes', 'PhplistModel');
 		$model->setState( 'select', 'tbl.name AS title, tbl.id as id');
 		$model->setState( 'order', 'tbl.listorder' );
 		$model->setState( 'direction', 'ASC' );
 		$items = $model->getList();
 		foreach (@$items as $item)
 		{
 			$list[] =  self::option( $item->id, JText::_($item->title), 'id', 'title' );
 		}
 	
 		return self::genericlist($list, $name, $attribs, 'id', 'title', $selected, $idtag );
 	}
 	
 	/**
 	 *
 	 * @param $selected
 	 * @param $name
 	 * @param $attribs
 	 * @param $idtag
 	 * @param $allowAny
 	 * @return unknown_type
 	 */
	public static function attribute_type($selected, $name = 'filter_type', $attribs = array('class' => 'inputbox', 'size' => '1'), $idtag = null, $allowAny = false, $title = 'Select Type')
 	{
        $list = array();
		if($allowAny) {
			$list[] =  self::option('', "- ".JText::_( $title )." -", 'id', 'title' );
		}
		
 		JModel::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.'com_phplist'.DS.'models' );
        $model = JModel::getInstance('Attributes', 'PhplistModel');
        $model->setState( 'select', 'tbl.type AS title, tbl.type as id');
        $model->setState( 'select_group', 'tbl.type' );
		$model->setState( 'order', 'tbl.type' );
		$model->setState( 'direction', 'ASC' );
		$items = $model->getList();
        foreach (@$items as $item)
        {
        	$list[] =  self::option( $item->title, JText::_($item->title), 'id', 'title' );
        }

		return self::genericlist($list, $name, $attribs, 'id', 'title', $selected, $idtag );
 	}
 	
	public static function AttribSelect( $id, $value, $name )
 	{
		$data = PhplistHelperAttribute::getAttributeListValues($id);
		foreach (@$data as $option)
        {	
        	$list[] =  self::option( $option->id, JText::_($option->name),'id' , 'title' );
        }
	return self::genericlist($list, $name, '', 'id', 'title', $value, '' );
 	}
 	
	public static function AttribRadio( $id, $value, $name )
 	{
		$data = PhplistHelperAttribute::getAttributeListValues($id);
		foreach (@$data as $option)
        {	
        	$list[] =  self::option( $option->id, JText::_($option->name),'id' , 'title' );
        }
	return self::radiolist($list, $name, '', 'id', 'title', $value, '' );
 	}


	/**
	 * 
	 * @param $selected
	 * @param $name
	 * @param $attribs
	 * @param $idtag
	 * @param $allowAny
	 * @return unknown_type
	 */
	public static function messagestate($selected, $name = 'filter_messagestate', $attribs = array('class' => 'inputbox', 'size' => '1'), $idtag = null, $allowAny = false, $title = 'Select State')
 	{
        $list = array();
		if($allowAny) {
			$list[] =  self::option('', "- ".JText::_( $title )." -", 'id', 'title' );
		}

        $items = PhplistHelperMessage::getStates();
        foreach (@$items as $item)
        {
        	$list[] =  self::option( $item->id, JText::_($item->title), 'id', 'title' );
        }

		return self::genericlist($list, $name, $attribs, 'id', 'title', $selected, $idtag );
 	}
 	
	/**
	 * 
	 * @param $selected
	 * @param $name
	 * @param $attribs
	 * @param $idtag
	 * @param $allowAny
	 * @return unknown_type
	 */
	public static function newsletter($selected, $name = 'filter_listid', $attribs = array('class' => 'inputbox', 'size' => '1'), $idtag = null, $allowAny = false, $allowNone = false, $title = 'Select Newsletter', $title_none = 'No Newsletter' )
 	{
		// Build list
        $list = array();
 		if($allowAny) {
			$list[] =  self::option('', "- ".JText::_( $title )." -", 'id', 'title' );
		}
 		if($allowNone) {
			$list[] =  self::option('0', "- ".JText::_( $title_none )." -", 'id', 'title' );
		}

		JModel::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_phplist'.DS.'models' );
		$model = JModel::getInstance( 'Newsletters', 'PhplistModel' );
		$model->setState( 'order', 'name' );
		$model->setState( 'direction', 'ASC' );
		$items = $model->getList();
        foreach (@$items as $item)
        {
        	$list[] =  self::option( $item->id, JText::_($item->name), 'id', 'title' );
        }
		return self::genericlist($list, $name, $attribs, 'id', 'title', $selected, $idtag );
 	}
	
	/**
	 * Generates a HTML/text select list
	 * @param $selected
	 * @param $name
	 * @param $attribs
	 * @param $idtag
	 * @param $allowAny
	 * @return unknown_type
	 */
	public static function sendas($selected, $name = 'sendformat', $attribs = array('class' => 'inputbox', 'size' => '1'), $idtag = null, $allowAny = false)
 	{
        $list = array();
		$list[] = JHTML::_('select.option',  'HTML', JText::_( 'HTML' ) );
		$list[] = JHTML::_('select.option',  'text', JText::_( 'TEXT' ) );

		return self::genericlist($list, $name, $attribs, 'value', 'text', $selected, $idtag );
 	}
}