<?php
/**
 * @package  Wiki
 * @author   Alan Hardman <alan@phpizza.com>
 * @version  0.0.2
 */

namespace Plugin\Wiki;

class Controller extends \Controller {

	public function index($f3) {
		$f3->set("PARAMS", array("page" => "index"));
		$this->single($f3, $f3->get("PARAMS"));
	}

	public function single($f3, $params) {
		$page = new Model\Page;
		$page->load(array("slug = ?", $params["page"]));

		$f3->set("UI", $f3->get("UI") . ";./app/plugin/wiki/view/");

		if(!$page->id) {
			$f3->status(404);
			$this->_render("wiki/404.html");
			return;
		}

		$f3->set("title", $page->name);
		$f3->set("page", $page);
		$this->_render("wiki/single.html");
	}

	public function edit($f3, $params) {
		if(!isset($params["page"])) {
			$params["page"] = null;
			$f3->set("PARAMS.page", "");
		}

		$page = new Model\Page;
		$page->load(array("slug = ?", $params["page"]));

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

				$update->created_date = gmdate("Y-m-d H:i:s");

				$page->save();

				$update->wiki_page_id = $page->id;
				$update->save();

				$f3->reroute("/wiki/" . $page->slug);
				return;
			}
		}

		$f3->set("page", $page);
		$f3->set("UI", $f3->get("UI") . ";./app/plugin/wiki/view/");
		$this->_render("wiki/edit.html");
	}

}
