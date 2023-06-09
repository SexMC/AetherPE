<?php

declare(strict_types=1);

namespace skyblock\forms\commands;


use Closure;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\player\Player;
use skyblock\Main;
use skyblock\misc\kits\Kit;
use skyblock\misc\kits\KitHandler;
use skyblock\utils\TimeUtils;
use skyblock\utils\Utils;
use SOFe\AwaitGenerator\Await;

class KitsForm extends MenuForm {

	public function __construct(private Player $player, private array $cooldownData){
		parent::__construct(
			"Kits",
			"Claim your kits",
			array_map(
				fn(Kit $kit) => $this->getButton($kit),
				array_values(KitHandler::getInstance()->getAll())
			),
			Closure::fromCallable([$this, "handle"])
		);
	}

	public function handle(Player $player, int $button): void {
		/** @var Kit $kit */
		$kit = KitHandler::getInstance()->get(explode("\n", $this->getOption($button)->getText())[0]);

		if(!$player->hasPermission($kit->getPermission())){
			$player->sendMessage(Main::PREFIX . "You don't have permissions to claim this kit");
			return;
		}

		if(($left = $this->getLeftCooldown($kit)) > 0){
			$player->sendMessage(Main::PREFIX . "This kit is on cooldown for " . TimeUtils::getFormattedTime($left));
			return;
		}


		Await::f2c(function() use($kit, $player){
			if($player->isOnline()){
				$this->cooldownData[$kit->getName()] = time();

				$success = yield KitHandler::getInstance()->setCooldownData($player->getName(), $this->cooldownData);

				if($player->isOnline()){
					if($success === true){
						$player->sendMessage(Main::PREFIX . "Successfully claimed kit " . $kit->getName());

						foreach($kit->getItems() as $item){
							Utils::addItem($player, $item);
						}
					} else $player->sendMessage(Main::PREFIX . "An error occurred.");
				}
			}
		});
	}

	public function getLeftCooldown(Kit $kit): int {
		if(isset($this->cooldownData[$kit->getName()])){
			return $kit->getCooldown() -  (time() - $this->cooldownData[$kit->getName()]);
		}

		return 0;
	}

	public function getButton(Kit $kit): MenuOption {
		$text = "§cNo permission";

		if($this->player->hasPermission($kit->getPermission())){
			if(($left = $this->getLeftCooldown($kit)) > 0){
				$text = "§cCooldown: " . TimeUtils::getFormattedTime($left);
			} else $text = "§aAvailable";
		}

		return new MenuOption($kit->getName() . "\n" . $text);
	}

}