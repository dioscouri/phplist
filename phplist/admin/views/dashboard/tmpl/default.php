<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php $stats = @$this->statistics; ?>

<?php echo PhplistGrid::pagetooltip( JRequest::getVar('view') ); ?>

<table style="width: 100%;">
<tr>
	<td style="width: 70%; max-width: 70%; vertical-align: top; padding: 0px 5px 0px 5px;">
	
            <?php
            jimport('joomla.html.pane');
            $tabs = JPane::getInstance( 'tabs' );

            echo $tabs->startPane("tabone");
            echo $tabs->startPanel( JText::_( 'NEW SUBSCRIPTIONS' ), "subscriptions" );

                echo "<h2>".@$this->lastThirty->title."</h2>";
                echo @$this->lastThirty->image;

            echo $tabs->endPanel();
            echo $tabs->endPane();
            ?>
	
		<?php 		$modules = JModuleHelper::getModules("phplist_dashboard_main");
		$document	= &JFactory::getDocument();
		$renderer	= $document->loadRenderer('module');
		$attribs 	= array();
		$attribs['style'] = 'xhtml';
		foreach ( @$modules as $mod ) 
		{
			echo $renderer->render($mod, $attribs);
		}
		?>
	</td>
	<td style="vertical-align: top; width: 30%; min-width: 30%; padding: 0px 5px 0px 10px;">
	
        <table class="adminlist" style="margin-bottom: 5px;">
        <thead>
            <tr>
                <th colspan="2"><?php echo JText::_( "SUBSCRIPTIONS PER NEWSLETTER" ); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php
            foreach ($this->newsletters as $newsletter)
            {
            	?>
	            <tr>
	                <th><?php echo JText::_( $newsletter->name ); ?></th>
	                <td style="text-align: right;">
	                	<?php $NumSubscribers = PhplistHelperNewsletter::getNumSubscribers($newsletter->id);
	                	echo PhplistHelperBase::number( $NumSubscribers);
	                	 ?>
	                </td>
	            </tr>            	
            	<?php
            }
        ?>
		</tbody>
		</table>
		
		<?php 		$modules = JModuleHelper::getModules("phplist_dashboard_right");
		$document	= &JFactory::getDocument();
		$renderer	= $document->loadRenderer('module');
		$attribs 	= array();
		$attribs['style'] = 'xhtml';
		foreach ( @$modules as $mod ) 
		{
			echo $renderer->render($mod, $attribs);
		}
		?>
	</td>
</tr>
</table>