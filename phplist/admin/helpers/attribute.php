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
		$success = false;
		$database = PhplistHelperPhplist::getDBO();
		
		Phplist::load( 'PhplistQuery', 'library.query' );
		JTable::addIncludePath( JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_phplist' . DS . 'tables' );
		
		$table = JTable::getInstance( 'Attributes', 'Table' );
		$query = new PhplistQuery( );
		$query->select( "tablename" );
		$query->from( $table->getTableName( ) . " AS tbl" );
		$query->where( "tbl.id = " . ( int ) $attribId );
		
		$database->setQuery( ( string ) $query );
		$data = $database->loadObject();
		
		$tablename_attributeslists = $data->tablename;
		$phplist_prefix = PhplistHelperPhplist::getPrefix();
		$success = "{$phplist_prefix}_listattr_{$tablename_attributeslists}";
		
		return $success;
	}
	
	/**
	 * returns a list of all the attributes, or front end only attributes.
	 * replaces .'s and spaces for form use if $formuse = '1'
	 */
	function getAttributes($frontend = false)
	{
		$success = false;
		$database = PhplistHelperPhplist::getDBO();
		Phplist::load( 'PhplistQuery', 'library.query' );
		JTable::addIncludePath( JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_phplist' . DS . 'tables' );
		
		$table = JTable::getInstance( 'Attributes', 'Table' );
		$query = new PhplistQuery( );
		$query->select( "*" );
		$query->from( $table->getTableName( ) . " AS tbl" );
		
		if ($frontend)
		{
			// get csv of front end attribute id's from config
			$config = &PhplistConfig::getInstance();
			$frontendAttribs = $config->get( 'frontend_attribs', '1' );
			
			if ($frontendAttribs  != '' && $frontendAttribs  != '0')
			{
				$query->where( "tbl.id IN (" . $frontendAttribs .")");
			}
			else
			{
				// don't return anything if not frontend attribs in config
				return false;
			}
		}
		
		$database->setQuery( ( string ) $query );
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
	function getUserAttributes( $userid, $frontend = false )
	{
		$success = false;
		$database = PhplistHelperPhplist::getDBO();
		$tablename_attributes = PhplistHelperAttribute::getTableName();
		$tablename_userattributes = PhplistHelperAttribute::getTableName_userattributes();
		Phplist::load( 'PhplistQuery', 'library.query' );
		JTable::addIncludePath( JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_phplist' . DS . 'tables' );
		
		$query = new PhplistQuery( );
		$query->select( "*" );
		$query->from( $tablename_userattributes . " AS tbl" );		
		$query->join( 'LEFT', $tablename_attributes.' AS attribs ON tbl.attributeid = attribs.id' );
		$query->where( 'tbl.userid = '.$userid );
		
		if ($frontend)
		{
			// get csv of front end attribute id's from config
			$config = &PhplistConfig::getInstance();
			$frontendAttribs = $config->get( 'frontend_attribs', '1' );

			if ($frontendAttribs  != '' && $frontendAttribs  != '0')
			{
				$query->where( "attribs.id IN (" . $frontendAttribs .")");
			}
		}
		
		$database->setQuery( ( string ) $query );
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
		$tablename_userattributes = PhplistHelperAttribute::getTableName_userattributes();
		Phplist::load( 'PhplistQuery', 'library.query' );
		JTable::addIncludePath( JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_phplist' . DS . 'tables' );
		
		$query = new PhplistQuery( );
		$query->select( "value" );
		$query->from( $tablename_userattributes . " AS tbl" );		
		$query->where( 'tbl.userid = '.$userId );
		$query->where( 'tbl.attributeid = '.$attribId );
		
		$database->setQuery( ( string ) $query );
		$data = $database->loadObject();
		$success = $data->value;
		return $success;
	}
	
	
	/**
	 * returns default value for an attribute
	 */
	function getAttributeDefault( $attribId )
	{
		$success = false;
		$database = PhplistHelperPhplist::getDBO();
		$tablename_attributes = PhplistHelperAttribute::getTableName();
		Phplist::load( 'PhplistQuery', 'library.query' );
		JTable::addIncludePath( JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_phplist' . DS . 'tables' );
		
		$query = new PhplistQuery( );
		$query->select( "default_value" );
		$query->from( $tablename_attributes . " AS tbl" );		
		$query->where( 'tbl.id = '.$attribId );
		
		$database->setQuery( ( string ) $query );
		$data = $database->loadObject();
		$success = $data->default_value;
		return $success;
	}
	
	/**
	 * gets values from attribute list table (eg. for select box attribute)
	 */
	function getAttributeListValues($id)
	{
		$success = false;
		$database = PhplistHelperPhplist::getDBO();
		$tablename_attributeslists = PhplistHelperAttribute::getTableName_attributeslists( $id );
		Phplist::load( 'PhplistQuery', 'library.query' );
		JTable::addIncludePath( JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_phplist' . DS . 'tables' );
		
		$query = new PhplistQuery( );
		$query->select( "*" );
		$query->from( $tablename_attributeslists . " AS tbl" );		
		$query->order('listorder ASC');
		
		$database->setQuery( ( string ) $query );
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
		JLoader::import( 'com_phplist.library.select', JPATH_ADMINISTRATOR.DS.'components' );
		
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
		$success = false;
		$database = PhplistHelperPhplist::getDBO();
		$tablename_userattributes = PhplistHelperAttribute::getTableName_userattributes();
		Phplist::load( 'PhplistQuery', 'library.query' );
		JTable::addIncludePath( JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_phplist' . DS . 'tables' );
		
		$query = new PhplistQuery( );
		$query->select( "*" );
		$query->from( $tablename_userattributes . " AS tbl" );		
		$query->where('tbl.attributeid ='.$attribId);
		$query->where('tbl.userid ='.$userid);
		
		$database->setQuery( ( string ) $query );
		if ($data = $database->loadObject())
		{
			//if a row exsits for user attribute, update it
			$success = true;
			$database->setQuery( "UPDATE ".$tablename_userattributes." SET value = '".$value."' WHERE attributeid = ".$attribId." AND userid = ".$userid );
			if ( !$database->query( ) )
			{
				$this->setError( $database->getErrorMsg( ) );
				$success = false;
			}
			return $success;
		}
		else
		{
			$success = true;
			//if a row does not exsit for user attribute, insert it
			$database->setQuery( "INSERT INTO ".$tablename_userattributes." VALUES (".$attribId.",".$userid.",'".$value."')");
			if ( !$database->query( ) )
			{
				$this->setError( $database->getErrorMsg( ) );
				$success = false;
			}
			return $success;
		}
	}
}

?>
