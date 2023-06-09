<?php

declare(strict_types=1);

namespace skyblock\forms\staff;

use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\PlayerListPacket;
use pocketmine\network\mcpe\protocol\StartGamePacket;
use pocketmine\network\mcpe\protocol\types\PlayerListEntry;
use pocketmine\player\Player;
use pocketmine\Server;
use skyblock\Main;
use skyblock\sessions\Session;
use skyblock\utils\Utils;

class PlayerInfoForm extends MenuForm {

    public function __construct(string $username) {

        $session = new Session($username);

        parent::__construct("$username | " . (Utils::isOnline($username) ? "§aOnline" : "§cOffline"), "", [
            new MenuOption("Teleport to them"),
            new MenuOption($session->isFrozen() ? "Unfreeze" : "Freeze"),
        ],
            function (Player $player, int $selectedOption) use ($username): void {
                switch ($selectedOption) {
                    case 0:
                        if (!Utils::isOnline($username)) {
                            $player->sendMessage(Main::PREFIX . "§cYou cannot teleport to someone who is not online");
                            return;
                        }

                        Utils::teleport($player, $username);
                        break;
                    case 1:
                        $session = new Session($username);
                        if ($session->isFrozen()) {
                            $session->setFrozen(false);
                            $player->sendMessage(Main::PREFIX . "Successfully unfrozen $username");
                        } else {
                            $session->setFrozen(true);
                            $player->sendMessage(Main::PREFIX . "Successfully frozen $username");
                        }
                        break;
                }
        });
    }

}