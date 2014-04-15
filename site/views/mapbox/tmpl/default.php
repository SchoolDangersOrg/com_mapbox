<?php
	// NO DIRECT ACCESS
	defined('_JEXEC') or die('Restricted access');
	$doc = JFactory::getDocument();
	$doc->addScript("https://api.tiles.mapbox.com/mapbox.js/v1.6.2/mapbox.js");
	$doc->addScriptDeclaration("window.onload = function() { map = L.mapbox.map('".$this->data->map_alias."', '".$this->data->map_api_key."'); }");
	$doc->addStylesheet("https://api.tiles.mapbox.com/mapbox.js/v1.6.2/mapbox.css");
	$doc->addStyleDeclaration("#".$this->data->map_alias." { position: absolute; width: 100%; height: 100%;}");
?>

<div id="<?php echo $this->data->map_alias; ?>">
</div>
