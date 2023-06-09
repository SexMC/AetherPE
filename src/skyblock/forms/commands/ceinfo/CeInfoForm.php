<?php

declare(strict_types=1);

namespace skyblock\forms\commands\ceinfo;

use Closure;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\player\Player;
use skyblock\items\customenchants\CustomEnchantFactory;
use skyblock\items\customenchants\BaseCustomEnchant;
use skyblock\items\customenchants\CustomEnchantHandler;
use skyblock\items\customenchants\ICustomEnchant;
use skyblock\utils\CustomEnchantUtils;

class CeInfoForm extends MenuForm {

	public function __construct()
	{
		parent::__construct("Custom Enchants Info", "Select an option", [
			new MenuOption("View by rarities"),
			new MenuOption("View by tool"),
			new MenuOption("All")
		], Closure::fromCallable([$this, "handle"]));
	}

	public function handle(Player $player, int $button): void {
		if($button === 0){
			$menu = new MenuForm("Custom Enchant Rarities", "Select a rarity", [
				new MenuOption("Mastery"),
				new MenuOption("Heroic"),
				new MenuOption("Legendary"),
				new MenuOption("Rare"),
				new MenuOption("Elite"),
				new MenuOption("Uncommon"),
			], function (Player $player, int $button) : void {
				$options = [];
				$rarity = "";
				switch ($button){
					case 0:
						$options = CustomEnchantHandler::getCustomEnchantsByRarity(ICustomEnchant::RARITY_MASTERY);
						$rarity = "Mastery";
						break;
					case 1:
						$options = CustomEnchantHandler::getCustomEnchantsByRarity(ICustomEnchant::RARITY_HEROIC);
						$rarity = "Heroic";
						break;
					case 2:
						$options = CustomEnchantHandler::getCustomEnchantsByRarity(ICustomEnchant::RARITY_LEGENDARY);
						$rarity = "Legendary";
						break;
					case 3:
						$options = CustomEnchantHandler::getCustomEnchantsByRarity(ICustomEnchant::RARITY_RARE);
						$rarity = "Rare";
						break;
					case 4:
						$options = CustomEnchantHandler::getCustomEnchantsByRarity(ICustomEnchant::RARITY_ELITE);
						$rarity = "Elite";
						break;
					case 5:
						$options = CustomEnchantHandler::getCustomEnchantsByRarity(ICustomEnchant::RARITY_UNCOMMON);
						$rarity = "Uncommon";
						break;
				}

				if(count($options) > 0){
					$op = [];
					$o = [];
					/** @var \skyblock\items\customenchants\BaseCustomEnchant $ce */
					foreach ($options as $ce){
						$op[] = new MenuOption($ce->getIdentifier()->getName());
						$o[] = $ce->getIdentifier()->getName();
					}

					$form = new class($rarity, $o, $op) extends MenuForm {
						public function __construct(string $rarity, array $o, array $op)
						{
							parent::__construct("$rarity Custom Enchants", "Select a CE", $op, function (Player $player, int $button) use($o, $rarity, $op): void {
								if($ce = CustomEnchantFactory::getInstance()->get(($o[$button]))){
									$player->sendForm(new CeInfoViewForm($ce, $this));
								}
							});
						}
					};
					$player->sendForm($form);
				}
			});

			$player->sendForm($menu);
			return;
		} elseif($button === 2){
			$all = CustomEnchantFactory::getInstance()->getList();

			$ces = [];
			$pm = [];
			foreach ($all as $ce){
					$ces[] = new MenuOption($ce->getIdentifier()->getName());
					$pm[] = $ce;

			}
			$form = new MenuForm("All Custom Enchants", "Select a ce", $ces, function (Player $player, int $button) use($all, $pm): void {
				if(isset($pm[$button])){
					if(($ce = $pm[$button]) instanceof BaseCustomEnchant) {
						$player->sendForm(new CeInfoViewForm($ce));
					}
				}
			});
			$player->sendForm($form);
			return;
		}

		$all = CustomEnchantFactory::getInstance()->getList();
		$ces = [];
		foreach ($all as $ce){
			$ces[CustomEnchantUtils::itemTypeIntToString($ce->getApplicableTo())][] = $ce;
		}

		$options = [];
		foreach ($ces as $tool => $ce){
			$options[] = new MenuOption($tool);
		}

		$form = new class($options, $ces) extends MenuForm{
			public function __construct(array $options, array $ces)
			{
				parent::__construct("Custom Enchants", "Select an item type", $options, function (Player $player, int $button) use($ces): void {
					$e = [];
					/** @var BaseCustomEnchant $ce */
					foreach ($ces[$this->getOption($button)->getText()] ?? [] as $ce) {
						$e[] = new MenuOption($ce->getIdentifier()->getName());
					}
					$tool = $this->getOption($button)->getText();
					$form = new class($tool, $e) extends MenuForm {
						public function __construct(string $tool, array $e)
						{
							parent::__construct("$tool Custom Enchants", "Select a CE", $e, function (Player $player, int $button): void {
								if($ce = CustomEnchantFactory::getInstance()->get($this->getOption($button)->getText())){
									$player->sendForm(new CeInfoViewForm($ce, $this));
								}
							});
						}
					};
					$player->sendForm($form);
				});
			}
		};

		$player->sendForm($form);
	}
}