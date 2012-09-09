<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php JHTML::_('stylesheet', 'phplist.css', 'media/com_phplist/css/'); ?>
<?php JHTML::_('stylesheet', 'menu.css', 'media/com_phplist/css/'); ?>
<?php JHTML::_('script', 'common.js', 'media/com_phplist/js/'); ?>
<?php JHTML::_('script', 'joomla.javascript.js', 'includes/js/'); ?>
<?php JHTML::_('behavior.mootools' );  ?>
<?php $state = @$this->state; ?>
<?php $form = @$this->form; ?>
<?php $items = @$this->items; ?>
<?php $row = @$this->row; ?>
<?php $attributes = @$this->attributes; ?>

<?php $subscribed_src = JURI::root()."/media/com_phplist/images/"."accept.png"; ?>
<?php $unsubscribed_src = JURI::root()."/media/com_phplist/images/"."remove.png"; ?>
<?php $subscribe_src = JURI::root()."/media/com_phplist/images/"."add.png"; ?>
<?php $url_validate = JRoute::_( 'index.php?option=com_phplist&controller=newsletters&view=newsletters&task=validate&format=raw' ); ?>

<?php $htmlemail = isset($this->phplistuser->htmlemail) ? $this->phplistuser->htmlemail : 1; ?>

<div class='componentheading'>
	<span><?php echo JText::_( "Newsletters" ); ?></span>
</div>

<?php 
// menu not needed here at present if not logged in
if ($this->phplistuser)
{
	 echo DSCMenu::getInstance('submenu')->display(); 
}
?>

<form action="<?php echo JRoute::_( @$form['action'] ) ?>" method="post" class="adminform" name="adminForm" id="adminForm" enctype="multipart/form-data" onsubmit="phplistFormValidation( '<?php echo $url_validate; ?>', 'validationmessage', document.adminForm.task.value, document.adminForm )">

    <div id='onBeforeDisplay_wrapper'>
	    <?php 
	    $dispatcher = JDispatcher::getInstance();
	    $dispatcher->trigger( 'onBeforeDisplayNewsletters', array( @$this->row, @$this->user ) );
	    ?>
    </div>
<div id="validationmessage"></div>
    <table  width="100%" class="adminform">
       	<tr>
            <td align="left" width="60%">
		    	<?php if ($this->phplistuser) : ?>
			       <div style="vertical-align: middle;">
			       	<b><?php echo JText::_( "Your Email"). ': '; ?></b> <?php echo $this->phplistuser->email; ?>
			       	<?php if ($this->joomlauserID == '0') { ?>
			       		[<a href="<?php echo JRoute::_('index.php?option=com_phplist&view=newsletters&task=list', false); ?>" alt="logout" title="logout"><?php echo JText::_("Logout"); ?></a>]
			       <?php } ?>
			       <br/><br/>
			       	<?php echo JText::_( "WITH_SELECTED" ); ?>
			       	<img src="<?php echo $subscribe_src; ?>" style="max-height: 14px; padding-left:7px;" onclick="Dsc.submitForm('subscribe_selected')" onmouseover="this.style.cursor='pointer'" alt="<?php echo JText::_("SUBSCRIBE"); ?>" /> 
					<a href="javascript:void(0);" style="padding-left:3px;" onclick="Dsc.submitForm('subscribe_selected')">
						<?php echo JText::_("SUBSCRIBE"); ?>
					</a>
					<img src="<?php echo $unsubscribed_src; ?>" style="max-height: 14px; padding-left:7px;" onclick="Dsc.submitForm('unsubscribe_selected')" onmouseover="this.style.cursor='pointer'" alt="<?php echo JText::_("UNSUBSCRIBE"); ?>" /> 
					<a href="javascript:void(0);" style="padding-left:3px;" onclick="Dsc.submitForm('unsubscribe_selected')">
						<?php echo JText::_("UNSUBSCRIBE"); ?>
					</a>
					<input type="hidden" name="uid" value="<?php echo $this->phplistuser->uniqid; ?>" />
				</div>
				<?php else : ?>
			  	<fieldset>
			  		<legend>
			  			<?php echo JText::_( "SUBSCRIBE" ); ?>
			  		</legend>
			  		<table class="adminform">
			  			<tr>
			  				<th>
			  					<?php echo JText::_("EMAIL"); ?>:
			  				</th>
			  				<td>
			  					<input type="text" name="subscriber2add" id="subscriber2add" value="" />
			  				</td>
			  			</tr>
					 	<?php if ($attributes) :
								for ($r=0; $r<count($attributes); $r++) :
									$attr = $attributes[$r];
									$title = $attr->name;
									$input = PhplistHelperAttribute::formInput( $attr->id, '', $attr->type, $attr->name);
									if ($input != NULL) : ?>
						<tr>
							<th style="width: 25%;">
								<?php echo JText::_($title); ?>
							</th>
							<td>
								<?php echo $input; if ($attr->required == '1') echo "*"; ?>
							</td>
						</tr>
									<?php endif; ?>
								<?php endfor; ?>
							<?php endif; ?>
						<tr>
							<th>
								<?php echo JText::_("WOULD_YOU_PREFER_HTML_EMAILS")."? "; ?>
							</th>
							<td>
								<?php echo JHTML::_('select.booleanlist', 'htmlemail', 'class="inputbox"', $htmlemail ); ?>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<img src="<?php echo $subscribe_src; ?>" style="max-height: 24px; vertical-align: middle;" onclick="Dsc.submitForm('subscribe_new')" onmouseover="this.style.cursor='pointer'" alt="<?php echo JText::_("SUBSCRIBE"); ?>" />
			  					<a href="javascript:void(0);" onclick="Dsc.submitForm('subscribe_new')">
			  						<?php echo JText::_("SUBSCRIBE_TO_SELECTED"); ?>
			  					</a>
			  				</td>
			  			</tr>
            		</table> 
            	</fieldset>  
            </td>
            <td valign="top">
            	<fieldset>
            		<legend>
            			<?php echo JText::_("CHANGE_YOUR_SUBSCRIPTIONS"); ?>
            		</legend>
            		<?php echo JText::_( "PLEASE_LOGIN_IF_REGISTERED" ); ?>
            	</fieldset>
            <?php endif; ?>
            </td>
		</tr>
		<tr>
            <td align="right" valign="bottom" style="white-space: nowrap;" colspan="2">
            	<input type="text" name="filter" id="filter" value="<?php echo @$state->filter; ?>" />
               	<input type="button" onclick="this.form.submit();" value="<?php echo JText::_('SEARCH'); ?>" />
               	<input type="button" onclick="document.getElementById('filter').value=''; this.form.submit();" value="<?php echo JText::_('RESET'); ?>" />
            </td>
        </tr>
	</table>
	<table class="adminlist" style="clear: both;">
		<thead>
            <tr>
                <th width="5">
                	<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->items ); ?>);" />
                </th>
                <th>
                	<?php echo DSCGrid::sort( 'Title', "tbl.name", @$state->direction, @$state->order ); ?>
                </th>
                <th>
                	<?php echo DSCGrid::sort( 'Last Message Sent', "lastsent", @$state->direction, @$state->order ); ?>
                </th>
                <?php if ($this->phplistuser) : ?>
				<th>
					<?php echo JText::_( 'SUBSCRIPTION_STATUS' ); ?>
				</th>
                   <?php endif; ?>
            </tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="20">
					<div style="float: right; padding: 5px;">
						<?php echo @$this->pagination->getResultsCounter(); ?>
					</div>
					<?php echo @$this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
        <tbody>
			<?php $i=0; $k=0; ?>
        	<?php foreach (@$items as $item) : ?>
            <tr class='row<?php echo $k; ?>'>
				<td align="center">
					<?php echo JHTML::_( 'grid.id', $i, $item->id ); ?>
				</td>
				<td style="text-align: center;">
					<?php echo JText::_(@$item->name); ?><br/>
					[<a href="<?php echo PhplistUrl::siteLink(@$item->link_messages); ?>">
						<?php echo JText::_( 'READ_MESSAGES' ); ?>
					</a>]
				</td>	
				<td style="text-align: center;">				
				<?php if (@$item->lastsent != '')
						echo JHTML::_( "date", @$item->lastsent, JText::_('DATE_FORMAT_LC1') );
						else echo JText::_( "NO_MAILINGS_SENT" ); ?>			
				</td>
				<?php
                if ($this->phplistuser) :
					if ($isUser = PhplistHelperSubscription::isUser( $this->phplistuser->id, $item->id ))
						$img = JURI::root()."/media/com_phplist/images/"."accept.png";
						else $img = JURI::root()."/media/com_phplist/images/"."remove.png";?>
				<td style="text-align: center;">
					<span class="substatus-container">
						<a href="<?php echo $item->link_switch; ?>" style='text-decoration: none;'>
		                  <img src="<?php echo $img ?>" style="max-height: 24px;" onclick="Dsc.submitForm('subscribe_selected')" alt="subcribe selected" />
		                </a>
					</span>	
				</td>	
				<?php endif; ?>
			</tr>
		       
		    <?php if (isset($item->description) && strlen($item->description) > 1) : ?>
			<tr class='row<?php echo $k; ?>'>
		       	<td style="vertical-align:top; white-space:nowrap;">
					<span class='href' id='showhidedescription_<?php echo $item->id; ?>' onclick="Dsc.displayDiv('description_<?php echo $item->id; ?>', 'showhidedescription_<?php echo $item->id; ?>', '<?php echo JText::_('SHOW DESCRIPTION'); ?>', '<?php echo JText::_('HIDE DESCRIPTION'); ?>')">
		    			<?php echo JText::_('SHOW_DESCRIPTION'); ?>
					</span>
				</td>
				<td style="vertical-align:top;" colspan='10'> 
					<div id='description_<?php echo $item->id; ?>' style='display:none;'>
						<?php echo JText::_($item->description); ?>
					</div>
				</td>
			</tr>
			<?php endif; ?>
				
			<?php $i=$i+1; $k = (1 - $k); ?>
			<?php endforeach; ?>
			
			<?php if (!count(@$items)) : ?>
			<tr>
				<td colspan="10" align="center">
					<?php echo JText::_('NO_ITEMS_FOUND'); ?>
				</td>
			</tr>
			<?php endif;
			if (!$this->phplistuser) : ?>			
			<tr>
				<td colspan="3">
					<i>
						<?php echo JText::_('UNSUBSCRIBE_FOOTER_MESSAGE'); ?>
					</i>
				</td>
			</tr>
			<?php endif; ?>
		</tbody>
	</table>
	
	<div id='onAfterDisplay_wrapper'>
		<?php 
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger( 'onAfterDisplayNewsletters', array( @$this->row, @$this->user ) );
		?>
	</div>

    <input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="" />
	<input type="hidden" name="filter_order" value="<?php echo @$state->order; ?>" />
	<input type="hidden" name="filter_direction" value="<?php echo @$state->direction; ?>" />
	<input type="hidden" name="uid" value="<?php echo $this->uid; ?>"/>
	<?php echo $this->form['validate']; ?>
</form>