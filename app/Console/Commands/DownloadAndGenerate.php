<?php

namespace App\Console\Commands;

use App\CleanUpPage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;
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

		$bulmaDir = storage_path('bulma-documentation/');

//		$this->download($bulmaDir);

		$this->cleanUpFiles($bulmaDir);

		$command = 'dashing build --source ' . $bulmaDir . ' --config ' . storage_path('dashing.json');

		$process = new Process($command);
		$process->run();
		$this->info($process->getOutput());
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
	protected function cleanUpFiles($bulmaDir): void {
		$this->info("Cleaning up");

		$files = File::files($bulmaDir . 'bulma.io/documentation/');

		collect($files)->each(function(SplFileInfo $file) {
			if ($file->getFilename() != 'index.html') {
				File::delete($file->getPathname());
			}
		});

		$dirs = File::directories($bulmaDir . 'bulma.io/documentation/');

		foreach ($dirs as $dir) {
			collect(File::files($dir))->each(function(SplFileInfo $file) {
				$cleaner = new CleanUpPage($file->getPathname());
				$cleaner->cleanUpAndSave();
			});
		}
	}
}
