<?php

declare(strict_types=1);

namespace skyblock\commands\basic\sub;

use CortexPE\Commando\args\BooleanArgument;
use CortexPE\Commando\args\RawStringArgument;
use pocketmine\command\CommandSender;
use skyblock\commands\AetherSubCommand;
use skyblock\Main;
use skyblock\sessions\Session;

class ReclaimSetCommand extends AetherSubCommand {
    protected function prepare(): void {
        $this->setPermission("skyblock.command.reclaim.reset");
        $this->setDescription("Updates someones reclaim status");
        $this->registerArgument(0, new BooleanArgument("reclaimed", false));
        $this->registerArgument(1, new RawStringArgument("player", false));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        $reclaimed = (bool) $args['reclaimed'];
        $username = $args['player'];
        $session = new Session($username);
        if (!$session->playerExists()) {
            $sender->sendMessage(Main::PREFIX . "This player does not exist");
            return;
        }

        $session->setReclaimed($reclaimed);
        $sender->sendMessage(Main::PREFIX . "Updated $username's reclaim");
    }
}