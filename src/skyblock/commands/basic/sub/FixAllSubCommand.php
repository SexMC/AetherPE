<?php

declare(strict_types=1);

namespace skyblock\commands\basic\sub;

use pocketmine\command\CommandSender;
use pocketmine\item\Armor;
use pocketmine\item\Durable;
use pocketmine\player\Player;
use skyblock\commands\AetherSubCommand;
use skyblock\Main;
use skyblock\traits\PlayerCooldownTrait;
use skyblock\utils\TimeUtils;

class FixAllSubCommand extends AetherSubCommand {

	use PlayerCooldownTrait;

	protected function prepare() : void{
		$this->setPermission("skyblock.command.fix.all");
		$this->setDescription("Fix every item in your inventory");
	}

	public function canBeUsedInCombat() : bool{
		return true;
	}

	public function onRun(CommandSender $player, string $aliasUsed, array $args) : void{
		if($player instanceof Player){
			if($this->isOnCooldown($player)){
				$player->sendMessage(Main::PREFIX . "This command is on cooldown for " . TimeUtils::getFormattedTime($this->getCooldown($player)));
				return;
			}


			foreach($player->getInventory()->getContents() as $index => $content){
				if($content instanceof Durable){
					$content->setDamage(0);
					$player->getInventory()->setItem($index, $content);
				}
			}
			foreach($player->getArmorInventory()->getContents() as $index => $content){
				if($content instanceof Armor){
					$content->setDamage(0);
					$player->getArmorInventory()->setItem($index, $content);
				}

			}

			$this->setCooldown($player, 60 * 5);
			$player->sendMessage(Main::PREFIX . "Fixed all your items");
		}
	}
}