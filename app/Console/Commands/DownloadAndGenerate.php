<?php

namespace App\Console\Commands;

use App\CreateFromMenu;
use App\MenuParser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class DownloadAndGenerate extends Command
{

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'bulma:download-and-generate';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Downloads Bulma docs and generates docset';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle() {
		if (File::exists(base_path() . 'output/') == FALSE) {
			File::makeDirectory(base_path() . 'output/');
		}
		$bulmaDir = storage_path('bulma-documentation/');

		$this->download($bulmaDir);

		$myDocumentationDir = $bulmaDir . 'bulma.io/my-documentation/docs/';
		if (File::exists($myDocumentationDir) == FALSE) {
			File::makeDirectory($myDocumentationDir, 0755, TRUE);
		}

		$this->createDocs($bulmaDir);

		$this->cleanUp($bulmaDir);

		$this->runDashing($bulmaDir);

		$this->saveDocset($bulmaDir);
	}

	/**
	 * @param $bulmaDir
	 *
	 * @return void
	 */
	protected function download($bulmaDir): void {
		File::deleteDirectory($bulmaDir);
		File::makeDirectory($bulmaDir);

		$command = "wget --mirror -q -o " . storage_path('logs/wget.log') . " --page-requisites --adjust-extension --no-parent --convert-links https://bulma.io/documentation/ -P " . $bulmaDir;

		$this->info("Downloading files");
		$this->info("Executing " . $command);

		exec($command);
	}

	/**
	 * @param $bulmaDir
	 *
	 * @return void
	 */
	protected function createDocs($bulmaDir): void {
		$this->info("Creating docs...");

		$menu = new MenuParser($bulmaDir . 'bulma.io/documentation/index.html');

		$createFromMenu = new CreateFromMenu($menu->getMenu(), $bulmaDir . 'bulma.io/my-documentation/docs/');

		$createFromMenu->create();
	}

	protected function cleanUp($bulmaDir) {
		$this->info("Cleaning up");
		File::deleteDirectory($bulmaDir . 'documentation/');
	}

	/**
	 * @param $bulmaDir
	 *
	 * @return void
	 */
	protected function runDashing($bulmaDir): void {
		$this->info("Running dashing");

		$command = 'dashing build --config ' . storage_path('dashing.json');

		$process = new Process($command, $bulmaDir);
		$process->run();
		$this->info($process->getOutput());
	}

	/**
	 * @return void
	 */
	protected function saveDocset($bulmaDir): void {
		File::deleteDirectory(base_path() . 'output/bulma.docset/');
		File::moveDirectory($bulmaDir . 'bulma.docset', base_path() . '/output/bulma.docset', TRUE);

		File::copy(resource_path() . '/assets/icons/icon.png', base_path() . '/output/bulma.docset/icon.png');
		File::copy(resource_path() . '/assets/icons/icon@2x.png', base_path() . '/output/bulma.docset/icon@2x.png');

		$this->info("Your dash docset is ready at " . base_path() . '/output/bulma.docset');
	}

}
