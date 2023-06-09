<?php

declare(strict_types=1);

namespace skyblock\tasks;

use pocketmine\scheduler\Task;
use skyblock\menus\AetherMenu;

class TickableMenuTask extends Task {

	/** @var AetherMenu  */
	private AetherMenu $menu;
	/** @var int  */
	private int $slot;
	/** @var nuLL|int  */
	private ?int $tickAmount;
	/** @var int  */
	private int $ticks = 0;

	public function __construct(AetherMenu $menu, int $slot, int $tickAmount = null) {
		$this->menu = $menu;
		$this->slot = $slot;
		$this->tickAmount = $tickAmount;
	}

	public function onRun(): void {
		if($this->tickAmount !== null && $this->ticks >= $this->tickAmount){
			$this->getHandler()->cancel();
			return;
		}

		$this->ticks++;
		if($this->menu->onTick($this->slot, $this->ticks >= $this->tickAmount) === false){
			$this->getHandler()?->cancel();
		}
	}

	public function getTickAmount(): ?int {
		return $this->tickAmount;
	}
}