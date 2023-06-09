<?php

declare(strict_types=1);

namespace skyblock\tasks;

use Closure;
use pocketmine\scheduler\AsyncTask;
use skyblock\Main;
use ZipArchive;

class FileDecompressTask extends AsyncTask {

	public function __construct(private string $dir, private string $output, Closure $closure){
		$this->storeLocal("closure", $closure);
	}

	public function onRun() : void{
		$this->setResult(self::unzip($this->dir, $this->output));
	}

	public function onCompletion() : void{
		parent::onCompletion();

		$closure = $this->fetchLocal("closure");
		$result = $this->getResult();

		if($closure !== null){
			if($result === true){
				Main::debug("Successfully decompressed file {$this->dir}. Output: {$this->output}");
			} else Main::debug("Failed to decompress file {$this->dir}");

			$closure($result);
		}
	}

	public static function unzip(string $dir, string $output): bool {
		$zip = new ZipArchive();

		if($zip->open($dir)){
			$zip->extractTo($output);
			return $zip->close();
		}

		return false;
	}
}