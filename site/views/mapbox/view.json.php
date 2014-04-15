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
		$this->data = $this->get('Markers');
		$this->setLayout('markers');
		parent::display($tpl);
	}
}
