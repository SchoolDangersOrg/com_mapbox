<?php
	defined('_JEXEC') or die('Restricted access');
	JHtml::_('behavior.tooltip');
	JHtml::_('behavior.formvalidation');
	$key = JComponentHelper::getParams('com_mapbox')->get('default_api_key');
	$doc = JFactory::getDocument();
    $doc->addScript("https://api.tiles.mapbox.com/mapbox.js/v1.6.2/mapbox.js");
    $doc->addScriptDeclaration("window.addEvent('domready', function() { map = L.mapbox.map('twukSoWweucw', '".$key."').setView([0,0], 2); var marker = L.marker([0, 0], { draggable: true }).addTo(map); });");
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
	<?php echo JHTML::_('form.token')."\n"; ?>
	<div id="editcell">
		<div class="width-60 fltlft">
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_MAPBOX_FORM_LEGEND_BASIC'); ?></legend>
				<dl>
				<?php foreach($this->form->getFieldset('base') as $field){ ?>
					<dt><?php echo $field->label; ?></dt>
					<dd><?php echo $field->input; ?></dd>
				<?php } ?>
				</dl>
				<div class="clr"></div>
				<?php echo $this->form->getLabel('marker_description'); ?>
				<div class="clr"></div>
				<?php echo $this->form->getInput('marker_description'); ?>
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
			<fieldset class="adminform">
			    <legend><?php echo JText::_('COM_MAPBOX_FORM_LEGEND_MARKER'); ?></legend>
			    <div style="position: relative; width: 100%; height: 300px; overflow: hidden;">
			    <div id="twukSoWweucw">
			    </div>
			    </div>
			</fieldset>
		</div>
	</div>
</form>
