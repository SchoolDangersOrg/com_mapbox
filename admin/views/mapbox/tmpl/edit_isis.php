<?php
	defined('_JEXEC') or die('Restricted access');
	JHtml::_('behavior.tooltip');
	JHtml::_('behavior.formvalidation');
	$key = JComponentHelper::getParams('com_mapbox')->get('default_api_key');
    $access_token = JComponentHelper::getParams('com_mapbox')->get('default_access_token');
	$doc = JFactory::getDocument();
    $doc->addScript("https://api.mapbox.com/mapbox.js/v2.2.4/mapbox.js");
    $doc->addStylesheet("https://api.mapbox.com/mapbox.js/v2.2.4/mapbox.css");
    $doc->addStyleDeclaration("#twukSoWweucw { position: absolute; width: 100%; height: 100%;}");
?>

<script type="text/javascript">
//<![CDATA[
	jQuery(document).ready(function(){
		document.formvalidator.setHandler('uint', function(value){
			re_uint = /^\d+$/;
			return re_uint.test(value);
		});
		document.formvalidator.setHandler('string', function(value){
			re_string = /^([\w\d\s-_\.,]+)?$/;
			return re_string.test(value);
		});
		document.formvalidator.setHandler('cmd', function(value){
			re_cmd = /^([\w-_]+)$/;
			return re_cmd.test(value);
		});
		L.mapbox.accessToken = '<?php echo $access_token; ?>';
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
			jQuery('#jform_params_zoom').val(map.getZoom());
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
		<div class="span9 pull-left">
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_MAPBOX_FORM_LEGEND_BASIC'); ?></legend>
				<div class="row-fluid">
					<div class="span5">
					<?php foreach($this->form->getFieldset('base') as $field){ ?>
					    <div class="control-group">
						    <?php echo $field->label; ?>
						    <div class="controls">
						        <?php echo $field->input; ?>
						    </div>
						</div>
					<?php } ?>
					</div>
					<div class="span7">
						<div style="position:relative; width: 100%; height: 200px; overflow: hidden;">
							<div id="twukSoWweucw"></div>
						</div>
					</div>
				</div>
				<div class="clr"></div>
				<?php echo $this->form->getLabel('map_description'); ?>
				<div class="clr"></div>
				<?php echo $this->form->getInput('map_description'); ?>
			</fieldset>
		</div>
		<div class="span3 pull-left">
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_MAPBOX_FORM_LEGEND_OPTIONS'); ?></legend>
				<?php foreach($this->form->getFieldset('options') as $field){ ?>
				<div class="control-group">
                    <?php echo $field->label; ?>
                    <div class="controls">  
					    <?php echo $field->input; ?>
				    </div>
				</div>
				<?php } ?>
			</fieldset>
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_MAPBOX_FORM_LEGEND_PARAMS'); ?></legend>
				<?php foreach($this->form->getFieldset('params') as $field){ ?>
				<div class="control-group">
                    <?php echo $field->label; ?>
                    <div class="controls">  
					    <?php echo $field->input; ?>
				    </div>
				</div>
				<?php } ?>
			</fieldset>
		</div>
	</div>
</form>
