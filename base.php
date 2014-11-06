<?php
/**
 * @package  Wiki
 * @author   Alan Hardman <alan@phpizza.com>
 * @version  1.0.0
 */

namespace Plugin\Wiki;

class Base extends \Plugin {

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
