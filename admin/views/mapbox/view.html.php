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

class MapboxViewMapbox extends JViewLegacy
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
			JToolBarHelper::title(JText::_('COM_MAPBOX_VIEW_MAPBOX_LIST_TITLE'), 'generic.png');
			JToolBarHelper::addNew('mapbox.add', 'JTOOLBAR_NEW');
			JToolBarHelper::editList('mapbox.edit', 'JTOOLBAR_EDIT', true);
			JToolBarHelper::deleteList(JText::_('COM_MAPBOX_MSG_DELETE_CONFIRM'), 'mapbox.delete', 'JTOOLBAR_DELETE', true);
			JToolBarHelper::preferences('com_mapbox', '500');
			// GET DATA FROM THE MODEL
			$this->filter = $this->get('State');
			$this->items = $this->get('List');
			$this->page = $this->get('Pagination');
			break;
		default:
			$input->set('hidemainmenu', 1);
			JToolBarHelper::title(JText::_('COM_MAPBOX_VIEW_MAPBOX_EDIT_TITLE'), 'generic.png');
			JToolBarHelper::apply('mapbox.apply');
			JToolBarHelper::save('mapbox.save');
			JToolBarHelper::save2new('mapbox.save2new');
			JToolBarHelper::cancel('mapbox.cancel');
			$this->form = $this->get('Form');
			break;
		}
		parent::display($tpl);
	}
}
