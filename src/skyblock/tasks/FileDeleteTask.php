<?php

declare(strict_types=1);

namespace skyblock\tasks;

use Closure;
use pocketmine\scheduler\AsyncTask;
use pocketmine\utils\Filesystem;
use skyblock\Main;
use Throwable;

class FileDeleteTask extends AsyncTask {

	public function __construct(private string $dir, Closure $closure){
		$this->storeLocal("closure", $closure);
	}

	public function onRun() : void{
		try {
			self::delete($this->dir);
			$this->setResult(true);
		} catch (Throwable $e) {
			$this->setResult(false);
		}
	}

	public function onCompletion() : void{
		parent::onCompletion();

		$closure = $this->fetchLocal("closure");
		$result = $this->getResult();

		if($closure !== null){
			if($result === true){
				Main::debug("Successfully deleted directory {$this->dir}");
			} else Main::debug("Failed to delete directory {$this->dir}");

			$closure($result);
		}
	}

	public static function delete(string $dir): void {
		Filesystem::recursiveUnlink($dir);
	}

}