<?php

declare(strict_types=1);

namespace skyblock\commands\economy;

use pocketmine\command\CommandSender;
use skyblock\commands\AetherCommand;
use skyblock\communication\CommunicationLogicHandler;
use skyblock\communication\operations\economy\TopOperation;
use SOFe\AwaitGenerator\Await;

class EssenceTopCommand extends AetherCommand {

	protected function prepare() : void{
		$this->setDescription("View players with the most essence");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		Await::f2c(function() use($sender){
			$this->plugin->getCommunicationLogicHandler()->addOperation(new TopOperation(TopOperation::TYPE_ESSENCE, 0, 9, yield Await::RESOLVE));
			$data = yield Await::ONCE;

			if(isset($data["message"])){
				$sender->sendMessage("§7Top §c10 §7players that have the most §cessence");
				$num = 1;
				foreach($data["message"] as $d){
					$money = $d["Value"];
					$sender->sendMessage("§c#$num §7{$d["Player"]} - §c$" . number_format($money));
					$num++;
				}
			}
		});
	}
}