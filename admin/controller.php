<?php
/**
 * Mapbox Admin View Controller
 *
 * @package		Mapbox
 * @subpackage	Components
 * @license		GNU/GPL
 */

// NO DIRECT ACCESS
defined( '_JEXEC' ) or die( 'Restricted access' );

// PRIVILEGE CHECK
if(!JFactory::getUser()->authorise('core.manage', 'com_mapbox')){
	return JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
}

class MapboxController extends JControllerLegacy
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	public function __construct($config = array())
	{
		parent::__construct();
	}
	
	/**
	 * Medthod to display the correct view and layout
	 *
	 * @return  JController  A JController object to support chaining.
	 */
	public function display($cachable = false, $urlparams = false)
	{
		parent::display($cachable, $urlparams);
		$document = JFactory::getDocument();
		$viewType = $document->getType();
		$viewName = $this->input->get('view', $this->default_view);
		if($viewType == 'html' && $viewName == 'markers'){
		    $view = $this->getView($viewName, 'html');
		    $view->setModel($this->getModel('Images'));
		}
	}
}
