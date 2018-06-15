<?php namespace App;

use Illuminate\Support\Facades\File;
use Symfony\Component\DomCrawler\Crawler;

class CleanUpPage
{

	public $filename;

	public function __construct($filename) {

		$this->filename = $filename;
	}

	public function cleanUpAndSave() {
		$crawler = new Crawler();
		$crawler->addHtmlContent(File::get($this->filename));

		$crawler = $this->removeNodes($crawler, '.bd-breadcrumb');
		$crawler = $this->removeNodes($crawler, 'aside.bd-side');
		$crawler = $this->removeNodes($crawler, 'nav.bd-prev-next-bis');
		$crawler = $this->removeNodes($crawler, '#typo');
		$crawler = $this->removeNodes($crawler, '#carbon');
		$crawler = $this->removeNodes($crawler, 'body > *:not(.bd-main)');

		File::put($this->filename,
				  '<!DOCTYPE html><html lang="en" class="route-documentation">' . $crawler->html() . '</html>');
	}

	private function removeNodes(Crawler $crawler, $selector): Crawler {
		$crawler->filter($selector)->each(function(Crawler $crawler) {
			$node = $crawler->getNode(0);
			$node->parentNode->removeChild($node);
		});

		return $crawler;
	}
}
