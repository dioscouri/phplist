<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php JHTML::_('script', 'phplist.js', 'media/com_phplist/js/'); ?>
<?php $state = @$vars->state; ?>

    <p><?php echo JText::_( "The downloaders of the selected Ambrasubs file will be added to the selected Phplist newsletter" ); ?></p>

    <div class="note">
	    <?php echo JText::_("First select an AMBRASUBS file"); ?>:
	    <input name='ambrasubs_fileid' value='' size='5' type='text' />
	    
        <?php echo JText::_("then select a target Phplist newsletter"); ?>:
        <input name='phplist_newsletterid' value='' size='5' type='text' />
    </div>
        