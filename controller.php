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

		$f3->set("page", $page);
		$this->_render("wiki/single.html");
	}

	public function edit($f3, $params) {
		if($f3->get("POST")) {
			// TODO: Save posted edit
		}

		$page = new Model\Page;
		$page->load(array("slug = ?", $params["page"]));

		$f3->set("page", $page);
		$f3->set("UI", $f3->get("UI") . ";./app/plugin/wiki/view/");
		$this->_render("wiki/edit.html");
	}

}
