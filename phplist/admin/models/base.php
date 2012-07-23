<?php
/**
 * @version 1.5
 * @package Phplist
 * @author  Dioscouri Design
 * @link    http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

class PhplistModelBase extends DSCModel
{

    public function getTable($name='', $prefix='PhplistTable', $options = array())
    {
        JTable::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_phplist/tables' );
        return parent::getTable($name, $prefix, $options);
    }
}