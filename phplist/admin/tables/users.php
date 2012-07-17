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
defined( '_JEXEC' ) or die( 'Restricted access' );

class PhplistTableUsers extends DSCTable 
{

	function TableUsers( &$db ) 
	{
		$tbl_key 	= 'id';
		$tbl_suffix = 'users';
		$this->set( '_suffix', $tbl_suffix );
		
		JLoader::import( 'com_phplist.helpers.phplist', JPATH_ADMINISTRATOR.DS.'components' );
		JLoader::import( 'com_phplist.helpers.user', JPATH_ADMINISTRATOR.DS.'components' );
		$database = PhplistHelperPhplist::getDatabase();
		$tablename = PhplistHelperUser::getTableName();
		
		parent::__construct( $tablename, $tbl_key, $db );	
	}
	
	 /**
     * Publish/Unpublish function
     * Overrides generic b/c of different fieldname
     *
     * @access public
     * @param array An array of id numbers
     * @param integer 0 if unpublishing, 1 if publishing
     * @param integer The id of the user performnig the operation
     * @since 1.0.4
     */
    function publish( $cid=null, $publish=1, $user_id=0 )
    {
        JArrayHelper::toInteger( $cid );
        $user_id    = (int) $user_id;
        $publish    = (int) $publish;
        $k            = $this->_tbl_key;

        if (count( $cid ) < 1)
        {
            if ($this->$k) {
                $cid = array( $this->$k );
            } else {
                $this->setError("No items selected.");
                return false;
            }
        }
        
        $cids = $k . '=' . implode( ' OR ' . $k . '=', $cid );
        
        $query = "
        	UPDATE 
        		$this->_tbl
        	SET 
        		`confirmed` = '" . (int) $publish ."'
        	 WHERE 
        	 	('$cids')
        ";

        $checkin = in_array( 'checked_out', array_keys($this->getProperties()) );
        if ($checkin)
        {
            $query .= ' AND (checked_out = 0 OR checked_out = '.(int) $user_id.')';
        }
        
        $this->_db->setQuery( $query );
        if (!$this->_db->query())
        {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }
        
        if (count( $cid ) == 1 && $checkin)
        {
            if ($this->_db->getAffectedRows() == 1) {
                $this->checkin( $cid[0] );
                if ($this->$k == $cid[0]) {
                    $this->published = $publish;
                }
            }
        }
        $this->setError('');
        return true;
    }
}

?>