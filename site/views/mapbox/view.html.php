<?php
/**
 * Mapbox Default Site View
 
 * @package		Mapbox
 * @subpackage	Components
 * @license		GNU/GPL
 */

// REQUIRE THE BASE VIEW
jimport( 'joomla.application.component.view');

class MapboxViewMapbox extends JViewLegacy
{
	function display($tpl = null)
	{
		$this->data = $this->get('Data');
		$this->items = $this->get('List');
		parent::display($tpl);
	}
}
