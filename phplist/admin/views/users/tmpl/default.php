<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php JHTML::_('script', 'common.js', 'media/com_phplist/js/'); ?>
<?php $state = @$this->state; ?>
<?php $form = @$this->form; ?>
<?php $items = @$this->items; ?>

<form action="<?php echo JRoute::_( @$form['action'] )?>" method="post" name="adminForm" enctype="multipart/form-data">

	<?php echo PhplistGrid::pagetooltip( JRequest::getVar('view') ); ?>
	    
    <table>
        <tr>
            <td align="left" width="100%">
                <?php echo JText::_( 'FLEX' ).': ' . PhplistSelect::newsletter( @$state->flex_list, 'flex_list', '', 'listid', true ); ?>
            </td>
            <td nowrap="nowrap">
                <input id="search" name="filter" value="<?php echo @$state->filter; ?>" />
                <button onclick="this.form.submit();"><?php echo JText::_('SEARCH'); ?></button>
                <button onclick="phplistResetFormFilters(this.form);"><?php echo JText::_('Reset'); ?></button>
            </td>
        </tr>
    </table>

	<table class="adminlist" style="clear: both;">
		<thead>
            <tr>
                <th style="width: 5px;">
                	<?php echo JText::_("NUM"); ?>
                </th>
                <th width="20">
                	<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->items ); ?>);" />
                </th>
                <th style="width: 50px;">
                	<?php echo PhplistGrid::sort( 'PHPList ID', "tbl.id", @$state->direction, @$state->order ); ?>
                </th>
                 <th style="width: 50px;">
                	<?php echo PhplistGrid::sort( 'Joomla! ID', "tbl.foreignkey", @$state->direction, @$state->order ); ?>
                </th>             
                <th>
                	<?php echo JText::_( "NAME" ); ?>
                </th>
                <th>
                	<?php echo JText::_( "USERNAME" ); ?>
                </th>
				<th>
					<?php echo PhplistGrid::sort( 'EMAIL', 'tbl.email', @$state->direction, @$state->order); ?>
				</th>
				<th>
					<?php echo JText::_( "ATTRIBUTES" ); ?>
				</th>
				<th>
					<?php echo JText::_( "SUBSCRIPTIONS" ); ?>
				</th>
				<th>
					<?php echo PhplistGrid::sort( 'HTML/Text', 'tbl.htmlemail', @$state->direction, @$state->order); ?>
				</th>
				<th style="width: 150px;">
                	<?php echo PhplistGrid::sort( 'Confirmed', "tbl.confirmed", @$state->direction, @$state->order ); ?>
                </th>
            </tr>
            <tr class="filter">
                <th colspan="3">
                    <?php $attribs = array('class' => 'inputbox', 'size' => '1', 'onchange' => 'document.adminForm.submit();'); ?>
                    <div class="range">
                        <div class="rangeline">
                            <span class="label"><?php echo JText::_("FROM"); ?>:</span> <input id="filter_id_from" name="filter_id_from" value="<?php echo @$state->filter_id_from; ?>" size="5" class="input" />
                        </div>
                        <div class="rangeline">
                            <span class="label"><?php echo JText::_("TO"); ?>:</span> <input id="filter_id_to" name="filter_id_to" value="<?php echo @$state->filter_id_to; ?>" size="5" class="input" />
                        </div>
                    </div>
                </th>
                <th>
                    <div class="range">
                        <div class="rangeline">
                            <span class="label"><?php echo JText::_("FROM"); ?>:</span> <input id="filter_foreignkey_from" name="filter_foreignkey_from" value="<?php echo @$state->filter_foreignkey_from; ?>" size="5" class="input" />
                        </div>
                        <div class="rangeline">
                            <span class="label"><?php echo JText::_("TO"); ?>:</span> <input id="filter_foreignkey_to" name="filter_foreignkey_to" value="<?php echo @$state->filter_foreignkey_to; ?>" size="5" class="input" />
                        </div>
                    </div>
                </th>                    
                <th>
                </th>
                 <th>
                </th>
                <th style="text-align: center;">
                    <input id="filter_email" name="filter_email" value="<?php echo @$state->filter_email; ?>" size="25"/>
                </th>
                <th>
                </th>
                <th>
                </th>
                <th style="text-align: center;">
                    <?php echo PhplistSelect::booleans( @$state->filter_html, 'filter_html', $attribs, 'htmlemail', true, 'Select Type', 'HTML', 'Text' ); ?>
                </th>
                <th>
                    <?php echo PhplistSelect::booleans( @$state->filter_confirmed, 'filter_confirmed', $attribs, 'confirmed', true, 'Select State', 'Confirmed', 'Unconfirmed' ); ?>
                </th>
            </tr>
            <tr>
                <th colspan="20" style="font-weight: normal;">
                    <div style="float: right; padding: 5px;"><?php echo @$this->pagination->getResultsCounter(); ?></div>
                    <div style="float: left;"><?php echo @$this->pagination->getListFooter(); ?></div>
                </th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="20">
                    <div style="float: right; padding: 5px;"><?php echo @$this->pagination->getResultsCounter(); ?></div>
                    <?php echo @$this->pagination->getPagesLinks(); ?>
                </td>
            </tr>
        </tfoot>
        <tbody>
		<?php $i=0; $k=0; ?>
        <?php foreach (@$items as $item) : ?>
            <tr class='row<?php echo $k; ?>'>
				<td align="center">
					<?php echo $i + 1; ?>
				</td>
				<td style="text-align: center;">
					<?php echo JHTML::_( 'grid.id', $i, $item->id ); ?>
				</td>
				<td style="text-align: center;">
					<a href="<?php echo $item->link; ?>">
						<?php echo $item->id; ?>
					</a>	
				</td>
				<td style="text-align: center;">
						<?php echo $item->foreignkey; ?>
				</td>		
				<td style="text-align: center;">
					<?php echo $item->joomlaName; ?>
				</td>
				<td style="text-align: center;">
					<?php echo $item->joomlaUsername; ?>
				</td>
				<td style="text-align: center;">
					<a href="<?php echo $item->link; ?>">
						<?php echo $item->email; ?>
					</a>	
				</td>
				<td style="text-align: left;">
				
					<?php echo $item->attributes_list; ?>
				</td>
				<td style="text-align: center;">
					<?php echo $item->subscriptions; ?>
				</td>
				<td style="text-align: center;">
					<?php echo $item->html; ?>
				</td>
				<td style="text-align: center;">
					<?php echo PhplistGrid::enable($item->confirmed, $i, 'confirmed.' ); ?>
				</td>
			</tr>
			<?php $i=$i+1; $k = (1 - $k); ?>
			<?php endforeach; ?>
			
			<?php if (!count(@$items)) : ?>
			<tr>
				<td colspan="10" align="center">
					<?php echo JText::_('NO_ITEMS_FOUND'); ?>
				</td>
			</tr>
			<?php endif; ?>
		</tbody>
	</table>

	<input type="hidden" name="order_change" value="0" />
	<input type="hidden" name="id" value="" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="" />
	<input type="hidden" name="filter_order" value="<?php echo @$state->order; ?>" />
	<input type="hidden" name="filter_direction" value="<?php echo @$state->direction; ?>" />
	
	<?php echo $this->form['validate']; ?>
</form>