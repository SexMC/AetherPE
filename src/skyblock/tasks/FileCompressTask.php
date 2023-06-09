<?php

namespace skyblock\tasks;

use Closure;
use pocketmine\scheduler\AsyncTask;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use skyblock\Main;
use ZipArchive;

class FileCompressTask extends AsyncTask {


	public function __construct(private string $dir, private string $output, Closure $closure){
		$this->storeLocal("closure", $closure);
	}

	public function onRun() : void{
		$this->setResult(self::zip($this->dir, $this->output));
	}

	public function onCompletion() : void{
		parent::onCompletion();

		$closure = $this->fetchLocal("closure");
		$result = $this->getResult();

		if($closure !== null){
			if($result === true){
				Main::debug("Successfully compressed file {$this->dir}. Output: {$this->output}");
			} else Main::debug("Failed to compress file {$this->dir}");

			$closure($result);
		}
	}

	public static function zip($source, $destination): bool
	{
		$zip = new ZipArchive();
		if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
			return false;
		}

		if(!defined("DS")){
			if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
				DEFINE('DS', DIRECTORY_SEPARATOR); //for windows
			} else {
				DEFINE('DS', '/'); //for linux
			}
		}


		$source = str_replace('\\', DS, realpath($source));

		if (is_dir($source) === true)
		{
			$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
			foreach ($files as $file)
			{
				$file = str_replace('\\',DS, $file);
				// Ignore "." and ".." folders
				if( in_array(substr($file, strrpos($file, DS)+1), array('.', '..')) )
					continue;

				$file = realpath($file);

				if (is_dir($file) === true)
				{
					$zip->addEmptyDir(str_replace($source . DS, '', $file . DS));
				}
				else if (is_file($file) === true)
				{
					$zip->addFromString(str_replace($source . DS, '', $file), file_get_contents($file));
				}
			}
		}
		else if (is_file($source) === true)
		{
			$zip->addFromString(basename($source), file_get_contents($source));
		}

		return $zip->close();
	}
}