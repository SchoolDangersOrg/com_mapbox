<?php
	defined('_JEXEC') or die('Restricted access');
	JHtml::_('behavior.tooltip');
	JHtml::_('behavior.formvalidation');
	$key = $this->map->map_api_key ? $this->map->map_api_key : JComponentHelper::getParams('com_mapbox')->get('default_api_key');
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
		
		map = L.mapbox.map('twukSoWweucw', '<?php echo $key; ?>').setView([<?php echo $this->form->getValue('marker_lat', null, 0); ?>, <?php echo $this->form->getValue('marker_lng', null, 0); ?>], <?php echo $this->map->zoom; ?>);
		marker = L.marker([<?php echo $this->form->getValue('marker_lat', null, 0); ?>, <?php echo $this->form->getValue('marker_lng', null, 0); ?>], {
		    icon: L.mapbox.marker.icon({
		        "marker-size": "<?php echo $this->form->getValue('params.marker_size'); ?>",
		        "marker-symbol": "<?php echo $this->form->getValue('params.marker_symbol'); ?>",
		        "marker-color": "<?php echo $this->form->getValue('params.marker_color'); ?>",
		    }),
		    draggable: true
		}).addTo(map);
		marker.on('dragend', function(){
		    var coords = marker.getLatLng();
		    document.getElementById('jform_marker_lat').value = coords.lat;
		    document.getElementById('jform_marker_lng').value = coords.lng;
		});
	});
	Joomla.submitbutton = function (sometask){
		var someForm = document.forms.adminForm;
		var re_blank = /^(\W*)$/;
		if(sometask != 'mapbox.cancel'){
			if(!document.formvalidator.isValid(someForm)){
				return false;
			}
		}
		<?php echo $this->form->getField('marker_description')->save(); ?>
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
	<input type="hidden" name="marker_id" value="<?php echo $this->form->getValue('marker_id'); ?>" />
	<?php echo JHTML::_('form.token')."\n"; ?>
	<div id="editcell">
		<div class="span9 pull-left">
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_MAPBOX_FORM_LEGEND_BASIC'); ?></legend>
				<div class="row-fluid">
				    <div class="span4">
                        <?php foreach($this->form->getFieldset('base') as $field){ ?>
                        <div class="control-group">	
                            <?php echo $field->label; ?>
                            <div class="controls">
                                <?php echo $field->input; ?>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
				    <div class="span4">
                        <?php foreach($this->form->getFieldset('params') as $field){ ?>
                        <div class="control-group">	
                            <?php echo $field->label; ?>
                            <div class="controls">
                                <?php echo $field->input; ?>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
				    <div class="span4">
                        <div style="position: relative; width: 100%; height: 300px; overflow: hidden;">
                            <div id="twukSoWweucw"></div>
                        </div>
                    </div>
                </div>
				<div class="clr"></div>
				<?php echo $this->form->getLabel('marker_description'); ?>
				<div class="clr"></div>
				<?php echo $this->form->getInput('marker_description'); ?>
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
		</div>
	</div>
</form>
