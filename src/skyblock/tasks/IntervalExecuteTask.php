<?php

declare(strict_types=1);

namespace skyblock\tasks;

use Closure;
use pocketmine\scheduler\CancelTaskException;
use pocketmine\scheduler\Task;
use skyblock\utils\TimeUtils;

class IntervalExecuteTask extends Task {

	private int $current;


	public function __construct(private int $beginSeconds, private array $intervals, private Closure $closure){
		$this->current = $this->beginSeconds;
	}

	public function onRun() : void{
		if(in_array($this->current, $this->intervals)){
			$closure = $this->closure;

			$closure(TimeUtils::getFormattedTime($this->current), $this->current);
		}

		if($this->current <= 0){
			$this->current = $this->beginSeconds;
		}

		$this->current--;
	}
}