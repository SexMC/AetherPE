<?php

declare(strict_types=1);

namespace skyblock\commands\staff;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use skyblock\commands\AetherCommand;
use skyblock\player\AetherPlayer;
use skyblock\sessions\Session;

class StaffModeCommand extends AetherCommand {

    protected function prepare(): void {
        $this->setPermission("skyblock.command.staffmode");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        if(!$sender instanceof AetherPlayer) {
            $sender->sendMessage("Please use this command in-game");
            return;
        }

        $session = new Session($sender);
        if ($session->isStaffMode()) {
            $session->disableStaffMode();
            $sender->sendMessage(TextFormat::RED . "Staffmode has been disabled");
			$sender->inStaffMode = false;
        } else {
            $session->enableStaffMode();
            $sender->sendMessage(TextFormat::GREEN . "Staffmode has been enabled");
			$sender->inStaffMode = true;
        }
    }
}