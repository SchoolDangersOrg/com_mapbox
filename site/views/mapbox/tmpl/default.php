<?php
	// NO DIRECT ACCESS
	defined('_JEXEC') or die('Restricted access');
	JHtml::_('behavior.framework', true);
	$doc = JFactory::getDocument();
	$doc->addScript("https://api.tiles.mapbox.com/mapbox.js/v1.6.2/mapbox.js");
	$doc->addStylesheet("https://api.tiles.mapbox.com/mapbox.js/v1.6.2/mapbox.css");
	$doc->addStyleDeclaration("#".$this->data->map_alias." { position: absolute; width: 100%; height: 100%;}");
	$params = json_decode($this->data->attribs);
?>
<script type="text/javascript">
//<![CDATA[
window.addEvent('domready', function(){
    map = L.mapbox.map('<?php echo $this->data->map_alias; ?>', '<?php echo $this->data->map_api_key; ?>');
    var someRequest = new Request.JSON({
        method: "post",
        url: "/index.php",
        onSuccess: function(responseJSON, responseText){
            iconLayer = L.mapbox.featureLayer();
            iconLayer.setGeoJSON(responseJSON);
            iconLayer.addTo(map);
            <?php if($params->fit_to_markers){ ?>
            map.fitBounds(iconLayer.getBounds());
            <?php } ?>
        },
        onFailure: function(xhr){}
    });
    someRequest.send({"data": {"option": "com_mapbox", "view": "mapbox", "layout": "markers", "format": "json", "id": <?php echo $this->data->map_id; ?> }});
});
//]]>
</script>
<div style="position: relative; width: 100%; min-height: 300px; overflow: hidden;">
	<div id="<?php echo $this->data->map_alias; ?>"></div>
</div>
