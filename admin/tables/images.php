<?php
// NO DIRECT ACCESS
defined( '_JEXEC' ) or die( 'Restricted access' );

class TableImages extends JTable
{
	/** @var int Primary Key */
	var $image_id			= null;
	/** @var string Image alternate text */
	var $image_alt			= null;
	/** @var string Image title tag */
	var $image_title		= null;
	/** @var string Image original source path */
	var $image_original     = "images/mapbox/originals/default.png";
	/** @var string Modified image source path */
	var $image_src			= "images/mapbox/full/default.png";
	/** @var string Image thumbnail source path */
	var $image_thumb		= "images/retail/thumb/default.png";
	/** @var string Optional caption to accompany image */
	var $image_caption		= null;
	/** @var string JSON string */
	var $attribs            = null;
	/** @var int */
	var $ordering			= null;
	/** @var int */
	var $published			= null;
	/** @var int */
	var $checked_out		= null;
	/** @var time */
	var $checked_out_time	= null;
	/** @var int */
	var $created_by			= null;
	/** @var int */
	var $modified_by		= null;
	/** @var int */
	var $access				= null;
	/** @var int Foregin key */
	var $marker_id          = null;
	
	public function TableMarkers(&$db){
		parent::__construct('#__mapbox_images', 'image_id', $db);
	}
	
	public function bind($array, $ignore=''){
		if(key_exists('params', $array)){
			if(is_array($array['params'])){
				$registry = new JRegistry();
				$registry->loadArray($array['params']);
				$array['attribs'] = $registry->toString();
			}
		}
		return parent::bind($array, $ignore);
	}
	
	public function check(){
		// ASSIGN ORDERING IF NECESSARY
		if(!$this->ordering){
			$this->ordering = $this->getNextOrder("map_id = ".$this->map_id);
		}
		if(!$this->created_by){
		    $user = JFactory::getUser();
		    $this->created_by = $user->get('id');
		}
		return true;
	}
	
	public function store($updateNulls = false){
	    $user = JFactory::getUser();
	    $this->modified_by = $user->get('id');
		if(!parent::store($updateNulls)){
			return false;
		}
		return true;
	}
}
