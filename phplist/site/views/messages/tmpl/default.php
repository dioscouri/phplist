<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php JHTML::_('stylesheet', 'phplist.css', 'media/com_phplist/css/'); ?>
<?php JHTML::_('stylesheet', 'menu.css', 'media/com_phplist/css/'); ?>
<?php JHTML::_('script', 'phplist.js', 'media/com_phplist/js/'); ?>
<?php $state = @$this->state; ?>
<?php $form = @$this->form; ?>
<?php $items = @$this->items; ?>
<?php $row = @$this->row; ?>

<div class='componentheading'>
	<span><?php echo JText::_( "MESSAGES" ); ?></span>
</div>

<?php echo PhplistMenu::display(); ?>

<div id='onBeforeDisplay_wrapper'>
<?php 
	$dispatcher = JDispatcher::getInstance();
	$dispatcher->trigger( 'onBeforeDisplayMessages', array( @$this->row, @$this->user ) );
?>
</div>

<form action="<?php echo JRoute::_( $this->action ); ?>" method="post" name="adminForm" enctype="multipart/form-data">

    <table>
        <tr>
            <td align="left" width="100%">
				<h2><?php echo JText::_( $row->name ); ?></h2>
            </td>
            <td style="white-space: nowrap;">
            	<input type="text" name="filter" id="filter" value="<?php echo @$state->filter; ?>" />
                <input type="button" onclick="this.form.submit();" value="<?php echo JText::_('SEARCH'); ?>" />
                <input type="button" onclick="document.getElementById('filter').value=''; this.form.submit();" value="<?php echo JText::_('RESET'); ?>" />
            </td>
        </tr>
    </table>

	<table class="adminlist" style="clear: both;">
		<thead>
            <tr>
				<th>
                	<?php echo DSCGrid::sort( 'Subject', "tbl.subject", @$state->direction, @$state->order ); ?>
                </th>
                <th>
                	<?php echo DSCGrid::sort( 'Sent', "tbl.sendstart", @$state->direction, @$state->order ); ?>
                </th>
            </tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="20">
					<div style="float: right; padding: 5px;"><?php echo @$this->pagination->getResultsCounter(); ?></div>
					<?php echo @$this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
        <tbody>
		<?php $i=0; $k=0; ?>
        <?php foreach (@$items as $item) : ?>
            <tr class='row<?php echo $k; ?>'>
				<td style="text-align: left;">
					<a href="<?php echo @$item->link_view; ?>">
						<?php echo @$item->subject; ?>
					</a>
				</td>	
				<td style="text-align: center;">
					<?php echo JHTML::_( "date", @$item->sendstart, JText::_('SEND DATE FORMAT'), '0'); ?>
				</td>
			</tr>				
			<?php $i=$i+1; $k = (1 - $k); ?>
			<?php endforeach; ?>
			
			<?php if (!count(@$items)) : ?>
			<tr>
				<td colspan="10" align="center">
					<?php echo JText::_('NO ITEMS FOUND'); ?>
				</td>
			</tr>
			<?php endif; ?>
		</tbody>
	</table>
	
	<div id='onAfterDisplay_wrapper'>
			<?php 
				$dispatcher = JDispatcher::getInstance();
				$dispatcher->trigger( 'onAfterDisplayMessages', array( @$this->row, @$this->user ) );
			?>
	</div>
    
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="" />
	<input type="hidden" name="filter_order" value="<?php echo @$state->order; ?>" />
	<input type="hidden" name="filter_direction" value="<?php echo @$state->direction; ?>" />
	
	<?php echo $this->form['validate']; ?>
</form>