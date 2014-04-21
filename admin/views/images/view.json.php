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

class MapboxViewImages extends JViewLegacy
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
			// GET DATA FROM THE MODEL
			$this->items = $this->get('List');
			break;
		default:
			$this->form = $this->get('Form');
			break;
		}
		parent::display($tpl);
	}
}
