<?php namespace App;

use Illuminate\Support\Facades\File;
use Symfony\Component\DomCrawler\Crawler;

class MenuParser
{

	/**
	 * @var string
	 */
	public $file;

	public function __construct($file) {
		$this->file = $file;
	}

	/**
	 * @return \Illuminate\Support\Collection
	 */
	public function getMenu() {
		$crawler = new Crawler();
		$crawler->addHtmlContent(File::get($this->file));

		$baseFolder = storage_path('bulma-documentation/bulma.io/documentation/');

		$menus = collect([]);
		$crawler->filter('.bd-category')->each(function(Crawler $menuGroup) use ($menus, $baseFolder) {
			$title = $menuGroup->filter('.bd-category-name strong')->text();
			$items = [];

			$menuItems = $menuGroup->filter('.bd-category-list li a');
			foreach ($menuItems as $menuItem) {
				$menuItem = new Crawler($menuItem);
				$items[] = [
					'name' => trim($menuItem->text()),
					'link' => $baseFolder . $menuItem->attr('href')
				];
			};

			$menus->push([
							 'title' => $title,
							 'items' => $items
						 ]);
		});

		return $menus;
	}

}
