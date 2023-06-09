<?php


namespace skyblock\forms\commands\nbt;

use Closure;
use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Input;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

//directly imported from s3 core so it's messy
class NBTNewValueForm extends CustomForm {

	/** @var Item */
	private $item;

	public function __construct(Item $item) {
		$this->item = $item;

		$options = [
			new Input("tag_name_input", "Tag Name"),
			new Input("tag_value_input", "Tag Value")
		];

		parent::__construct("Create new Tag", $options, Closure::fromCallable([$this, "onSubmit"]));
	}

	public function onSubmit(Player $player, CustomFormResponse $response): void {
		$nbt = $this->item->getNamedTag();
		$tagName = $response->getString("tag_name_input");
		$tagValue = $response->getString("tag_value_input");
		if($tagName === "" || $tagValue === "") {
			$player->sendMessage(TextFormat::RED . "Invalid Values");
			return;
		}

		$intval = intval($tagValue);

		if($intval !== 0 && is_int($intval)) {
			$nbt->setInt($tagName, $intval);
		} else $nbt->setString($tagName, $tagValue);
		$this->item->setNamedTag($nbt);
		$player->getInventory()->setItemInHand($this->item);
	}

}