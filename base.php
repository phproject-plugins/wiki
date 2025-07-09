<?php

/**
 * @package  Wiki
 * @author   Alan Hardman <alan@phpizza.com>
 * @version  1.1.1
 */

namespace Plugin\Wiki;

class Base extends \Plugin
{
    /**
     * Initialize the plugin
     */
    public function _load()
    {
        $f3 = \Base::instance();

        // Add menu item
        $this->_addNav("wiki", $f3->get("dict.wiki.wiki"), "/^\\/wiki/i");
        $this->_addNav("wiki/edit", $f3->get("dict.wiki.page"), "/^\\/wiki\\/edit\\/?$/i", "new");

        // Add routes
        $f3->route("GET /wiki", "Plugin\Wiki\Controller->index");
        $f3->route("GET /wiki/@page", "Plugin\Wiki\Controller->single");
        $f3->route("GET|POST /wiki/edit", "Plugin\Wiki\Controller->edit");
        $f3->route("GET|POST /wiki/edit/@page", "Plugin\Wiki\Controller->edit");
        $f3->route("GET|POST /wiki/delete/@page", "Plugin\Wiki\Controller->delete");
    }

    /**
     * Install plugin (add database tables)
     */
    public function _install()
    {
        $f3 = \Base::instance();
        $db = $f3->get("db.instance");
        $install_db = file_get_contents(__DIR__ . "/db/database.sql");
        $db->exec(explode(";", $install_db));
    }

    /**
     * Check if plugin is installed
     * @return bool
     */
    public function _installed()
    {
        $f3 = \Base::instance();
        if ($f3->get("plugins.wiki.installed")) {
            return true;
        }

        $db = $f3->get("db.instance");
        $db->exec("SHOW TABLES LIKE 'wiki_page'");

        $installed = (bool) $db->count();
        if ($installed) {
            $f3->set("plugins.wiki.installed", true, 3600 * 24);
        }

        return $installed;
    }

    /**
     * Generate page for admin panel
     */
    public function _admin()
    {
        $f3 = \Base::instance();
        $db = $f3->get("db.instance");

        if ($f3->get("AJAX")) {
            // Update configuration value
            match ($f3->get("POST.key")) {
                "parse.markdown", "parse.textile" => \Model\Config::setVal("wiki." . $f3->get("POST.key"), (int)$f3->get("POST.val")),
                default => $f3->error(400),
            };
        } else {
            // Gather some stats
            $result = $db->exec("SELECT COUNT(id) AS `count` FROM wiki_page WHERE deleted_date IS NULL");
            $f3->set("count_page", $result[0]["count"]);
            $result = $db->exec("SELECT COUNT(DISTINCT user_id) AS `count` FROM wiki_page_update");
            $f3->set("count_page_update_user", $result[0]["count"]);
            $result = $db->exec("SELECT COUNT(id) AS `count` FROM wiki_page_update");
            $f3->set("count_page_update", $result[0]["count"]);

            // Render view
            echo \Helper\View::instance()->render("wiki/view/admin.html");
        }
    }
}
