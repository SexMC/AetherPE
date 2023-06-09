<?php

declare(strict_types=1);

namespace skyblock\forms\island\admin;

use Closure;
use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Input;
use pocketmine\player\Player;
use skyblock\islands\Island;
use skyblock\islands\IslandInterface;

class IslandValuesForm extends CustomForm {

	public function __construct(private Island $island)
	{

		parent::__construct("Island Admin: " . $island->getName(), $this->getButtons(), Closure::fromCallable([$this, "handle"]));
	}

	public function handle(Player $player, CustomFormResponse $form): void {
		$spawner = $form->getString("spawnerAmount");
		$hopper = $form->getString("hopperAmount");
		$value = $form->getString("value");
		$power = $form->getString("power");


		$this->island->setLimit(IslandInterface::LIMIT_HOPPER, (int) $hopper);
		$this->island->setLimit(IslandInterface::LIMIT_SPAWNER, (int) $spawner);
		$this->island->setValue((int) $value);
		$this->island->setPower((int) $power);

		$player->sendMessage("§7Updated island values sir!");
		$player->sendMessage("§7New hopper amount: $hopper");
		$player->sendMessage("§7New spawner amount: $spawner");
	}

	public function getButtons(): array {

		return [
			new Input("spawnerAmount", "Spawner Amount", "A number..", "{$this->island->getLimit(IslandInterface::LIMIT_SPAWNER)}"),
			new Input("hopperAmount", "Hopper Amount", "A number..", "{$this->island->getLimit(IslandInterface::LIMIT_HOPPER)}"),
			new Input("power", "power", "A number..", "{$this->island->getPower()}"),
			new Input("value", "Value", "A number..", "{$this->island->getValue()}"),
		];
	}
}