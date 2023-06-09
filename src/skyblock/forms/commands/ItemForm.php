<?php

declare(strict_types=1);

namespace skyblock\forms\commands;

use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Label;
use pocketmine\item\Item;
use pocketmine\player\Player;


class ItemForm extends CustomForm {

	public function __construct(Item $item)
	{
		parent::__construct("Your held item", $this->getElements($item), function (Player $_, CustomFormResponse $__): void {});
	}

	public function getElements(Item $item): array {
		$array = [];

		$array[] = new Label("a", "§f§lType: §r§f" . $item->getVanillaName());

		$add = "";
		foreach ($item->getLore() as $k => $v) {
			$add .= "\n$v";
		}
		$array[] = new Label("c", "§f§lLore:§r§f " . count($item->getLore()) . $add);


		return $array;
	}
}