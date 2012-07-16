<?php
/**
 * @version 1.5
 * @package Phplist
 * @author  Dioscouri Design
 * @link    http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/ ?>
<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php JHTML::_('stylesheet', 'phplist.css', 'media/com_phplist/css/'); ?>
<?php JHTML::_('stylesheet', 'menu.css', 'media/com_phplist/css/'); ?>
<?php JHTML::_('script', 'phplist.js', 'media/com_phplist/js/'); ?>

<div class="componentheading">
	<span><?php echo JText::_( "THANK YOU" ); ?></span>
</div>

<div class="subscribe_message">
<?php echo $vars->message; ?>
</div>