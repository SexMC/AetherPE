<?php

declare(strict_types=1);

namespace skyblock\commands\staff;

use CortexPE\Commando\args\RawStringArgument;
use pocketmine\command\CommandSender;
use skyblock\commands\AetherCommand;
use skyblock\Main;
use skyblock\player\AetherPlayer;
use skyblock\utils\Utils;

class TeleportCommand extends AetherCommand {

    protected function prepare(): void {
        $this->setPermission("skyblock.command.teleport");
        $this->registerArgument(0, new RawStringArgument("player", false));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        if(!$sender instanceof AetherPlayer) {
            $sender->sendMessage("Please use this command in-game");
            return;
        }

        if (!Utils::isOnline($args['player'])) {
            $sender->sendMessage(Main::PREFIX . "§cYou cannot teleport to someone who is not online");
            return;
        }

        Utils::teleport($sender, $args['player']);
    }
}