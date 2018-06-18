<?php namespace App;

use Illuminate\Support\Facades\File;

class CreateFromMenu
{

	public $menu;
	public $finalDirectory;

	/**
	 * CreateFromMenu constructor.
	 *
	 * @param $menu
	 * @param $finalDirectory
	 */
	public function __construct($menu, $finalDirectory) {

		$this->menu = $menu;
		$this->finalDirectory = $finalDirectory;
	}

	public function create() {

		foreach ($this->menu as $menu) {
			$page = [];
			$page['title'] = $menu['title'];
			$page['filename'] = str_slug(ucfirst($menu['title']), '_') . '.html';
			$page['items'] = [];
			foreach ($menu['items'] as $item) {
				$cleaned = new CleanUpPage($item['link']);
				$page['items'][] = $cleaned->getBody();
			}

			$contents = view('guide', ['title' => $page['title'], 'bodies' => $page['items']])->render();

			File::put($this->finalDirectory . $page['filename'], $contents);
		}
	}

}
