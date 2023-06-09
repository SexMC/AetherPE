<?php

declare(strict_types=1);

namespace skyblock\commands\staff;

use CortexPE\Commando\args\IntegerArgument;
use SOFe\AwaitGenerator\Await;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use skyblock\commands\AetherCommand;
use skyblock\Database;
use skyblock\Main;
use skyblock\player\AetherPlayer;
use skyblock\utils\Queries;
use skyblock\utils\Utils;

class DeathRestoreCommand extends AetherCommand {
	protected function prepare() : void{
		$this->setPermission("skyblock.command.deathrestore");
		$this->setDescription("Restore death players");
		$this->registerArgument(0, new IntegerArgument("id"));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
		$id = $args["id"];
		if(!$sender instanceof AetherPlayer) return;

		Await::f2c(function() use ($id, $sender){
			$data = yield Database::getInstance()->getLogs()->asyncSelect(Queries::DEATH_RESTORE_LOG, ["id" => $id]);
			if(!isset($data[0])){
				$sender->sendMessage(Main::PREFIX . "invalid id");
				return;
			}

			$lost = json_decode($data[0]["itemsLost"], true);
			foreach ($lost as $itemData){
				if(isset($itemData["meta"])){
					$itemData["damage"] = $itemData["meta"];
				}
				try {
					$item = Item::jsonDeserialize($itemData);
					Utils::addItem($sender, $item, true);
				} catch (\Exception $e){
					var_dump($e);
				}
			}
		});

	}
}