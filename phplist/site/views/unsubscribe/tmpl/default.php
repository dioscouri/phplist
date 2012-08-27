<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php JHTML::_('stylesheet', 'phplist.css', 'media/com_phplist/css/'); ?>
<?php JHTML::_('stylesheet', 'menu.css', 'media/com_phplist/css/'); ?>
<?php JHTML::_('script', 'common.js', 'media/com_phplist/js/'); ?>
<?php JHTML::_('script', 'joomla.javascript.js', 'includes/js/'); ?>
<?php $items = @$this->items; ?>
<?php $row = @$this->row; ?>

<div class='componentheading'>
	<?php echo JText::_('UNSUBSCRIBE_FROM_NEWSLETTERS'); ?>
</div>
<?php echo PhplistMenu::display(); ?>

<div id='onBeforeDisplay_wrapper'>
	<?php $dispatcher = JDispatcher::getInstance();
	$dispatcher->trigger( 'onBeforeDisplayUnsubscribe', array( $this->row, $this->user ) ); ?>
</div>

<form action="<?php JRoute::_('index.php'); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminheading">
		<tr>
	        <th>
	        	<?php echo JText::_( "YOUR_EMAIL" ).":"; ?>
	        	 <span class="non_bold">
					<?php echo $this->email; ?>
				</span>
	        	<input type="hidden" name="uid" value="<?php echo $this->uid; ?>" />
	        </th>
		</tr>
		<tr>
			<td>
				<?php if (!count(@$items)) echo JText::_('NOT_SUBSCRIBED'); 
				else echo JText::_( 'YOU_ARE_SUBSCRIBED_TO_THE_FOLLOWING_NEWSLETTERS' ); ?>
			</td>
		</tr>
		</table>
		<?php  if (count(@$items)) : ?>
		<table class="adminlist">
		<thead>
            <tr>
            	<th width="5">
                	<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->items ); ?>);" />
                </th>
                <th style="text-align:left">
	                <?php echo JText::_('SELECT_ALL'); ?>
                </th>
            </tr>
		</thead>
        <tbody>
		<?php $i=0; $k=0; ?>
        <?php foreach (@$items as $item) : ?>
            <tr class='row<?php echo $k; ?>'>
				<td align="center">
					<?php echo JHTML::_( 'grid.id', $i, $item->listid ); ?>
				</td>
				<td>
					<a href="<?php echo @$item->link; ?>">
					<?php echo @$item->newsletter_name; ?>
					</a>
				</td>	
		       	</tr>
			<?php $i=$i+1; $k = (1 - $k); ?>
			<?php endforeach; ?>
        </tbody>
	</table>
	<input type="button" class="button" onclick="submitform('cancel')" value="<?php echo JText::_( 'CANCEL' ); ?>" />
	<input type="button" class="button" onclick="submitform('unsubscribe')" value="<?php echo JText::_('UNSUBSCRIBE'); ?>"  />
	<?php endif; ?>
		
	<div id='onAfterDisplay_wrapper'>
		<?php $dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger( 'onAfterDisplayUnsubscribe', array( $this->row, $this->user ) ); ?>
	</div>

	<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="" />
	<input type="hidden" name="filter_order" value="<?php  echo @$state->order; ?>" />
	<input type="hidden" name="filter_direction" value="<?php echo @$state->direction; ?>" />
	<input type="hidden" name="uid" value="<?php echo $this->uid; ?>"/>
	<?php echo $this->form['validate']; ?>
</form>