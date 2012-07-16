<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php $form = @$this->form; ?>
<?php $row = @$this->row; ?>

<form action="<?php echo JRoute::_( @$form['action'] ) ?>" method="post" class="adminform" name="adminForm" >

    <?php echo PhplistGrid::pagetooltip( JRequest::getVar('view') ); ?>

	<h3>
		<?php echo @$row->name ?>
	</h3>
	
	<?php
	    $dispatcher = JDispatcher::getInstance();
	    $dispatcher->trigger( 'onDisplayToolPhplist', array( $row ) );
	?>
	
	<?php
	echo $form['validate'];
	?>
        
	<input type="hidden" name="id" value="<?php echo @$row->id; ?>" />
	<input type="hidden" name="task" id="task" value="" />
</form>