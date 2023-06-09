<?php

declare(strict_types=1);

namespace skyblock\forms\commands;

use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Slider;
use pocketmine\item\Item;
use pocketmine\player\Player;
use skyblock\items\customenchants\CustomEnchantFactory;
use skyblock\items\customenchants\CustomEnchantHandler;
use skyblock\items\customenchants\CustomEnchantInstance;
use skyblock\items\ItemEditor;
use skyblock\utils\CustomEnchantUtils;

class CeEditForm extends CustomForm {

	private array $ces = [];

	public function __construct(private Item $item){
		parent::__construct("Ce Edit", $this->getButtons(), function(Player $player, CustomFormResponse $response): void {
			ItemEditor::clearCustomEnchants($this->item);
			foreach($this->ces as $k => $v){
				$level = (int)  floor($response->getFloat($k));

				if($level > 0){
					ItemEditor::addCustomEnchantment($this->item, new CustomEnchantInstance($v, $level));
				}
			}

			$player->getInventory()->setItemInHand($this->item);
		});
	}

	public function getButtons(): array {
		$arr = [];
		foreach(CustomEnchantFactory::getInstance()->getList() as $name => $ce){
			if(CustomEnchantUtils::itemMatchesItemType($this->item, $ce->getApplicableTo())){
				$this->ces[$name] = $ce;
				$arr[] = new Slider($name, $ce->getRarity()->getColor() . $name, 0, $ce->getMaxLevel());
			}
		}


		return $arr;
	}
}