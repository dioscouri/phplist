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

class PhplistViewNewsletters extends PhplistViewBase 
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
     * (non-PHPdoc)
     * @see phplist/site/views/PhplistViewBase#_default($tpl)
     */
    function _default($tpl=null)
    {
    	parent::_default($tpl);
        
        $user = JFactory::getUser();
        $phplistUser = null;
        
        if ($user->id > '0') 
        {
            $phplistUser = PhplistHelperUser::getUser( $user->id, '1', 'foreignkey' );
            if (!isset($phplistUser->id)) 
            {
                $phplistUser = PhplistHelperUser::create( $user );  
            }
        }
            elseif ($uid = PhplistHelperUser::getUid())
        {
        	$phplistUser = PhplistHelperUser::getUser( $uid, '1', 'uid' );
        }
        
		$this->assign( 'phplistuser', $phplistUser );
		$this->assign('joomlauserID', $user->id);

		$attributes = PhplistHelperAttribute::getAttributes($frontend = '1');
		$this->assign( 'attributes', $attributes );
		
		//TODO if user comes from UID, and is Joomla! user, log them in.
    }
}

?>