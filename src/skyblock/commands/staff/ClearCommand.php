<?php

declare(strict_types=1);

namespace skyblock\commands\staff;

use CortexPE\Commando\args\RawStringArgument;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\Server;
use skyblock\commands\AetherCommand;
use skyblock\Main;

class ClearCommand extends AetherCommand {

    protected function prepare() : void{
        $this->setPermission("skyblock.command.clearinvv");
        $this->registerArgument(0, new RawStringArgument("player", false));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
        $p = Server::getInstance()->getPlayerExact($args["player"]);
        if (!$p instanceof Player) {
            $sender->sendMessage(Main::PREFIX . "That player was not found");
            return;
        }

        $contents = array_merge($p->getInventory()->getContents(), $p->getArmorInventory()->getContents());
        $contentNames = array_map(function (Item $item) { return $item->getName(); }, $contents);

        $p->getInventory()->clearAll();
        $p->getArmorInventory()->clearAll();
        $p->sendMessage(Main::PREFIX . "Your inventory has been cleared! items cleared§7:§f " . implode("§7,§f ", $contentNames));
        $sender->sendMessage(Main::PREFIX . "Cleared {$p->getName()}'s inventory! items cleared§7:§f " . implode("§7,§f ", $contentNames));
    }
}