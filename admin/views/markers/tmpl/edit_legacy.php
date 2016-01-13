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
    $doc->addStyleDeclaration("dl.base { width: 50%; float: left; }");
    $doc->addStyleDeclaration("ul#sortable-image-list li { overflow: hidden; }");
    $doc->addStyleDeclaration("ul#sortable-image-list li img { margin: 0; }");
    $doc->addStyleDeclaration("ul#sortable-image-list li img { margin: 0; }");
    $doc->addStyleDeclaration("ul#sortable-image-list div.toolbar { display: block; float: left; overflow: hidden;}");
    $doc->addStyleDeclaration("ul#sortable-image-list div.toolbar a.button { padding: 5px 10px; display: block; overflow: hidden;}");
    $doc->addStyleDeclaration("ul#sortable-image-list div.info { padding: 15px 0 0 25px; font-size: 1.4em; }");
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
		
		var sortable = new Sortables($('sortable-image-list'), { 
		    handle: 'li div.handle',
		    onSort: function(element, clone){
                var i = 1;
                var pks = [];
                var ordering = [];
                re_image_id = /^image_id_(\d+)$/
                // GET THE IDS AND ORDERING FROM THE SORTED LIST
                $$('#sortable-image-list li').each(function(item, index){
                    if(re_image_id.test(item.className)){
                        pks.push(RegExp.$1);
                        ordering.push(i++);
                    }
                });
                // AJAX CALL TO SORT FUNCTION
                new Request.JSON({
                    url: "index.php",
                    data: {
                        "option": "com_mapbox",
                        "task": "images.saveOrderAjax",
                        "cid": pks,
                        "order": ordering,
                        "<?php echo JSession::getFormToken(); ?>": 1
                    },
                    onSuccess: function(someData, someText){},
                    onError: function(someText, someError){}
                }).send();
            }
        });
		
		
		$$('div.toolbar a.button').addEvent('click', function(evt){
		    evt.preventDefault();
		    var target = event.target;
		    var target_item = $(target).getParents('li')[0];
		    re_image_id = /^image_id_(\d+)$/
		    re_image_id.test(target_item.className);
		    var image_id = [RegExp.$1];
            // AJAX CALL TO SORT FUNCTION
            new Request.JSON({
                url: "index.php",
                data: {
                    "option": "com_mapbox",
                    "task": "images.delete",
                    "cid": image_id,
                    "<?php echo JSession::getFormToken(); ?>": 1
                },
                onSuccess: function(someData, someText){ $(target_item).destroy();},
                onError: function(someText, someError){}
            }).send();
		});
		
		$('geo_search_button').addEvent('click', function(){
			geocoder.query($('geo_search_input').value, function(err, data){
				$('jform_marker_lat').value = data.latlng[0];
				$('jform_marker_lng').value = data.latlng[1];
				marker.setLatLng(data.latlng);
				map.fitBounds(data.lbounds);
			});
		});
		L.mapbox.accessToken = '<?php echo $access_token; ?>';		
		geocoder = L.mapbox.geocoderControl('<?php echo $key; ?>');
		map = L.mapbox.map('twukSoWweucw', '<?php echo $key; ?>').setView([<?php echo $this->form->getValue('marker_lat', null, 0); ?>, <?php echo $this->form->getValue('marker_lng', null, 0); ?>], <?php echo @(int)$this->map->zoom; ?>);
		marker = L.marker([<?php echo $this->form->getValue('marker_lat', null, 0); ?>, <?php echo $this->form->getValue('marker_lng', null, 0); ?>], {
		    icon: L.mapbox.marker.icon({
		        "marker-size": "<?php echo $this->form->getValue('params.marker_size'); ?>",
		        "marker-symbol": "<?php echo $this->form->getValue('params.marker_symbol'); ?>",
		        "marker-color": "<?php echo $this->form->getValue('params.marker_color'); ?>"
		    }),
		    draggable: true
		}).addTo(map);
		<?php if($this->form->getValue('params.marker_image')): ?>
		marker.setIcon(L.icon({ iconUrl: "/<?php echo $this->form->getValue('params.marker_image'); ?>", iconSize: [<?php echo $this->form->getValue('params.marker_width'); ?>, <?php echo $this->form->getValue('params.marker_height'); ?>], iconAnchor: [<?php echo $this->form->getValue('params.marker_origin_x'); ?>, <?php echo $this->form->getValue('params.marker_origin_y'); ?>], popupAnchor: [<?php echo $this->form->getValue('params.window_origin_x'); ?>, <?php echo $this->form->getValue('params.window_origin_y'); ?>] }));
		<?php endif; ?>
		marker.on('dragend', function(){
		    var coords = marker.getLatLng();
		    $('jform_marker_lat').value = coords.lat;
		    $('jform_marker_lng').value = coords.lng;
		});
	});
	Joomla.submitbutton = function (sometask){
		var someForm = document.forms.adminForm;
		var re_blank = /^(\W*)$/;
		if(sometask != 'markers.cancel'){
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
	<?php echo JHtml::_('form.token')."\n"; ?>
	<div id="editcell">
		<div class="width-60 fltlft">
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_MAPBOX_FORM_LEGEND_BASIC'); ?></legend>
				<dl class="base">
				<?php foreach($this->form->getFieldset('base') as $field){ ?>
					<dt><?php echo $field->label; ?></dt>
					<dd><?php echo $field->input; ?></dd>
				<?php } ?>
				</dl>
				<dl class="base">
				<?php foreach($this->form->getFieldset('params') as $field){ ?>
					<dt><?php echo $field->label; ?></dt>
					<dd><?php echo $field->input; ?></dd>
				<?php } ?>
				</dl>
				<div class="clr"></div>
				<?php echo $this->form->getLabel('marker_description'); ?>
				<div class="clr"></div>
				<?php echo $this->form->getInput('marker_description'); ?>
			</fieldset>
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_MAPBOX_FORM_LEGEND_IMAGES'); ?></legend>
				<dl>
				<?php foreach($this->form->getFieldset('images') as $field){ ?>
					<dt><?php echo $field->label; ?></dt>
					<dd><?php echo $field->input; ?></dd>
				<?php } ?>
				</dl>
				<div class="clr"></div>
				<ul id="sortable-image-list">
				<?php foreach($this->images as $obj){ ?>
					<li class="image_id_<?php echo $obj->image_id; ?>">
					    <div class="handle">
					        <img src="<?php echo $base.$obj->image_thumb; ?>" title="" alt="" />
					    </div>
					    <div class="toolbar">
					        <a href="#" class="button edit">
					            <img src="components/com_mapbox/images/application_form_edit.png" alt="Edit" title="" />
					        </a>
					        <a href="#" class="button delete">
					            <img src="components/com_mapbox/images/delete.png" alt="Delete" title="" />
					        </a>
					    </div>
					    <div class="info">
					        <strong>Source File:</strong> <?php echo $obj->image_original; ?>
					    </div>
					</li>
				<?php } ?>
				</ul>
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
			<?php if($use_templates): ?>
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_MAPBOX_FORM_LEGEND_TEMPLATES'); ?></legend>
				<dl>
				<?php foreach($this->form->getFieldset('templates') as $field){ ?>
					<dt><?php echo $field->label; ?></dt>
					<dd><?php echo $field->input; ?></dd>
				<?php } ?>
				</dl>
			</fieldset>
			<?php endif; ?>
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_MAPBOX_FORM_LEGEND_INFO'); ?></legend>
				<dl>
				<?php foreach($this->form->getFieldset('info') as $field){ ?>
					<dt><?php echo $field->label; ?></dt>
					<dd><?php echo $field->input; ?></dd>
				<?php } ?>
				</dl>
			</fieldset>
			<fieldset class="adminform">
			    <legend><?php echo JText::_('COM_MAPBOX_FORM_LEGEND_MARKER'); ?></legend>
			    <dl>
			    	<dt><label for="geo_search_input"><?php echo JText::_('COM_MAPBOX_GEO_SEARCH'); ?></label></dt>
			    	<dd><input type="text" name="geo_search_input" id="geo_search_input" placeholder="<?php echo JText::_('COM_MAPBOX_GEO_SEARCH_PLACEHOLDER'); ?>" value="" /><button type="button" id="geo_search_button">Locate</button></dd>
			    </dl>
			    <div style="position: relative; width: 100%; height: 300px; overflow: hidden;">
                    <div id="twukSoWweucw"></div>
			    </div>
				<dl>
				<?php foreach($this->form->getFieldset('icons') as $field){ ?>
					<dt><?php echo $field->label; ?></dt>
					<dd><?php echo $field->input; ?></dd>
				<?php } ?>
				<?php foreach($this->form->getFieldset('custom') as $field){ ?>
					<dt><?php echo $field->label; ?></dt>
					<dd><?php echo $field->input; ?></dd>
				<?php } ?>
				</dl>
			</fieldset>
		</div>
	</div>
</form>
