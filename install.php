<?php
/**
 * @package     Mapbox
 * @subpackage  Install
 *
 * @copyright   Copyright (C) 2007 - 2013 Subtext Productions. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

// NO DIRECT ACCESS
defined('JPATH_PLATFORM') or die;
 
class com_mapboxInstallerScript
{
	/*
	 * PREFLIGHT
	 */
	public function preflight( $type, $parent ) {
		$jversion = new JVersion();

		// INSTALLING COMPONENT MANIFEST FILE VERSION
		$this->release = $parent->get( "manifest" )->version;
		
		// MANIFEST FILE MINIMUM JOOMLA VERSION
		$this->minimum_joomla_release = $parent->get( "manifest" )->attributes()->version;   


		// ABORT IF THE CURRENT JOOMLA RELEASE IS OLDER
		if( version_compare( $jversion->getShortVersion(), $this->minimum_joomla_release, 'lt' ) ) {
			JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_MAPBOX_MSG_ERROR_JVERSION', $this->minimum_joomla_release), 'error');
			return false;
		}
 
		// ABORT IF THE COMPONENT BEING INSTALLED IS NOT NEWER THAN THE CURRENTLY INSTALLED VERSION
		if ( $type == 'update' ) {
			$oldRelease = $this->getParam('version');
			$rel = $oldRelease . ' to ' . $this->release;
			if ( version_compare( $this->release, $oldRelease, 'le' ) ) {
				JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_MAPBOX_MSG_ERROR_SCHEMA', $this->release), 'error');
				return false;
			}
		}
		else { $rel = $this->release; }

		// ABORT IF THE SERVER ENVIRONMENT DOES NOT INCLUDE THE GD LIBRARIES
		if (!extension_loaded('gd')) {
			JFactory::getApplication()->enqueueMessage(JText::_('COM_MAPBOX_MSG_ERROR_GD_MISSING'), 'error');
			return false;
		}
		
		// ABORT IF THE IMAGES DIRECTORY IS NOT WRITEABLE
		if(!is_writeable(JPATH_ROOT."/images")){
			JFactory::getApplication()->enqueueMessage(JText::_('COM_MAPBOX_INSTALL_ERROR_PERMISSIONS'), 'error');
			return false;
		}

		echo '<p>' . JText::_('COM_MAPBOX_MSG_SUCCESS_PREFLIGHT') . '</p>';
	}
 
	/*
	 * INSTALL
	 */
	public function install( $parent ) {
	    if(!file_exists(JPATH_ROOT."/images/mapbox")) mkdir(JPATH_ROOT."/images/mapbox");
        rename(dirname(__FILE__)."/admin/images/mapbox/fullsize", JPATH_ROOT."/images/mapbox/fullsize");
        rename(dirname(__FILE__)."/admin/images/mapbox/thumbnails", JPATH_ROOT."/images/mapbox/thumbnails");
        rename(dirname(__FILE__)."/admin/images/mapbox/originals", JPATH_ROOT."/images/mapbox/originals");
		echo '<p>' . JText::sprintf('COM_MAPBOX_MSG_SUCCESS_INSTALL', $this->release) . '</p>';
	}
 
	/*
	 * UPDATE
	 */
	public function update( $parent ) {
	    if(version_compare($this->getManifestParam('version'), "1.0.0.1", "le")){
	        if(version_compare($this->release, "1.0.0.2", "ge")){
	            if(!file_exists(JPATH_ROOT."/images/mapbox")) mkdir(JPATH_ROOT."/images/mapbox");
                rename(dirname(__FILE__)."/admin/images/mapbox/fullsize", JPATH_ROOT."/images/mapbox/fullsize");
                rename(dirname(__FILE__)."/admin/images/mapbox/thumbnails", JPATH_ROOT."/images/mapbox/thumbnails");
                rename(dirname(__FILE__)."/admin/images/mapbox/originals", JPATH_ROOT."/images/mapbox/originals");
	        }
	    }
		echo '<p>' . JText::sprintf('COM_MAPBOX_MSG_SUCCESS_UPDATE', $this->release) . '</p>';
	}
 
	/*
	 * POSTFLIGHT
	 */
	public function postflight( $type, $parent ) {
		echo '<p>' . JText::_('COM_MAPBOX_MSG_SUCCESS_POSTFLIGHT') . '</p>';
	}

	/*
	 * UNINSTALL
	 */
	public function uninstall( $parent ) {
	    if(file_exists(JPATH_ROOT."/images/mapbox")){
	        $this->deleteDir(JPATH_ROOT."/images/mapbox");
	    }
		if(!isset($this->release)) $this->release = '1.0.0.7';
		echo '<p>' . JText::sprintf('COM_MAPBOX_MSG_SUCCESS_UNINSTALL', $this->release) . '</p>';
	}
 
	/*
	 * GET A VARIABLE FROM THE MANIFEST FILE (ACTUALLY, FROM THE MANIFEST CACHE).
	 */
	protected function getManifestParam( $name ) {
		$db = JFactory::getDbo();
		$db->setQuery('SELECT manifest_cache FROM #__extensions WHERE name = "com_mapbox"');
		$manifest = json_decode( $db->loadResult(), true );
		return $manifest[ $name ];
	}
	
	/*
	 * DELETE RESOURCES
	 */
	protected function deleteDir($some_dir){
		if(is_dir($some_dir)){
			$handle = opendir($some_dir);
			while(false !== ($file = readdir($handle))){
				if($file != "." && $file != ".."){
					if(is_dir($some_dir . "/" . $file)){
						$this->deleteDir($some_dir . "/" . $file);
					}else{
						unlink($some_dir . "/" . $file);
					}
				}
			}
			closedir($handle);
			rmdir($some_dir);
		}
	}
	
	/*
	 * GET A VARIABLE FROM THE MANIFEST FILE (ACTUALLY, FROM THE MANIFEST CACHE).
	 */
	protected function getParam( $name ) {
		$db = JFactory::getDbo();
		$db->setQuery('SELECT manifest_cache FROM #__extensions WHERE name = "com_mapbox"');
		$manifest = json_decode( $db->loadResult(), true );
		return $manifest[ $name ];
	}
 
	/*
	 * SETS PARAMETER VALUES IN THE COMPONENT'S ROW OF THE EXTENSION TABLE
	 */
	protected function setParams($param_array) {
		if ( count($param_array) > 0 ) {
			// read the existing component value(s)
			$db = JFactory::getDbo();
			$db->setQuery('SELECT params FROM #__extensions WHERE name = "com_mapbox"');
			$params = json_decode( $db->loadResult(), true );
			// add the new variable(s) to the existing one(s)
			foreach ( $param_array as $name => $value ) {
				$params[ (string) $name ] = (string) $value;
			}
			// store the combined new and existing values back as a JSON string
			$paramsString = json_encode( $params );
			$db->setQuery('UPDATE #__extensions SET params = ' .
				$db->quote( $paramsString ) .
				' WHERE name = "com_mapbox"' );
				$db->query();
		}
	}
}
