<?php
	defined('_JEXEC') or die('Restricted access');
	JHtml::_('behavior.tooltip');
	JHtml::_('behavior.formvalidation');
	$user = JFactory::getUser();
	$uri = JURI::getInstance();
	$base = $uri->root();
?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<input type="hidden" name="option" value="com_mapbox" />
	<input type="hidden" name="scope" value="" />
	<input type="hidden" name="task" value="markers.filter" />
	<input type="hidden" name="chosen" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filter->filter_order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filter->filter_order_Dir; ?>" />
	<?php echo JHTML::_('form.token')."\n"; ?>
	<table class="table table-bordered">
		<tr>
			<td width="100%">
				<label for=""><?php echo JText::_('COM_MAPBOX_FILTER_SEARCH_LABEL'); ?></label>
				<input type="text" name="filter_search" id="filter-search_" class="input-small" value="<?php echo $this->filter->filter_search; ?>" />
				<input type="button" name="submit_button" id="submit-button_" value="Go" onclick="document.forms.adminForm.task.value='filter';document.forms.adminForm.submit();"/>
				<input type="button" name="reset_button" id="reset-button_" value="Reset" onclick="document.forms.adminForm.filter_search.value='';document.forms.adminForm.task.value='filter';document.forms.adminForm.submit();"/>
			</td>
		</tr>
	</table>
	<table class="adminlist">
		<thead>
			<tr>
				<th width="5">
					<?php echo JText::_('Num'); ?>
				</th>
				<th width="5">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this);" />
				</th>
				<th class="title" nowrap="nowrap">
					<?php echo JHTML::_('grid.sort', 'COM_MAPBOX_LIST_MARKER_NAME_LABEL', 'marker_name', $this->filter->filter_order_Dir, $this->filter->filter_order, 'markers.filter'); ?>
				</th>
				<th width="5%" nowrap="nowrap">
					<?php echo JHTML::_('grid.sort', 'COM_MAPBOX_LIST_PUBLISHED_LABEL', 'published', $this->filter->filter_order_Dir, $this->filter->filter_order, 'markers.filter'); ?>
				</th>
				<th width="10%" nowrap="nowrap">
					<?php echo JHTML::_('grid.sort', 'COM_MAPBOX_LIST_ORDERING_LABEL', 'map.ordering, marker.ordering', $this->filter->filter_order_Dir, $this->filter->filter_order, 'markers.filter');?>
					<?php echo JHTML::_('grid.order', $this->items, 'filesave.png', 'markers.saveorder'); ?>
				</th>
				<th nowrap="nowrap">
					<?php echo JHTML::_('grid.sort', 'COM_MAPBOX_LIST_ACCESS_LABEL', 'marker.access', $this->filter->filter_order_Dir, $this->filter->filter_order, 'markers.filter'); ?>
				</th>
				<th>
				    <?php echo JHtml::_('grid.sort', 'COM_MAPBOX_MAP_NAME_LABEL', 'map_name', $this->filter->filter_order_Dir, $this->filter->filter_order, 'markers.filter'); ?>
				</th>
				<th>
					<?php echo JText::_('COM_MAPBOX_LIST_DESCRIPTION_LABEL'); ?>
				</th>
				<th width="1%">
					<?php echo JText::_('COM_MAPBOX_LIST_ID_LABEL'); ?>
				</th>
			</tr>
		</thead>
		<tbody>
		<?php
		$k = 0;
		for($i=0; $i < count($this->items); $i++){
			$row		= $this->items[$i];
			$checked	= JHTML::_('grid.id', $i, $row->marker_id);
			$link		= JRoute::_('index.php?option=com_mapbox&task=markers.edit&marker_id='. $row->marker_id.'&'.JSession::getFormToken().'=1');
			$canEdit	= $user->authorise('core.edit', 'com_mapbox');
			$canCheckin	= $user->authorise('core.manage', 'com_checkin') || $row->checked_out==$user->get('id') || $row->checked_out==0;
			$canChange	= $user->authorise('core.edit.state', 'com_mapbox') && $canCheckin;
			$desc       = explode(" ", strip_tags($row->marker_description));
			?>
			<tr class="row<?php echo $k; ?>">
				<td>
					<?php echo $this->page->getRowOffset($i); ?>
				</td>
				<td align="center">
					<?php echo $checked; ?>
				</td>
				<td  nowrap="nowrap">
					<?php
					if($row->checked_out){
						echo JHTML::_('jgrid.checkedout', $i, $row->editor, $row->checked_out_time, 'markers.', $canCheckin);
						echo htmlspecialchars($row->marker_name, ENT_QUOTES);
					}else{
					    if($canEdit){
						    echo "<a href=\"{$link}\">" . htmlspecialchars($row->marker_name, ENT_QUOTES) . "</a>";
						}else{
						    echo htmlspecialchars($row->marker_name, ENT_QUOTES);
						}
					}
					?>
				</td>
				<td align="center">
					<?php echo JHtml::_('jgrid.published', $row->published, $i, 'markers.', true, 'cb'); ?>
				</td>
				<td class="order">
					<span><?php echo $this->page->orderUpIcon( $i, ($this->page->getRowOffset($i) > 1 && $row->ordering > 1), 'markers.orderup', 'Move Up'); ?></span>
					<span><?php echo $this->page->orderDownIcon( $i, count($this->items), (@$this->items[$i+1]->map_id == $row->map_id), 'markers.orderdown', 'Move Down'); ?></span>
					<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="text_area" style="text-align: center" />
				</td>
				<td align="center">
					<?php echo $row->access; ?>
				</td>
				<td>
				    <?php echo $row->map_name; ?>
				</td>
				<td>
					<?php echo implode(" ", array_splice($desc, 0, 55)); ?>
				</td>
				<td>
					<?php echo $row->marker_id; ?>
				</td>
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="10">
					<?php echo $this->page->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
	</table>
</form>
