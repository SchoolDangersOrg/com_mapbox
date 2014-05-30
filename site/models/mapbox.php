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
		    if(!isset($params->template)) $params->template = '';
		    $sql->clear();
            $sql->select("*");
            $sql->from("`#__mapbox_images`");
            $sql->where("marker_id = {$record->marker_id}");
            $sql->order("`ordering` ASC");
            $this->_db->setQuery($sql);
            $images = $this->_db->loadObjectList();
		    if($params->marker_image){
		        $icon_obj = new stdClass();
		        $icon_obj->iconUrl = "/".$params->marker_image;
		        $icon_obj->iconSize = [$params->marker_width, $params->marker_height];
		        $icon_obj->iconAnchor = [$params->marker_origin_x, $params->marker_origin_y];
		        $icon_obj->popupAnchor = [$params->window_origin_x, $params->window_origin_y];
                $geo = array(
                    "type"      =>  "Feature",
                    "geometry"  =>  array(
                        "type"=>"Point",
                        "coordinates"=>array($record->marker_lng,$record->marker_lat)
                    ),
                    "properties"=>  array(
                        "title"=>$record->marker_name,
                        "description"=>$record->marker_description,
                        "icon"=>$icon_obj,
                        "template"=>$params->template,
                        "metric_1"=>$params->metric_1,
                        "metric_2"=>$params->metric_2,
                        "metric_3"=>$params->metric_3,
                        "metric_4"=>$params->metric_4,
                        "metric_5"=>$params->metric_5,
                        "metric_6"=>$params->metric_6,
                        "metric_7"=>$params->metric_7,
                        "metric_8"=>$params->metric_8,
                        "images"=>$images
                    )
                );
		    }else{
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
                        "marker-color"=>$params->marker_color,
                        "template"=>$params->template,
                        "metric_1"=>$params->metric_1,
                        "metric_2"=>$params->metric_2,
                        "metric_3"=>$params->metric_3,
                        "metric_4"=>$params->metric_4,
                        "metric_5"=>$params->metric_5,
                        "metric_6"=>$params->metric_6,
                        "metric_7"=>$params->metric_7,
                        "metric_8"=>$params->metric_8,
                        "images"=>$images
                    )
                );
            }
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
