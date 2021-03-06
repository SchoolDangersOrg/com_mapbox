<?php
	defined('_JEXEC') or die('Restricted access');
	JHtml::_('behavior.tooltip');
	JHtml::_('behavior.formvalidation');
	$uri = JURI::getInstance();
	$base = $uri->root();
	$use_templates = (count($this->templates) > 0);
	$key = $this->map->map_api_key ? $this->map->map_api_key : JComponentHelper::getParams('com_mapbox')->get('default_api_key');
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
		
		jQuery('#geo_search_button').on('click', function(evt){
			geocoder.query(jQuery('#geo_search_input').val(), function(err, data){
				marker.setLatLng(data.latlng);
				map.fitBounds(data.lbounds);
				jQuery('#jform_marker_lat').val(data.latlng[0]);
				jQuery('#jform_marker_lng').val(data.latlng[1]);
			});
		});
		L.mapbox.accessToken = '<?php echo $access_token; ?>';
		geocoder = L.mapbox.geocoder('<?php echo $key; ?>');
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
				    <div class="span8">
				        <div class="row-fluid">
				            <div class="span6">
                                <?php foreach($this->form->getFieldset('base') as $field){ ?>
                                <div class="control-group">	
                                    <?php echo $field->label; ?>
                                    <div class="controls">
                                        <?php echo $field->input; ?>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                            <div class="span6">
                                <?php foreach($this->form->getFieldset('params') as $field){ ?>
                                <div class="control-group">	
                                    <?php echo $field->label; ?>
                                    <div class="controls">
                                        <?php echo $field->input; ?>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="row-fluid">
                            <div class="span12">
                                <fieldset class="adminform">
                                    <legend><?php echo JText::_('COM_MAPBOX_FORM_LEGEND_IMAGES'); ?></legend>
                                    <?php foreach($this->form->getFieldset('images') as $field){ ?>
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
                    </div>
				    <div class="span4">
                        <div class="control-group">
                            <label for="geo_search_input"><?php echo JText::_('COM_MAPBOX_GEO_SEARCH'); ?></label>
                            <div class="controls"><input type="text" name="geo_search_input" id="geo_search_input" placeholder="<?php echo JText::_('COM_MAPBOX_GEO_SEARCH_PLACEHOLDER'); ?>" value="" /><button type="button" id="geo_search_button" class="btn"><i class="icon icon-search"></i></button></div>
                        </div>
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
			<?php if($use_templates): ?>
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_MAPBOX_FORM_LEGEND_TEMPLATES'); ?></legend>
				<?php foreach($this->form->getFieldset('templates') as $field){ ?>
				<div class="control-group">
					<?php echo $field->label; ?>
					<div class="controls">
					    <?php echo $field->input; ?>
					</div>
				</div>
				<?php } ?>
			</fieldset>
			<?php endif; ?>
			<fieldset class="adminform">
			    <legend><?php echo JText::_('COM_MAPBOX_FORM_LEGEND_MARKER'); ?></legend>
				<?php foreach($this->form->getFieldset('icons') as $field){ ?>
				<div class="control-group">
					<?php echo $field->label; ?>
					<div class="controls">
					    <?php echo $field->input; ?>
					</div>
				</div>
				<?php } ?>
				<?php foreach($this->form->getFieldset('custom') as $field){ ?>
				<div class="control-group">
					<?php echo $field->label; ?>
					<div class="controls">
					    <?php echo $field->input; ?>
					</div>
				</div>
				<?php } ?>
			</fieldset>
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_MAPBOX_FORM_LEGEND_INFO'); ?></legend>
				<?php foreach($this->form->getFieldset('info') as $field){ ?>
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
