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
                <button onclick="phplistResetFormFilters(this.form);"><?php echo JText::_('Reset'); ?></button>
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
                	<?php echo PhplistGrid::sort( 'ID', "tbl.id", @$state->direction, @$state->order ); ?>
                </th>
                <th>
                	<?php echo PhplistGrid::sort( 'Subject', "tbl.subject", @$state->direction, @$state->order ); ?>
                </th>
                <th style="width: 150px;">
                	<?php echo PhplistGrid::sort( 'Embargo Date', "tbl.embargo", @$state->direction, @$state->order ); ?>
                </th>
                <th style="width: 150px;">
                	<?php echo PhplistGrid::sort( 'Date Sent', "tbl.sent", @$state->direction, @$state->order ); ?>
                </th>
                <th style="width: 120px;">
                	<?php echo PhplistGrid::sort( 'Status', "tbl.status", @$state->direction, @$state->order ); ?>
                </th>
                <th>
                	<?php echo JText::_( 'NEWSLETTERS' ); ?>
                </th>
                <th style="width: 200px;">
                	<?php echo JText::_( 'DATA' ); ?>
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
                <th style="text-align: center;">
                    <input id="filter_subject" name="filter_subject" value="<?php echo @$state->filter_subject; ?>" size="25"/>
                </th>
                <th style="text-align: center;">
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
                    <?php echo PhplistSelect::messagestate( @$state->filter_messagestate, 'filter_messagestate', $attribs, 'messagestate', true ); ?>                    
                </th>
                <th>
                    <?php echo PhplistSelect::newsletter( @$state->filter_listid, 'filter_listid', $attribs, 'listid', true ); ?>
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
					<?php echo JHTML::_( 'grid.id', $i, $item->id ); ?>
				</td>
				<td style="text-align: center;">
					<a href="<?php echo $item->link; ?>">
						<?php echo $item->id; ?>
					</a>
				</td>	
				<td style="text-align: center;">
					<a href="<?php echo $item->link; ?>">
						<?php echo $item->subject; ?>
					</a>
				</td>	
				<td style="text-align: center;">
					<?php echo $item->embargo; ?>
				</td>	
				<td style="text-align: center;">
					<?php echo $item->sent; ?>
				</td>
				<td style="text-align: center;">
					<?php echo $item->status; ?>
					<br/>
					[<a href="<?php echo $item->link_status; ?>">
						<?php echo $item->link_status_text; ?>
					</a>]
				</td>
				<td style="text-align: center;">
					<?php echo $item->newsletters; ?>
				</td>
				<td style="text-align: center;">
					<?php 	switch (strtolower($item->status))
						{
						case "sent": ?>
							<strong><?php echo JText::_( "TIME TO SEND" ); ?></strong>: <?php echo $item->timetosend; ?><br/>
							<strong><?php echo JText::_( "PROCESSED" ); ?></strong>: <?php echo $item->sent_processed; ?><br/>
							<strong><?php echo JText::_( "TEXT" ); ?></strong>: <?php echo $item->sent_text; ?><br/>
							<strong><?php echo JText::_( "HTML" ); ?></strong>: <?php echo $item->sent_html; ?><br/>
							<strong><?php echo JText::_( "PDF" ); ?></strong>: <?php echo $item->sent_pdf; ?><br/>
							<strong><?php echo JText::_( "TOTAL SENT" ); ?></strong>: <?php echo $item->sent_total; ?><br/>
						  <?php break;
						case "inprocess": ?>
							<strong><?php echo JText::_( "STILL TO PROCESS" ); ?></strong>: <?php echo $item->to_process; ?><br/>
							<strong><?php echo JText::_( "ETA" ); ?></strong>: <?php echo $item->eta; ?><br/>
							<strong><?php echo JText::_( "MESSAGES PER HOUR" ); ?></strong>: <?php echo $item->mph; ?><br/>
						  <?php break;
						case "submitted": ?>
							<?php echo JText::_( "Awaiting queue to be processed by cronjob" ); ?>
						<?php break;
						default:
						  break;
					}  ?>
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
        
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="" />
	<input type="hidden" name="filter_order" value="<?php echo @$state->order; ?>" />
	<input type="hidden" name="filter_direction" value="<?php echo @$state->direction; ?>" />
	
	<?php echo $this->form['validate']; ?>
</form>