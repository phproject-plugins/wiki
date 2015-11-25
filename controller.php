<?php
/**
 * @package  Wiki
 * @author   Alan Hardman <alan@phpizza.com>
 * @version  1.1.0
 */

namespace Plugin\Wiki;

class Controller extends \Controller {

	public function index($f3) {
		$this->_requireLogin(\Model\User::RANK_GUEST);

		$page = new Model\Page;
		$page->indent = 0;
		$pages = $this->build_tree($page->find(array("deleted_date IS NULL"), array("order" => "name ASC")));

		$f3->set("title", "Wiki");
		$f3->set("pages", $pages);

		$f3->set("UI", $f3->get("UI") . ";./app/plugin/wiki/view/");
		$this->_render("wiki/index.html");
	}

	public function single($f3, $params) {
		$this->_requireLogin(\Model\User::RANK_GUEST);

		$page = new Model\Page;
		$page->load(array("slug = ?", $params["page"]));

		$f3->set("UI", $f3->get("UI") . ";./app/plugin/wiki/view/");

		if(!$page->id) {
			$f3->status(404);
			$this->_render("wiki/404.html");
			return;
		}

		// Load latest update
		$update = new Model\Page\Update;
		$db = $f3->get("db.instance");
		$maxUpdateId = $db->exec("SELECT MAX(id) id FROM wiki_page_update WHERE wiki_page_id = :id", array(":id" => $page->id));
		$update->load(intval($maxUpdateId[0]['id']));

		// Load update user
		$user = new \Model\User;
		$user->load($update->user_id);
		$f3->set("update_user", $user);

		// Load update count
		$updateCount = $db->exec("SELECT COUNT(*) num FROM wiki_page_update WHERE wiki_page_id = :id", array(":id" => $page->id));
		$f3->set("update_count", intval($updateCount[0]['num']));

		// Load pages list
		$pages = $this->build_tree($page->find(array("deleted_date IS NULL"), array("order" => "name ASC")));

		$f3->set("pages", $pages);

		$f3->set("title", $page->name);
		$f3->set("page", $page);
		$f3->set("update", $update);
		$this->_render("wiki/single.html");
	}

	public function edit($f3, $params) {

		$this->_requireLogin(\Model\User::RANK_USER);

		if(!isset($params["page"])) {
			$params["page"] = null;
			$f3->set("PARAMS.page", "");
		}

		$page = new Model\Page;
		$page->indent = 0;
		if($params["page"]) {
			$page->load(array("slug = ?", $params["page"]));
		}
		if($page->id) {
			$pages_selection = $page->find(array("deleted_date IS NULL AND id != ?", $page->id), array("order" => "name ASC"));
		} else {
			$pages_selection = $page->find(array("deleted_date IS NULL"), array("order" => "name ASC"));
		}
		$f3->set("pages", $this->build_tree($pages_selection, true));

		if($f3->get("POST")) {
			if(!strlen($f3->get("POST.name"))) {
				$f3->set("error", "Page title is required.");
			}
			if(!strlen($f3->get("POST.slug"))) {
				$f3->set("error", "Page slug is required.");
			}
			if(!$f3->get("error")) {
				// Set created date on new pages
				if(!$page->id) {
					$page->created_date = gmdate("Y-m-d H:i:s");
				}

				$page->slug = trim($f3->get("POST.slug"), " -");

				// Save page update
				$update = new Model\Page\Update;
				$update->user_id = $f3->get("user.id");

				$update->old_name = $page->name;
				$page->name = $f3->get("POST.name");
				$update->new_name = $page->name;

				$update->old_content = $page->content;
				$page->content = $f3->get("POST.content");
				$update->new_content = $page->content;

				$page->parent_id = $f3->get("POST.parent_id");

				$update->created_date = gmdate("Y-m-d H:i:s");

				$page->save();

				$update->wiki_page_id = $page->id;
				$update->save();

				$f3->reroute("/wiki/" . $page->slug);
				return;
			}
		}

		$f3->set("title", "Edit Page");
		$f3->set("page", $page);
		$f3->set("UI", $f3->get("UI") . ";./app/plugin/wiki/view/");
		$this->_render("wiki/edit.html");
	}

	public function delete($f3, $params) {
		$this->_requireLogin(\Model\User::RANK_MANAGER);

		$page = new Model\Page;
		$page->load(array("slug = ?", $params["page"]));
		$page->delete();
		$f3->reroute("/wiki");
	}


	function build_tree($pages, $add_spaces = false) {
		$data = array();
		$index = array();
		foreach($pages as $row){
			$id = $row["id"];
			$parent_id = $row["parent_id"] === NULL ? "" : $row["parent_id"];
			$data[$id] = $row;
			$index[$parent_id][] = $id;
		}
		$newarray = array();
		$level = 0;
		foreach ($pages as $row) {
			if($row["parent_id"] == "") {
				if($add_spaces){
					$row["name"] = str_repeat("&emsp;", $level) . $row["name"];
				}
				$row["indent"] = $level;
				$newarray[] = $row;
				$this->display_child_nodes($row['id'], $level + 1, $data, $index, $newarray, $add_spaces);
			}
		}

		return $newarray;
	}


	function display_child_nodes($parent_id, $level, &$data, &$index, &$newarray, $add_spaces = false) {
		if (isset($index[$parent_id])) {
			foreach ($index[$parent_id] as $id) {
				if($add_spaces){
					$data[$id]["name"] = str_repeat("&emsp;", $level) . $data[$id]["name"];
				}
				$data[$id]["indent"] = $level;
				$newarray[] = $data[$id];
				$this->display_child_nodes($id, $level + 1, $data, $index, $newarray, $add_spaces);
			}
		}
	}
}
