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
                	<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( @$items ); ?>);" />
                </th>
                <th style="width: 50px;">
                	<?php echo PhplistGrid::sort( 'ID', "tbl.id", @$state->direction, @$state->order ); ?>
                </th>
                <th style="text-align: left;">
                	<?php echo PhplistGrid::sort( 'Name', "tbl.name", @$state->direction, @$state->order ); ?>
                </th>
                <th style="width: 150px;">
                    <?php echo JText::_( 'Last Message Sent' ); ?>
                </th>
                <th style="width: 100px;">
                	<?php echo PhplistGrid::sort( 'Order', "tbl.listorder", @$state->direction, @$state->order ); ?>
    	            <?php echo JHTML::_('grid.order', @$items ); ?>
                </th>
                <th style="width: 150px;">
                	<?php echo PhplistGrid::sort( 'Published', "tbl.active", @$state->direction, @$state->order ); ?>
                </th>
                <th style="width: 150px;">
                	<?php echo JText::_( 'SUBSCRIBERS' ); ?>
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
                <th style="text-align: left;">
                    <input id="filter_name" name="filter_name" value="<?php echo @$state->filter_name; ?>" size="25"/>
                </th>
                <th>
                </th>
                <th>
                </th>
                <th>
                    <?php echo PhplistSelect::booleans( @$state->filter_active, 'filter_active', $attribs, 'active', true ); ?>
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
				<td style="text-align: left;">
					<a href="<?php echo $item->link; ?>">
						<?php echo $item->name; ?>
					</a>
				</td>
				<td style="text-align: center;">
				    <?php echo @$item->lastMailingDate; ?>
				</td>
				<td style="text-align: center;">
					<?php echo PhplistGrid::order($item->id); ?>
					<?php echo PhplistGrid::ordering($item->id, $item->listorder ); ?>
				</td>
				<td style="text-align: center;">
					<?php echo PhplistGrid::enable($item->active, $i, 'active.' ); ?>
				</td>
				<td style="text-align: center;">
				    <?php $NumSubscribers = PhplistHelperNewsletter::getNumSubscribers($item->id);
				   echo $NumSubscribers. ' ';
				   echo ($NumSubscribers == '1') ? JText::_( "SUBSCRIBER" ) : JText::_( "SUBSCRIBERS" ); ?>
				    <br/>
					[<a href="<?php echo $item->link_subscribers;?>">
					   <?php echo JText::_( 'VIEW SUBSCRIBERS' ); ?>
					</a>]
				</td>
			</tr>
                <?php
                if (isset($item->description) && strlen($item->description) > 1) 
                {
                    $text_display = "[ + ]";
                    $text_hide = "[ - ]";
                    $onclick = "displayDiv(\"description_{$item->id}\", \"showhidedescription_{$item->id}\", \"{$text_display}\", \"{$text_hide}\");";
                    ?>
                    <tr class='row<?php echo $k; ?>'>
                        <td style="vertical-align: top; white-space:nowrap;">
                            <span class='href' id='showhidedescription_<?php echo $item->id; ?>' onclick='<?php echo $onclick; ?>'><?php echo $text_display; ?></span>
                        </td>
                        <td colspan='10'> 
                            <div id='description_<?php echo $item->id; ?>' style='display: none;'>
                            <?php
                            $fulltext = nl2br( htmlspecialchars_decode( stripslashes( strip_tags( $item->description ) ) ) );
                            $dispatcher = JDispatcher::getInstance();
                            $dispatcher->trigger( 'onBBCode_RenderText', array(&$fulltext) );
                            echo $fulltext;
                            ?>
                            </div>
                        </td>
                    </tr>
                    <?php
                }
                ?>
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
    
	<input type="hidden" name="order_change" value="0" />
	<input type="hidden" name="id" value="" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="" />
	<input type="hidden" name="filter_order" value="<?php echo @$state->order; ?>" />
	<input type="hidden" name="filter_direction" value="<?php echo @$state->direction; ?>" />
	
	<?php echo $this->form['validate']; ?>
</form>