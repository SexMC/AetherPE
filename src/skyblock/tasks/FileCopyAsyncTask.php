<?php

declare(strict_types=1);

namespace skyblock\tasks;

use Closure;
use PHPUnit\Exception;
use pocketmine\scheduler\AsyncTask;
use pocketmine\utils\Filesystem;
use skyblock\Main;

class FileCopyAsyncTask extends AsyncTask {
	public function __construct(private string $from, private string $to, Closure $closure){
		$this->storeLocal("closure", $closure);
	}

	public function onRun() : void{
		try{
			Filesystem::recursiveCopy($this->from, $this->to);
			$this->setResult(true);
		} catch(Exception $e) {
			$this->setResult(false);
		}
	}

	public function onCompletion() : void{
		$result = $this->getResult();
		$closure = $this->fetchLocal("closure");

		if($closure !== null){
			if($result === true){
				Main::debug("Successfully copied file from {$this->from} to {$this->to}");
			} else Main::debug("Failed to copy file from {$this->from} to {$this->to}");


			$closure($result);
		}
	}
}