<?php
/**
 * @package  Wiki
 * @author   Alan Hardman <alan@phpizza.com>
 * @version  0.0.2
 */

namespace Plugin\Wiki;

class Base extends \Plugin {

	/**
	 * Initialize the plugin
	 */
	public function _load() {
		$f3 = \Base::instance();

		// Add menu item
		$this->_addNav("wiki", $f3->get("dict.wiki.wiki"), "/\\/wiki/i");

		// Add routes
		$f3->route("GET /wiki", "Plugin\Wiki\Controller->index");
		$f3->route("GET /wiki/@page", "Plugin\Wiki\Controller->single");
	}

	/**
	 * Install plugin (add database tables)
	 */
	public function _install() {
		$f3 = \Base::instance();
		$db = $f3->get("db.instance");
		$install_db = file_get_contents(__DIR__ . "/db/database.sql");
		$db->exec(explode(";", $install_db));
	}

	/**
	 * Check if plugin is installed
	 * @return bool
	 */
	public function _installed() {
		$f3 = \Base::instance();
		$db = $f3->get("db.instance");
		$q = $db->exec("SHOW TABLES LIKE 'wiki_page'");
		return !!$db->count();
	}

}
