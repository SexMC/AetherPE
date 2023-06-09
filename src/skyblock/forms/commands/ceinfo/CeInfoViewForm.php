<?php

declare(strict_types=1);

namespace skyblock\forms\commands\ceinfo;

use dktapps\pmforms\BaseForm;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\player\Player;
use skyblock\items\customenchants\BaseCustomEnchant;
use skyblock\utils\CustomEnchantUtils;

class CeInfoViewForm extends MenuForm {

	public function __construct(BaseCustomEnchant $ce, BaseForm $form = null)
	{
		$text = "§7Name: §c" . $ce->getIdentifier()->getName() . PHP_EOL;
		$text .= "§7Rarity: " . $ce->getRarity()->getColor() . $ce->getRarity()->getDisplayName() . PHP_EOL;
		$text .= "§7Max level: §c" . $ce->getMaxLevel() . PHP_EOL;
		$text .= "§7Applicable items: §c" . CustomEnchantUtils::itemTypeIntToString($ce->getApplicableTo()) . PHP_EOL . PHP_EOL;
		$text .= "§7Description: §c" . $ce->getDescription();

		parent::__construct($ce->getIdentifier()->getName(), $text, [
			new MenuOption("<- Back")
		], function (Player $player, int $buttn) use($form) : void {
			$player->sendForm($form ?? (new CeInfoForm()));
		});
	}
}