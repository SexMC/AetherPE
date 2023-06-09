<?php

declare(strict_types=1);

namespace skyblock\forms\staff;

use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Dropdown;
use dktapps\pmforms\element\Input;
use pocketmine\player\Player;
use skyblock\utils\Utils;

class GetPlayerInfoForm extends CustomForm {

    public function __construct() {

        $players = array_merge(["none"], Utils::getOnlinePlayerUsernames());
        parent::__construct("Input a valid user", [
            new Input("inputted_player", "Input player name", "KingOfTurkey38"),
            new Dropdown("selected_player", "Online Players", $players)
        ],
            function (Player $player, CustomFormResponse $response) use ($players): void {
                if ($response->getInt("selected_player") !== 0) {
                    $username = $players[$response->getInt("selected_player")];
                } else $username = $response->getString("inputted_player");

                if (strlen($username) < 1) {
                    $player->sendMessage("Please user a valid username");
                    return;
                }

                $player->sendForm(new PlayerInfoForm($username));
            }
        );
    }

}