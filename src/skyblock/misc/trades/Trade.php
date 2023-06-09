<?php

declare(strict_types=1);

namespace skyblock\misc\trades;

use pocketmine\item\Item;

class Trade {

	public function __construct(private string $id, private Item $input, private Item $output){ }

	/**
	 * @return string
	 */
	public function getId() : string{
		return $this->id;
	}

	/**
	 * @return Item
	 */
	public function getInput() : Item{
		return clone $this->input;
	}

	/**
	 * @return Item
	 */
	public function getOutput() : Item{
		return clone $this->output;
	}

	public function getViewItem(int $count = 1): Item {
		$i = $this->getOutput()->setCount($count);

		$needed = $this->getInput()->getCount() * $count;
		$lore = $i->getLore();
		$lore[] = "";
		$lore[] = "§r§7Cost";
		$lore[] = "§r§c{$needed}x §7" . $this->getInput()->getName();
		$lore[] = "§r";
		$lore[] = "§r§eClick to trade";

		$i->getNamedTag()->setString("id", $this->id);

		return $i->setLore($lore);
	}
}