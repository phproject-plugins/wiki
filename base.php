<?php
/**
 * @package  Wiki
 * @author   Alan Hardman <alan@phpizza.com>
 * @version  0.0.3
 */

namespace Plugin\Wiki;

class Base extends \Plugin {

	/**
	 * Initialize the plugin
	 */
	public function _load() {
		$f3 = \Base::instance();

		// Add menu item
		$this->_addNav("wiki", $f3->get("dict.wiki.wiki"), '/$\\/wiki/i');

		// Add routes
		$f3->route("GET /wiki", "Plugin\Wiki\Controller->index");
		$f3->route("GET /wiki/@page", "Plugin\Wiki\Controller->single");
		$f3->route("GET|POST /wiki/edit", "Plugin\Wiki\Controller->edit");
		$f3->route("GET|POST /wiki/edit/@page", "Plugin\Wiki\Controller->edit");
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

	/**
	 * Generate page for admin panel
	 */
	public function _admin() {
		$f3 = \Base::instance();
		$db = $f3->get("db.instance");

		// Gather some stats
		$result = $db->exec("SELECT COUNT(id) AS `count` FROM wiki_page WHERE deleted_date IS NULL");
		$f3->set("count_page", $result[0]["count"]);
		$result = $db->exec("SELECT COUNT(DISTINCT user_id) AS `count` FROM wiki_page_update");
		$f3->set("count_page_update_user", $result[0]["count"]);
		$result = $db->exec("SELECT COUNT(id) AS `count` FROM wiki_page_update");
		$f3->set("count_page_update", $result[0]["count"]);

		// Render view
		$f3->set("UI", $f3->get("UI") . ";./app/plugin/wiki/view/");
		echo \Helper\View::instance()->render("admin/plugin-wiki.html");
	}

}
