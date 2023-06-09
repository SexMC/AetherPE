<?php

declare(strict_types=1);

namespace skyblock\commands\staff;

use pocketmine\command\CommandSender;
use pocketmine\item\ItemFactory;
use pocketmine\player\Player;
use skyblock\commands\AetherCommand;
use skyblock\Main;
use skyblock\utils\Utils;

class ReloadItemCommand extends AetherCommand {
	protected function prepare() : void{
		$this->setDescription("skyblock.command.reloaditem");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof Player){
			$data = yaml_parse(stream_get_contents(Main::getInstance()->getResource("item.yml")));

			$name = $data["name"];
			$lore = $data["lore"];
			$id = $data["id"];
			$meta = $data["meta"];

			$item = ItemFactory::getInstance()->get((int) $id, (int) $meta);
			$item->setCustomName($name);
			$item->setLore($lore);

			Utils::addItem($sender, $item);
		}
	}
}