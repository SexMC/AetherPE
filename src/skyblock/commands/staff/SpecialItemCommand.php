<?php

namespace skyblock\commands\staff;

use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\args\RawStringArgument;
use League\Flysystem\Exception;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use ReflectionClass;
use skyblock\commands\AetherCommand;
use skyblock\commands\arguments\SpecialItemArgument;
use skyblock\items\special\SpecialItemHandler;
use skyblock\Main;
use Throwable;

class SpecialItemCommand extends AetherCommand {
	protected function prepare() : void{
		$this->registerArgument(0, new RawStringArgument("player|list"));
		$this->registerArgument(1, new IntegerArgument("count", true));
		$this->registerArgument(2, new SpecialItemArgument("item", true));
		$this->registerArgument(3, new RawStringArgument("optional1", true));
		$this->registerArgument(4, new RawStringArgument("optional2", true));
		$this->registerArgument(5, new RawStringArgument("optional3", true));
		$this->registerArgument(6, new RawStringArgument("optional4", true));

		$this->setPermission("skyblock.command.specialitem");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		$p = Server::getInstance()->getPlayerExact($args["player|list"]);
		$count = $args["count"] ?? null;
		$item = $args["item"] ?? null;
		$opt1 = $args["optional1"] ?? null;
		$opt2 = $args["optional2"] ?? null;
		$opt3 = $args["optional3"] ?? null;
		$opt4 = $args["optional4"] ?? null;
		$array = [];
		foreach([$opt1, $opt2, $opt3, $opt4] as $v){
			if($v !== null){
				$array[] = $v;
			}
		}

		if($args["player|list"] === "list"){
			$sender->sendMessage("§b§lSpecial Item list: ");
			foreach (SpecialItemHandler::getItems() as $item) {
				$param = "";
				$class = new ReflectionClass($item);
				if ($method = $class->getMethod("getItem")) {
					foreach ($method->getParameters() as $parameter) {
						$param .= "<{$parameter->getName()}:{$parameter->getType()->getName()}>";
					}
				}

				if (empty($param)) $param = "None";

				$sender->sendMessage("§bName: " . $item::getItemTag() . ". parameters: $param");
			}

			return;
		}

		if($count === null || $item === null){
			return;
		}


		if($p === null){
			$sender->sendMessage(Main::PREFIX . "Invalid player");
			return;
		}

		try{
			$p->getInventory()->addItem($item::getItem(...$array)->setCount($count));
			$sender->sendMessage("§bGave {$count}x {$item::getItemTag()} to {$p->getName()}");

		} catch(Throwable $throwable) {
			Server::getInstance()->getLogger()->logException($throwable);
			$sender->sendMessage(Main::PREFIX . $throwable->getMessage());
		}
	}
}