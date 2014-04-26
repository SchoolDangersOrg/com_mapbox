<?php
/**
 * Mapbox Default View
 * 
 * @package		Mapbox
 * @subpackage	Components
 * @license		GNU/GPL
 */

// NO DIRECT ACCESS
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );

class MapboxViewMarkers extends JViewLegacy
{
	/**
	 * Mapbox view display method
	 * @return void
	 **/
	function display($tpl = null)
	{
		$input = JFactory::getApplication()->input;
		$layout = $input->get->get('layout', 'list', 'string');
		$this->setLayout($layout);
		switch($layout){
		case "list":
			MapboxHelper::addSubmenu($input->get('view', 'markers'));
			JToolBarHelper::title(JText::_('COM_MAPBOX_VIEW_MARKERS_LIST_TITLE'), 'generic.png');
			JToolBarHelper::addNew('markers.add', 'JTOOLBAR_NEW');
			JToolBarHelper::editList('markers.edit', 'JTOOLBAR_EDIT', true);
			JToolBarHelper::deleteList(JText::_('COM_MAPBOX_MSG_DELETE_CONFIRM'), 'markers.delete', 'JTOOLBAR_DELETE', true);
			JToolBarHelper::preferences('com_mapbox', '500');
			// GET DATA FROM THE MODEL
			$this->filter = $this->get('State');
			$this->items = $this->get('List');
			$this->page = $this->get('Pagination');
			break;
		default:
			$input->set('hidemainmenu', 1);
			JToolBarHelper::title(JText::_('COM_MAPBOX_VIEW_MARKERS_EDIT_TITLE'), 'generic.png');
			JToolBarHelper::apply('markers.apply');
			JToolBarHelper::save('markers.save');
			JToolBarHelper::save2new('markers.save2new');
			JToolBarHelper::cancel('markers.cancel');
			$this->form = $this->get('Form');
			$this->map = $this->get('Map');
			$this->images = $this->get('List','Images');
			$this->templates = $this->get('Templates');
			break;
		}
		parent::display($tpl);
	}
}
