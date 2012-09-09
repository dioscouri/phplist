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
            </td>
            <td nowrap="nowrap">
                <input id="search" name="filter" value="<?php echo @$state->filter; ?>" />
                <button onclick="this.form.submit();"><?php echo JText::_('SEARCH'); ?></button>
                <button onclick="Dsc.resetFormFilters(this.form);"><?php echo JText::_('Reset'); ?></button>
            </td>
        </tr>
    </table>

	<table class="adminlist" style="clear: both;">
		<thead>
            <tr>
                <th width="5">
                	<?php echo JText::_("NUM"); ?>
                </th>
                <th width="20">
                	<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->items ); ?>);" />
                </th>
                <th style="width: 50px;">
                    <?php echo PhplistGrid::sort( 'PHPList ID', "user.id", @$state->direction, @$state->order ); ?>
                </th>
                <th style="width: 50px;">
                    <?php echo PhplistGrid::sort( 'Joomla! ID', "user.foreignkey", @$state->direction, @$state->order ); ?>
                </th>
                <th>
                	<?php echo PhplistGrid::sort( 'Email', "user.email", @$state->direction, @$state->order ); ?>
                </th>
                <th>
                	<?php echo PhplistGrid::sort( 'Newsletter', "tbl.listid", @$state->direction, @$state->order ); ?>
                </th>
                <th style="width: 150px;">
                	<?php echo PhplistGrid::sort( 'Subscription Confirmed', "user.confirmed", @$state->direction, @$state->order ); ?>
                </th>
                <th style="width: 150px;">
                	<?php echo PhplistGrid::sort( 'Subscription Date', "tbl.entered", @$state->direction, @$state->order ); ?>
                </th>
                 <th style="width: 150px;">
                	<?php echo JText::_("UNSUBSCRIBE"); ?>
                </th>      
            </tr>
            <tr class="filter">
            	<th>
            	</th>
                <th colspan="2">
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
                    <?php $attribs = array('class' => 'inputbox', 'size' => '1', 'onchange' => 'document.adminForm.submit();'); ?>
                    <div class="range">
                        <div class="rangeline">
                            <span class="label"><?php echo JText::_("FROM"); ?>:</span> <input id="filter_joomlaid_from" name="filter_joomlaid_from" value="<?php echo @$state->filter_joomlaid_from; ?>" size="5" class="input" />
                        </div>
                        <div class="rangeline">
                            <span class="label"><?php echo JText::_("TO"); ?>:</span> <input id="filter_joomlaid_to" name="filter_joomlaid_to" value="<?php echo @$state->filter_joomlaid_to; ?>" size="5" class="input" />
                        </div>
                    </div>
                </th>                 
                <th style="text-align: center;">
                    <input id="filter_email" name="filter_email" value="<?php echo @$state->filter_email; ?>" size="25"/>
                </th>
                <th>
                    <?php echo PhplistSelect::newsletter( @$state->filter_listid, 'filter_listid', $attribs, 'listid', true ); ?>
                </th>
                <th>
                    <?php echo PhplistSelect::booleans( @$state->filter_confirmed, 'filter_confirmed', $attribs, 'confirmed', true, 'Select State', 'Confirmed', 'Unconfirmed' ); ?>
                </th>
                <th>
                    <div class="range">
                        <div class="rangeline">
                            <span class="label"><?php echo JText::_("FROM"); ?>:</span>
                            <?php echo JHTML::calendar( @$state->filter_date_from, "filter_date_from", "filter_date_from", '%Y-%m-%d %H:%M:%S' ); ?>
                        </div>
                        <div class="rangeline">
                            <span class="label"><?php echo JText::_("TO"); ?>:</span>
                            <?php echo JHTML::calendar( @$state->filter_date_to, "filter_date_to", "filter_date_to", '%Y-%m-%d %H:%M:%S' ); ?>
                        </div>
                    </div>
                </th>
                <th>
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
					<?php echo JHTML::_( 'grid.id', $i, $item->userid . ',' . $item->listid); ?>
				</td>
                <td align="center">
                    <?php echo $item->user_id; ?>
                </td>
                <td align="center">
                    <?php echo $item->foreignkey; ?>
                </td>
				<td style="text-align: center;">
					<?php echo $item->user_email; ?><br/>
					[ <a href="<?php echo $item->edit_link; ?>"><?php echo JText::_( 'EDIT_USER' ); ?></a> | <a href="<?php echo $item->veiw_link; ?>"><?php echo JText::_( 'VIEW USER' ); ?></a> ]
				</td>
				<td style="text-align: center;">
					<?php echo JText::_( $item->newsletter_name ); ?>
				</td>	
				<td style="text-align: center;">
					<?php if ($item->user_confirmed == '1')
					{ 
						echo '<img border="0" alt="Enabled" src="../media/dioscouri/images/tick.png"/>';
					}
					else
					{
						echo '<img border="0" alt="Disabled" src="../media/dioscouri/images/publish_x.png"/>';
					}
					?>
				</td>
				<td style="text-align: center;">
					<?php echo JHTML::_( "date", $item->entered,  JText::_('DATE_FORMAT_LC2') ); ?>
				</td>
				<td style="text-align: center;">
				[ <a href="<?php echo $item->link ?>">
					<?php echo JText::_( 'UNSUBSCRIBE' ); ?>
				</a> ]
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