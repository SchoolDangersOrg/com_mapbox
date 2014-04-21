<?php
	defined('_JEXEC') or die('Restricted access');
    JHtml::_('behavior.tooltip');
    JHtml::_('behavior.formvalidation');
	$key = JComponentHelper::getParams('com_mapbox')->get('default_api_key');
	$doc = JFactory::getDocument();
    $doc->addScript("https://api.tiles.mapbox.com/mapbox.js/v1.6.2/mapbox.js");
    $doc->addStylesheet("https://api.tiles.mapbox.com/mapbox.js/v1.6.2/mapbox.css");
    $doc->addStyleDeclaration("#twukSoWweucw { position: absolute; width: 100%; height: 100%;}");
?>

<script type="text/javascript">
//<![CDATA[
	window.addEvent('domready', function(){
		document.formvalidator.setHandler('uint', function(value){
			re_uint = /^\d+$/;
			return re_uint.test(value);
		});
		document.formvalidator.setHandler('string', function(value){
			re_string = /^([\w\d\s-_\.,&'#]+)?$/;
			return re_string.test(value);
		});
		document.formvalidator.setHandler('cmd', function(value){
			re_cmd = /^([\w-_]+)$/;
			return re_cmd.test(value);
		});
		
		map = L.mapbox.map('twukSoWweucw', '<?php echo $this->form->getValue("map_api_key", null, $key); ?>').setView([<?php echo $this->form->getValue('params.center_lat', null, 0); ?>,<?php echo $this->form->getValue('params.center_lng', null, 0); ?>],<?php echo $this->form->getValue('params.zoom', null, 2); ?>);
		marker = L.marker([<?php echo $this->form->getValue('params.center_lat'); ?>,<?php echo $this->form->getValue('params.center_lng'); ?>], { draggable: true }).addTo(map);
		marker.on('dragend', function(){
			var coords = marker.getLatLng();
			document.getElementById('jform_params_center_lat').value = coords.lat;
			document.getElementById('jform_params_center_lng').value = coords.lng;
		});
	});
	Joomla.submitbutton = function (sometask){
		var someForm = document.forms.adminForm;
		var re_blank = /^(\W*)$/;
		if(sometask != 'mapbox.cancel'){
			if(re_blank.test($('jform_map_alias').value)){
				$('jform_map_alias').value = $('jform_map_name').value.replace(/\W/g, '-').toLowerCase();
			}
			if(!document.formvalidator.isValid(someForm)){
				return false;
			}
			$('jform_params_zoom').value = map.getZoom();
		}
		<?php echo $this->form->getField('map_description')->save(); ?>
		someForm.task.value = sometask;
		someForm.submit();
	}
//]]>
</script>
<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
	<input type="hidden" name="option" value="com_mapbox" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="chosen" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="hidemainmenu" value="0" />
	<input type="hidden" name="map_id" value="<?php echo $this->form->getValue('map_id'); ?>" />
	<?php echo JHTML::_('form.token')."\n"; ?>
	<div id="editcell">
		<div class="width-60 fltlft">
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_MAPBOX_FORM_LEGEND_BASIC'); ?></legend>
				<dl class="fltlft" style="width: 60%;">
				<?php foreach($this->form->getFieldset('base') as $field){ ?>
					<dt><?php echo $field->label; ?></dt>
					<dd><?php echo $field->input; ?></dd>
				<?php } ?>
				</dl>
				<div class="fltlft" style="position: relative; width: 40%; height:200px; overflow:hidden;">
					<div id="twukSoWweucw" style=""></div>
				</div>
				<div class="clr"></div>
				<?php echo $this->form->getLabel('map_description'); ?>
				<div class="clr"></div>
				<?php echo $this->form->getInput('map_description'); ?>
			</fieldset>
		</div>
		<div class="width-40 fltlft">
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_MAPBOX_FORM_LEGEND_OPTIONS'); ?></legend>
				<dl>
				<?php foreach($this->form->getFieldset('options') as $field){ ?>
					<dt><?php echo $field->label; ?></dt>
					<dd><?php echo $field->input; ?></dd>
				<?php } ?>
				</dl>
			</fieldset>
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_MAPBOX_FORM_LEGEND_PARAMS'); ?></legend>
				<dl>
				<?php foreach($this->form->getFieldset('params') as $field){ ?>
					<dt><?php echo $field->label; ?></dt>
					<dd><?php echo $field->input; ?></dd>
				<?php } ?>
				</dl>
			</fieldset>
		</div>
	</div>
</form>
