<?php namespace App;

use Illuminate\Support\Facades\File;
use Symfony\Component\DomCrawler\Crawler;

class CleanUpPage
{

	public $file;
	public $body;
	public $title;

	public function __construct($file) {

		$this->file = $file;
		$this->cleanUp();
	}

	private function cleanUp() {
		$crawler = new Crawler();
		$crawler->addHtmlContent(File::get($this->file));

		$this->title = trim(str_replace(' | Bulma: a modern CSS framework based on Flexbox',
										'',
										$crawler->filter('title')->first()->text()));

		$crawler = $crawler->filter('.bd-lead')->first();

		$crawler = $this->removeNodes($crawler, '.bd-breadcrumb');
		$crawler = $this->removeNodes($crawler, '.bd-header-carbon');

		$crawler = $this->removeNodes($crawler, 'nav.bd-prev-next-bis');
		$crawler = $this->removeNodes($crawler, '#typo');
		$crawler = $this->removeNodes($crawler, '#carbon');
		$crawler = $this->cleanUpLinks($crawler);

		$this->body = trim('<main class="bd-main">' . trim($crawler->html()) . '</main>');

//		$this->body = '<!DOCTYPE html><html lang="en" class="route-documentation">' . $crawler->html() . '</html>';
//
//		$this->body = str_replace(' | Bulma: a modern CSS framework based on Flexbox</title>',
//								  '</title>',
//								  $this->body);

		return $this->body;
	}

	public function save() {
		File::put($this->file, $this->body);
	}

	private function cleanUpLinks(Crawler $crawler) {
		$crawler->filter('a')->each(function(Crawler $crawler) {
			$node = $crawler->getNode(0);
			$href = $node->getAttribute('href');

			if (starts_with($href, '../')) {
				$dirs = collect(explode('/', $href));
				$anchor = explode('#', $dirs->last());
				$href = $dirs->offsetGet(1) . '.html';

				if (count($anchor) == 2) {
					$href .= '#' . $anchor[1];
				}
				$node->setAttribute('href', $href);
			} else {
				if (str_contains($href, '.html#')) {
					$href = '#' . collect(explode('.html#', $href))->last();
					$node->setAttribute('href', $href);
				}
			}
		});

		return $crawler;
	}

	private function removeNodes(Crawler $crawler, $selector): Crawler {
		$crawler->filter($selector)->each(function(Crawler $crawler) {
			$node = $crawler->getNode(0);
			$node->parentNode->removeChild($node);
		});

		return $crawler;
	}

	public function getBody() {
		return $this->body;
	}

	public function getTitle() {
		return $this->title;
	}
}
