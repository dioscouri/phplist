<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php JHTML::_('script', 'common.js', 'media/com_phplist/js/'); ?>
<?php $state = @$vars->state; ?>
<?php $items = @$vars->items; ?>

<?php echo JText::_( "Users Added to Newsletter" ); ?>

    <table class="adminlist" style="clear: both;">
        <thead>
            <tr>
                <th style="width: 5px;">
                    <?php echo JText::_("Num"); ?>
                </th>
                <th style="width: 50px;">
                    <?php echo JText::_("ID"); ?>
                </th>
                <th style="text-align: left;">
                    <?php echo JText::_("Email"); ?>
                </th>
                <th style="width: 100px; text-align: center;">
                    <?php echo JText::_("PhpList User Id"); ?>
                </th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="20">

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
                     <?php echo $item->juserid; ?>
                </td>
                <td style="text-align: left;">
                     <?php echo $item->email; ?>
                </td>
                <td style="text-align: center;">
                     <?php echo $item->userid; ?>
                </td>
            </tr>
            <?php ++$i; $k = (1 - $k); ?>
            <?php endforeach; ?>

            <?php if (!count(@$items)) : ?>
            <tr>
                <td colspan="10" align="center">
                    <?php echo JText::_('No items found'); ?>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
