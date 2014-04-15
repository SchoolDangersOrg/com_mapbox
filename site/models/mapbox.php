<?php
/**
 * Mapbox Model
 * 
 * @package		Mapbox
 * @subpackage	Component
 * @license		GNU/GPL
 */

// CHECK TO ENSURE THIS FILE IS INCLUDED IN JOOMLA!
defined('_JEXEC') or die();

// REQUIRE THE BASE MODEL
jimport( 'joomla.application.component.model' );

class MapboxModelMapbox extends JModelLegacy
{
 	function __construct(){
		parent::__construct();
		$user = JFactory::getUser();
		$this->setState('levels', implode(',', array_unique($user->getAuthorisedViewLevels())));
  	}
    /**
     * Retrieve data for a single item
     * @return object A stdClass object containing the data for a single record.
     * @since 1.0
     */
    public function getData()
    {
    	$levels	= $this->getState('levels');
		$id 	= JFactory::getApplication()->input->get('id', 0, 'int');
		$sql    = $this->_db->getQuery(true);
		
		$sql->select("*");
		$sql->from("`#__mapbox_maps`");
		$sql->where("`map_id` = {$id}");
		$sql->where("`published` = 1");
		$sql->where("`access` IN ({$levels})");
		if($id){
			$this->_db->setQuery($sql);
			$this->_data = $this->_db->loadObject();
			$params = new JRegistry();
			$params->loadString($this->_data->attribs);
			$this->_data->params = $params;
		}else{
			JError::raiseError(404, JText::_('COM_MAPBOX_MSG_ERROR_404'));
			return false;
		}

        return $this->_data;
    }
    /**
     * Retrieve data for a single item
     * @return object A stdClass object containing the data for a single record.
     * @since 1.0
     */
    public function getMarkers()
    {
    	$levels	= $this->getState('levels');
		$id 	= JFactory::getApplication()->input->get('id', 0, 'int');
		$sql    = $this->_db->getQuery(true);
		
		$sql->select("marker.*");
		$sql->from("`#__mapbox_markers` AS `marker`");
		$sql->join("left", "`#__mapbox_maps` AS `map` USING(map_id)");
		$sql->where("marker.`access` IN ({$levels})");
		$sql->where("marker.`published` = 1");
		$sql->where("marker.`map_id` = {$id}");
		$sql->order("map.ordering, marker.ordering ASC");
		
		$this->_db->setQuery($sql);
		$this->_data = $this->_db->loadObjectList();
		$collection = array("type"=>"FeatureCollection", "features"=>array());
		foreach($this->_data as $record){
		    $params = json_decode($record->attribs);
		    $geo = array(
		        "type"      =>  "Feature",
		        "geometry"  =>  array(
		            "type"=>"Point",
		            "coordinates"=>array($record->marker_lng,$record->marker_lat)
		        ),
		        "properties"=>  array(
		            "title"=>$record->marker_name,
		            "description"=>$record->marker_description,
		            "marker-symbol"=>$params->marker_symbol,
		            "marker-size"=>$params->marker_size,
		            "marker-color"=>$params->marker_color
		        )
		    );
		    $collection['features'][] = $geo;
		}
		return $collection;
    }
    /**
     * Retrieve a list of all data items.
     * @return array An array of stdClass objects containing data for each record.
     * @since 1.0
     */
    public function getList()
    {
    	$levels	= $this->getState('levels');
		$sql	= "SELECT * FROM `#__mapbox` ".
		"WHERE `published` = 1 ".
		"AND `access` IN ({$levels}) LIMIT 1";
		$this->_db->setQuery($sql);
		$this->_data = $this->_db->loadObjectList();
		
		return $this->_data;
    }
}
