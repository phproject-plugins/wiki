<?php
/**
 * @package  Wiki
 * @author   Alan Hardman <alan@phpizza.com>
 * @version  1.0.0
 */

namespace Plugin\Wiki;

class Base extends \Plugin {

	/**
	 * Initialize the plugin
	 */
	public function _load() {
		// No hooks required
	}

	/**
	 * Install plugin (add database tables)
	 */
	public function _install() {

	}

	/**
	 * Check if plugin is installed
	 * @return bool
	 */
	public function _installed() {
		return false;
	}

}
