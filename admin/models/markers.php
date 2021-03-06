<?php
/**
 * Mapbox Default Admin Model
 * 
 * @package		Mapbox
 * @subpackage	Component
 * @license		GNU/GPL
 */

// CHECK TO ENSURE THIS FILE IS INCLUDED IN JOOMLA!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.modeladmin' );
jimport( 'joomla.filesystem.file' );

class MapboxModelMarkers extends JModelAdmin
{
    /**
     * Database records data
     *
     * @var mixed This may be an object or array depending on context.
     */
    var $_data			= null;

    /**
     * Total number of records retrieved
     *
     * @var integer
     */
     var $_total		= null;

    /**
     * Pagination object
     *
     * @var object
     */
     var $_pagination	= null;

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JController
	 * @since   11.1
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->populateState();
	}
    /**
     * Retrieves the Item data
     *
     * @return	object	A stdClass object containing the data for a single record.
     *
     * @since 1.0
     */
    public function getData()
    {
		$id 	= $this->_getCid();
		$row 	= $this->getTable();

		$row->load($id);
		$this->_data = $row;

        return $this->_data;
    }

	/**
	 * Method for getting the form from the model.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success, false on failure
	 *
	 * @since   1.0
	 */
	public function getForm($data = array(), $loadData = true)
	{
		if($form = $this->loadForm('com_mapbox.markers', 'markers', array('control'=>'jform', 'load_data'=>$loadData))){
			return $form;
		}
		JError::raiseError(0, JText::sprintf('JLIB_FORM_INVALID_FORM_OBJECT', 'markers'));
		return null;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  array    Load default data based on cid.
	 *
	 * @since   1.0
	 */
	protected function loadFormData()
	{
		$db		= $this->getDbo();
		$query 	= $db->getQuery(true);
		$row 	= $this->getTable();
		$id 	= $this->_getCid();
		
		$query->select("*");
		$query->from($row->getTableName());
		$query->where("{$row->getKeyName()} = {$id}");
		
		$db->setQuery($query);
		$this->_data = $db->loadAssoc();
		$ini = new JRegistry();
		$ini->loadString($this->_data['attribs']);
		$this->_data['params'] = $ini->toArray();

		return $this->_data;
	}
    /**
     * Method to retrieve the item data list
     *
     * @return	array	Array of objects containing the data from the database.
     *
     * @since	1.0
     */
    public function getList()
    {
    	$mainframe	= JFactory::getApplication();
		$option		= $mainframe->input->get('option', 'com_mapbox');
    	$scope		= $this->getName();
    	$row		= $this->getTable();
    	$filter		= array();
    	$sql        = $this->getDbo()->getQuery(true);
    	if($search = addslashes($mainframe->getUserState($option.'.'.$scope.'.filter_search'))){
    		$filter[] = "`map_name` LIKE '%{$search}%'";
    	}
    	if(!$ordering = $mainframe->getUserState($option.'.'.$scope.'.filter_order')){
    		$ordering = "map.ordering, marker.ordering";
    	}
    	if(!$order_dir = $mainframe->getUserState($option.'.'.$scope.'.filter_order_Dir')){
    		$order_dir = "ASC";
    	}
    	
    	$sql->select("SQL_CALC_FOUND_ROWS marker.*, map.map_name, map.map_description, map.ordering AS `map_order`, marker.ordering AS `ordering`");
    	$sql->select("v.title AS `access`, u.`name` AS `editor`");
    	$sql->from("`#__mapbox_markers` AS marker");
    	$sql->join("left", "`#__mapbox_maps` AS map USING(map_id)");
    	$sql->join("left", "`#__viewlevels` v ON marker.`access` = v.`id`");
    	$sql->join("left", "`#__users` u ON marker.`checked_out` = u.`id`");
		if(count($filter)){
		    $sql->where($filter);
		}
		$sql->order("{$ordering} {$order_dir}");
		$this->_data = $this->_getList($sql, $this->getState('limitstart'), $this->getState('limit'));

    	return $this->_data;
    }
	/**
	 * A public method to retrieve map data based on a supplied marker id.
	 *
	 * @return  object An containing data parameter for the map.
	 *
	 * @since   1.0
	 */
	public function getMap()
	{
	    $sql = $this->_db->getQuery(true);
	    $app = JFactory::getApplication();
	    $input = $app->input;
	    $id = $input->get('marker_id', 0, 'uint');
	    $sql->select("map_api_key, map.attribs");
	    $sql->from("#__mapbox_maps AS map");
	    $sql->join("left", "#__mapbox_markers AS marker USING(map_id)");
	    $sql->where("marker_id = {$id}");
	    $this->_db->setQuery($sql);
	    $result = $this->_db->loadObject();
	    $json = json_decode($result->attribs);
	    $json->map_api_key = $result->map_api_key;
	    return $json;
	}
	/**
	 * A public method to retrieve map data based on a supplied marker id.
	 *
	 * @return  object An containing data parameter for the map.
	 *
	 * @since   1.0
	 */
	 public function getTemplates()
	 {
	    $sql = $this->_db->getQuery(true);
	    $sql->select("*");
	    $sql->from("`#__mapbox_templates`");
	    $this->_db->setQuery($sql);
	    return $this->_db->loadObjectList();
	 }
	/**
	 * A public method to re-order a set of markers from drag and drop ordering.
	 *
	 * @param   array	$pks An array of private keys.
	 * @param	array	$ordering An array of ordering values to be matched with the keys
	 *
	 * @return  bool
	 *
	 * @since   1.0
	 */
    public function saveMarkerOrder($pks, $ordering)
    {
    	$sql = $this->_db->getQuery(true);
    	
    	foreach($pks as $i => $id){
			$sql->update("#__mapbox_markers");
			$sql->set("ordering = ".$ordering[$i]);
			$sql->where("marker_id = {$id}");
			$this->_db->setQuery($sql);
			$this->_db->execute();
			$sql->clear();
    	}
    	return true;
    }
	/**
     * Upload an image
	 *
	 * @return  bool
	 *
	 * @since   1.0.0.3
	 */
    public function uploadImages($id){
    	if(!(int)$id){
    		throw new UnexpectedValueException(JText::_('COM_MAPBOX_MSG_ERROR_INVALID_DATA'));
    		return false;
    	}
		$input = JFactory::getApplication()->input;
    	$files = $input->files->get('jform');
    	if(is_array($files['icons'])){
    		foreach($files['icons'] as $original){
    			switch($original['error']){
    			case 0:
    			// UPLOAD THE IMAGE
    				$upload = $original['tmp_name'];
    				$target = JPATH_ROOT."/images/mapbox/markers/".$original['name'];
    				if(!JFile::upload($upload, $target)){ 
    					return false;
    				}
    				$sizes = getimagesize($target);
    				$table = $this->getTable();
    				$table->load($id);
    				$options = json_decode($table->attribs);
    				$options->marker_image = "images/mapbox/markers/".$original['name'];
    				$options->marker_width = $sizes[0];
    				$options->marker_height = $sizes[1];
    				$options->marker_origin_x = floor($options->marker_width/2);
    				$options->marker_origin_y = $options->marker_height;
    				$options->window_origin_x = 0;
    				$options->window_origin_y = (-1 * $options->marker_height);
    				$table->attribs = json_encode($options);
    				$table->store();
    				break;
    			case 4:
    			// NO IMAGE WAS SET DO NOTHING
    				break;
    			default:
    			// THERE WAS A FILE UPLOAD ERROR
    				return false;
    				break;
    			}
    		}
    	}
    	return true;
    }
    
	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * @return  void
	 *
	 * @note    Calling getState in this method will result in recursion.
	 * @since   1.0
	 */
	 protected function populateState()
	 {
	 	$app	= JFactory::getApplication();
	 	$option	= $app->input->get('option', 'com_mapbox', 'CMD');
	 	$scope	= $this->getName();

  		$this->setState('limit', $app->getUserStateFromRequest($option.'.'.$scope.'.limit', 'limit', $app->getCfg('list_limit'), 'int'));
  		$this->setState('limitstart', $app->getUserStateFromRequest($option.'.'.$scope.'.limitstart', 'limitstart', 0, 'int'));
  		$this->setState('filter_search', $app->getUserStateFromRequest($option.'.'.$scope.'.filter_search', 'filter_search', '', 'string'));
  		$this->setState('filter_order', $app->getUserStateFromRequest($option.'.'.$scope.'.filter_order', 'filter_order', 'map.ordering, marker.ordering', 'raw'));
  		$this->setState('filter_order_Dir', $app->getUserStateFromRequest($option.'.'.$scope.'.filter_order_Dir', 'filter_order_Dir', 'asc', 'string'));
	 }
	/**
	 * A protected method to get a set of ordering conditions.
	 *
	 * @param   JTable  $table  A JTable object.
	 *
	 * @return  array  An array of conditions to add to ordering queries.
	 *
	 * @since   1.0
	 */
	protected function getReorderConditions($table)
	{
		return array("map_id = ".$table->map_id);
	}
    /**
     * Method to retrieve a JPagination object
     *
     * @return	object	a JPagination object
     *
     * @since	1.0
     */
    public function getPagination()
    {
    	$this->_db->setQuery("SELECT FOUND_ROWS()");
    	$this->_total = $this->_db->loadResult();
    	jimport('joomla.html.pagination');
    	$this->_pagination = new JPagination($this->_total, $this->getState('limitstart'), $this->getState('limit'));
    
    	return $this->_pagination;
    }
	/**
	 * A utility method for retrieving an item Id
	 *
	 * @return	int	the primary key
	 *
	 * @since	1.0
	 */
	protected function _getCid(){
		$row = $this->getTable();
		return JFactory::getApplication()->input->get($row->getKeyName(), 0, 'int');
	}
}
