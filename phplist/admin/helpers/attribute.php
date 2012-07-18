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

Phplist::load( 'PhplistHelperBase', 'helpers.base');

class PhplistHelperAttribute extends PhplistHelperBase
{
	/**
	 * Returns the phphlist attributes table name
	 * @return unknown_type
	 */
	function getTableName() 
	{
		JLoader::import( 'com_phplist.helpers.phplist', JPATH_ADMINISTRATOR.DS.'components' ); 
		$success = false;
		$phplist_prefix = PhplistHelperPhplist::getPrefix();
		$success = "{$phplist_prefix}_user_attribute";
		return $success;
	}
	
	/**
	 * Returns the phphlist user attributes table name
	 * @return unknown_type
	 */
	function getTableName_userattributes() 
	{
		JLoader::import( 'com_phplist.helpers.phplist', JPATH_ADMINISTRATOR.DS.'components' ); 
		$success = false;
		$phplist_user_prefix = PhplistHelperPhplist::getUserTablePrefix();
		$success = "{$phplist_user_prefix}_user_attribute";
		return $success;
	}
	
	/**
	 * Returns the phphlist attributes list table name (eg for content of dropdown lists etc.)
	 * @return unknown_type
	 */
	function getTableName_attributeslists($attribId) 
	{
		JLoader::import( 'com_phplist.helpers.phplist', JPATH_ADMINISTRATOR.DS.'components' ); 
		$success = false;
		$phplist_prefix = PhplistHelperPhplist::getPrefix();
		
		$success = false;
		$database = PhplistHelperPhplist::getDBO();
		
		JLoader::import( 'com_phplist.helpers.attribute', JPATH_ADMINISTRATOR.DS.'components' );
		$tablename_attributes = PhplistHelperAttribute::getTableName();
		
		$query = "
			SELECT
				*
			FROM
				{$tablename_attributes}
			WHERE
				{$tablename_attributes}.id = '{$attribId}'
		";
		$database->setQuery( $query );
		$data = $database->loadObject();
		$tablename_attributeslists = $data->tablename;
		$success = "{$phplist_prefix}_listattr_{$tablename_attributeslists}";
		
		return $success;
	}
	
	/**
	 * returns a list of all the attributes, or front end only attributes.
	 * replaces .'s and spaces for form use if $formuse = '1'
	 */
	function getAttributes($frontend = '0')
	{
		JLoader::import( 'com_phplist.helpers.phplist', JPATH_ADMINISTRATOR.DS.'components' );
		JLoader::import( 'com_phplist.helpers.attribute', JPATH_ADMINISTRATOR.DS.'components' );
		$tablename_attributes = PhplistHelperAttribute::getTableName();
		$database = PhplistHelperPhplist::getDBO();
		$success = false;
		
		$where = "";
		if ($frontend == '1')
		{
			// get csv of front end attribute id's from config
			$config = &Phplist::getInstance();
			$frontendAttribs = $config->get( 'frontend_attribs', '1' );

			if ($frontendAttribs  != '' && $frontendAttribs  != '0')
			{
				$where .= ' WHERE id IN ('.$frontendAttribs.')';
			}
			else
			{
				// don't return anything if not frontend attribs in config
				return false;
			}
		}

		$query = "
			SELECT
				*
			FROM
				{$tablename_attributes}  
			{$where}
		";
		$database->setQuery( $query );
		$data = $database->loadObjectList();
		
		$success = $data;
		return $success;
	}
	
	function saveAttributes( $userid )
	{
			JLoader::import( 'com_phplist.helpers.attribute', JPATH_ADMINISTRATOR.DS.'components' );
			$attributes = PhplistHelperAttribute::getAttributes();
	        if ($attributes)
	        {                   
	            foreach ($attributes as $a)
	            {
	            	//replace spaces with _ in input names
	            	$name = str_replace(' ','_',$a->name);
	            	$name = str_replace('.','_',$name);
	            	$attributeValue = JRequest::getVar( $name);

					//get CSV for checkboxgroup
					$Value ="";
					if ($a->type == 'checkboxgroup' && $attributeValue != '')
					{
						$Value .= '';
						foreach ($attributeValue as $tickbox)
						{
							$Value .= $tickbox . ',';
						}
						$attributeValue = $Value;
					}
	            	
	                if ($a->id > 0)
	                {
	                    $insert = PhplistHelperAttribute::insertAttributeValue( $userid, $a->id, $attributeValue );
	                }
	            }
	        }
	}
	/**
	 * gets attribute info for a user
	 */
	function getUserAttributes( $userid, $frontend='0' )
	{
		$success = false;
		$database = PhplistHelperPhplist::getDBO();
		
		JLoader::import( 'com_phplist.helpers.attribute', JPATH_ADMINISTRATOR.DS.'components' );
		$tablename_attributes = PhplistHelperAttribute::getTableName();
		$tablename_userattributes = PhplistHelperAttribute::getTableName_userattributes();

		$where = "";
		if ($frontend == '1')
		{
			// get csv of front end attribute id's from config
			$config = &Phplist::getInstance();
			$frontendAttribs = $config->get( 'frontend_attribs', '1' );

			if ($frontendAttribs  != '')
			{
				$where .= ' AND id IN ('.$frontendAttribs.')';
			}
		}
		
		$query = "
			SELECT
				*
			FROM
				{$tablename_userattributes}
				LEFT JOIN {$tablename_attributes} ON {$tablename_userattributes}.attributeid = {$tablename_attributes}.id   
			WHERE
				{$tablename_userattributes}.userid = '{$userid}'
				{$where}
		";
		$database->setQuery( $query );
		$data = $database->loadObjectList();
		$success = $data;
		
		return $success;
	}
	
	/**
	 * gets attribute value for a specific user/attribute
	 */
	function getAttributeValue( $userId, $attribId )
	{
		$success = false;
		$database = PhplistHelperPhplist::getDBO();
		
		JLoader::import( 'com_phplist.helpers.attribute', JPATH_ADMINISTRATOR.DS.'components' );
		$tablename_attributes = PhplistHelperAttribute::getTableName();
		$tablename_userattributes = PhplistHelperAttribute::getTableName_userattributes();
		
		$query = "
			SELECT
				*
			FROM
				{$tablename_userattributes}
			WHERE
				{$tablename_userattributes}.userid = '{$userId}'
				AND 
				{$tablename_userattributes}.attributeid = '{$attribId}'
			LIMIT 1
		";
		$database->setQuery( $query );
		$database->query();
		$rows = $database->getNumRows();
		$data = $database->loadObject();
		if ($rows == 1)
		{
			$success = $data->value;
		}
		return $success;
	}
	
	
	/**
	 * returns default value for an attribute
	 */
	function getAttributeDefault( $attribId )
	{
		$success = false;
		$database = PhplistHelperPhplist::getDBO();
		
		JLoader::import( 'com_phplist.helpers.attribute', JPATH_ADMINISTRATOR.DS.'components' );
		$tablename_attributes = PhplistHelperAttribute::getTableName();
		
		$query = "
			SELECT
				default_value
			FROM
				{$tablename_attributes}
			WHERE
				{$tablename_attributes}.id = '{$attribId}'
			LIMIT 1
		";
		$database->setQuery( $query );
		$database->query();
		if ($data = $database->loadObject())
		{
			$success = $data->default_value;
		}
		return $success;
	}
	
	/**
	 * gets values from attribute list table (eg. for select box attribute)
	 */
	function getAttributeListValues($id)
	{
		$success = false;
		JLoader::import( 'com_phplist.helpers.attribute', JPATH_ADMINISTRATOR.DS.'components' );
       	$database = PhplistHelperPhplist::getDBO();
		$tablename = PhplistHelperAttribute::getTableName_attributeslists( $id );
		$query = "
			SELECT
				*
			FROM
				{$tablename}
			ORDER BY listorder ASC
		";
		$database->setQuery( $query );
		$data = $database->loadObjectList();
		$success = $data;
		return $success;
	}
	
	/**
	 * initiates create of attributes form input fields
	 */
	function formInput( $id, $value, $type='textline', $name )
	{
		$success = false;
		JLoader::import( 'com_phplist.helpers.attribute', JPATH_ADMINISTRATOR.DS.'components' );
		$newname = $name;
		//remove spaces from name
		$name = str_replace(' ','_',$newname);
		$name = str_replace('.','_',$name);
		
		switch ($type) 
		{
			case "radio": 
				$success = PhplistSelect::AttribRadio( $id, $value, $name  );
			  break;
			case "hidden": 
				$success = PhplistHelperAttribute::AttribHidden( $id, $value, $name, $site="admin" );
			  break;
			case "checkbox": 
				$success = PhplistHelperAttribute::AttribCheckbox( $id, $value, $name );
			  break;
			case "checkboxgroup":
				$success = PhplistHelperAttribute::AttribCheckboxgroup( $id, $value, $name ); 
			  break;
			case "select":
				$success = PhplistSelect::AttribSelect( $id, $value, $name ); 
			  break;
			case "date":
				$success = PhplistHelperAttribute::AttribDate( $id, $value, $name ); 
			  break; 
			case "textarea":
				$success = PhplistHelperAttribute::AttribTextarea( $id, $value, $name ); 
			 break; 
			case "textline": 
			default: 
				$success = PhplistHelperAttribute::AttribTextline( $id, $value, $name );
			  break;	
		}
		
		return $success;
	}
	
	/**
	 * create text line attribute input field
	 */
	function AttribTextline( $id, $value, $name )
	{
		$success = '<input type="text" value="'.$value.'" name="'.$name.'" />';
		return $success;
	}
	
	/**
	 * create textarea attribute input field
	 */
	function AttribTextarea( $id, $value, $name )
	{
		//TODO save doesn't work when ' apostrophies '
		$success = '<textarea cols="30" rows="6" name="'.$name.'" id="'.$id.'">'. JText::_($value) . '</textarea>';
		return $success;
	}
	
	/**
	 * create hidden field attribute input field
	 */
	function AttribHidden( $id, $value, $name, $site )
	{
		switch ($site) 
		{
			case "frontend":
				$success = '<input type="hidden" value="'.$value.'" name="'.$name.'" />';
			 break;
			case "admin": 
			default:	
				$success = '<input type="text" value="'.$value.'" name="'.$name.'" />';
			 break;	
		}
			
		return $success;
	}
	
	/**
	 * create date attribute input field
	 */
	function AttribDate( $id, $value, $name )
	{
		$success = JHTML::calendar( $value, $name, $name, '%Y-%m-%d' );
		return $success;
	}
	
	/**
	 * create checkbox group attribute
	 */
	function AttribCheckboxgroup( $id, $value, $name )
	{
		JLoader::import( 'com_phplist.helpers.attribute', JPATH_ADMINISTRATOR.DS.'components' );
		$data = PhplistHelperAttribute::getAttributeListValues($id);
		$cids = explode( ',', trim($value) );
		$checkboxes = '';
		foreach (@$data as $option)
        {	
        	$checked = "";
        	if (in_array($option->id, $cids))
        	{
        		$checked = "checked='checked'";
        	}
        	$checkboxes .= JText::_($option->name);
        	$checkboxes .= "<input type='checkbox' name='".$name."[" . $option->id . "]' value='". $option->id ."' " . $checked . " />";
        	$checkboxes .= "<br/>";
        }
	
		return $checkboxes;
	}
	
	/**
	 * create single check box attribute input field
	 */
	function AttribCheckbox( $id, $value, $name )
	{
		JLoader::import( 'com_phplist.helpers.attribute', JPATH_ADMINISTRATOR.DS.'components' );
		$checkbox = "";
		$checked = "";
        if ($value == 'on')
        {
        	$checked = "checked='checked'";
        }
        $checkbox .= "<input type='checkbox' name='".$name."' " . $checked . " />";
		return $checkbox;
	}
		
	/**
	 * saves attribute value for user.
	 */
	function insertAttributeValue( $userid, $attribId, $value )
	{
		
		// First check if an attribute value exists for the user (even if value is null)
		$success = false;
		$isValue = false;
		$database = PhplistHelperPhplist::getDBO();
		JLoader::import( 'com_phplist.helpers.attribute', JPATH_ADMINISTRATOR.DS.'components' );
		$tablename = PhplistHelperAttribute::getTableName_userattributes();
		
		$query = "
			SELECT
				*
			FROM
				{$tablename}
			WHERE
				`attributeid` = '{$attribId}'
			AND
				`userid` = '{$userid}'
		";
				
		$database->setQuery( $query );
		if ($data = $database->loadObject())
		{
			$isValue = true;
		}
		
		if ($isValue == true)
		{
			//if a row exsits for user attribute, update it
			$updateQuery = "
			UPDATE
			{$tablename}
			SET
				`value` = '{$value}'
			WHERE
				`attributeid` = '{$attribId}'
			AND
				`userid` = '{$userid}'
			";
			$database->setQuery( $updateQuery );
			$success = $database->query();
		}
		else
		{
			//if a row does not exsit for user attribute, insert it
			$insertQuery = "
			INSERT INTO
			{$tablename}
			VALUES
			('{$attribId}','{$userid}','{$value}')
			";
			
			$database->setQuery( $insertQuery );
			$success = $database->query();
		}
		return $success;
	}
}

?>
