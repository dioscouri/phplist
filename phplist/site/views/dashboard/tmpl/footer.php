<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php
$url = "http://www.dioscouri.com/";
if ($amigosid = Phplist::getInstance()->get( 'amigosid', '' ))
{
    $url .= "?amigosid=".$amigosid; 
}
?>

<p align="center" <?php echo @$this->style; ?> >
	<?php echo JText::_('Powered by')." <a href='{$url}' target='_blank'>".JText::_('COM_PHPLIST_PHPLIST')."</a>"; ?>
</p>

