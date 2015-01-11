<?php
/**
 * @package  Wiki
 * @author   Alan Hardman <alan@phpizza.com>
 * @version  0.0.2
 */

namespace Plugin\Wiki;

class Controller extends \Controller {

	public function index($f3) {
		$this->single($f3, array("page" => "index"));
	}

	public function single($f3, $params) {
		$page = new Model\Page;
		$page->load(array("slug = ?", $params["page"]));
		if(!$page->id) {
			$f3->error(404);
			return;
		}
	}

}
