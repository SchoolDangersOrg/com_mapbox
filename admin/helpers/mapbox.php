<?php
/**
 * Mapbox Helper
 * 
 * @package		Mapbox
 * @subpackage	Components
 * @license		GNU/GPL
 */

// NO DIRECT ACCESS
defined( '_JEXEC' ) or die( 'Restricted access' );

abstract Class MapboxHelper {
	public static function addSubmenu($submenu){
		// ADD SUBMENU TABS
		//JSubMenuHelper::addEntry(JText::_('COM_MAPBOX_SUBMENU_SECONDARY'), 'index.php?option=com_mapbox', $submenu == 'mapbox');
	}
}
