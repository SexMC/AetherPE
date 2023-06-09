<?php

declare(strict_types=1);

namespace skyblock\commands\staff;

use CortexPE\Commando\args\RawStringArgument;
use pocketmine\command\CommandSender;
use skyblock\commands\AetherCommand;
use skyblock\items\lootbox\types\IntergalacticAgentLootbox;
use skyblock\items\special\SpecialItem;
use skyblock\items\special\types\IntergalacticWeirdCoinItem;
use skyblock\Main;
use skyblock\utils\Utils;

class IntergalacticCoinCommand extends AetherCommand {
	protected function prepare() : void{
		$this->setPermission("skyblock.command.intergalactic");

		$this->registerArgument(0, new RawStringArgument("player"));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($p = $sender->getServer()->getPlayerExact($args["player"])){
			if(!Utils::isHubServer()){
				$p->sendMessage(Main::PREFIX . "This command can only be used at spawn");
				return;
			}

			$hand = $p->getInventory()->getItemInHand();

			if($hand->getNamedTag()->getString(SpecialItem::TAG_SPECIAL_ITEM, "") === IntergalacticWeirdCoinItem::getItemTag()){
				$hand->pop();
				$p->getInventory()->setItemInHand($hand);
				Utils::addItem($p,  $i = IntergalacticAgentLootbox::getItem());
				$p->sendMessage(Main::PREFIX . "§7you got a " . $i->getName());

			} else $p->sendMessage(Main::PREFIX . "§r§7§oYou aren't holding what i want! i need an Intergalactic Weird Coin, which is obtained from mining the ores in this planet. Get to work..!~ please. Use /materialplanet for more information!");
		}
	}
}