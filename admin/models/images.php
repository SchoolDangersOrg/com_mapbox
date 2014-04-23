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

class MapboxModelImages extends JModelAdmin
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
    	$id			= $mainframe->input->get('marker_id', 0);
    	$filter		= array();
    	$sql        = $this->getDbo()->getQuery(true);
    	if(!$ordering = $mainframe->getUserState($option.'.'.$scope.'.filter_order')){
    		$ordering = "ordering";
    	}
    	if(!$order_dir = $mainframe->getUserState($option.'.'.$scope.'.filter_order_Dir')){
    		$order_dir = "ASC";
    	}
    	
    	$sql->select("SQL_CALC_FOUND_ROWS *");
    	$sql->from("`#__mapbox_images`");
    	$sql->where("marker_id = {$id}");
		if(count($filter)){
		    $sql->where($filter);
		}
		$sql->order("{$ordering} {$order_dir}");
		$this->_data = $this->_getList($sql, $this->getState('limitstart'), $this->getState('limit'));

    	return $this->_data;
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
    	if(is_array($files['images'])){
    		foreach($files['images'] as $original){
    			switch($original['error']){
    			case 0:
    			// UPLOAD THE IMAGE
    				$upload = $original['tmp_name'];
    				$target = JPATH_ROOT."/images/mapbox/originals/".$original['name'];
    				if(!JFile::upload($upload, $target)){ 
    					return false;
    				}
    				$options = JComponentHelper::getParams('com_mapbox');
    				$this->createImage($target, "/images/mapbox/fullsize/", $options->get('img_width'), $options->get('img_height'));
    				$this->createImage($target, "/images/mapbox/thumbnails/", $options->get('thumb_width'), $options->get('thumb_height'));
    				$table = $this->getTable();
    				$table->bind(array(
    					'image_id' => null,
    					'marker_id' => $id,
    					'image_original' => "images/mapbox/originals/".$original['name'],
    					'image_src' => "images/mapbox/fullsize/".$original['name'],
    					'image_thumb' => "images/mapbox/thumbnails/".$original['name']
    				));
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
    	echo "this is the end";
    	return true;
    }
    
    /**
     * Create a cropped and resized image from the uploaded original
     *
     * @return bool
	 *
	 * @since   1.0.0.3
     */
    protected function createImage($src, $dest, $width, $height){
    	$original = new JImage($src);
    	$org_width = $original->getWidth();
    	$org_height = $original->getHeight();
    	if(($org_width / $width) < ($org_height / $height)){
    		$original->resize($width, 0, false);
    	}else{
    		$original->resize(0, $height, false);
    	}
    	$thumb = $original->crop($width, $height, null, null, true);
		$filename = pathinfo($original->getPath(), PATHINFO_FILENAME);
		$extension = pathinfo($original->getPath(), PATHINFO_EXTENSION);
		if(!$thumb->toFile(JPATH_ROOT.$dest.$filename.".".$extension)){
			return false;
		}
		$original->destroy();
		$thumb->destroy();
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
		return array("marker_id = ".$table->marker_id);
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
