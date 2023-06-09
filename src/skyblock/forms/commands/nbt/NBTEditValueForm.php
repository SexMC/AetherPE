<?php


namespace skyblock\forms\commands\nbt;

use Closure;
use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Input;
use pocketmine\item\Item;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

//directly imported from s3 core so it's messy
class NBTEditValueForm extends CustomForm
{


	/** @var Item */
	private $item;

	/** @var string */
	private $tag;

	public function __construct(Player $player, $item, string $tag)
	{

		$this->item = $item;
		$this->tag = $tag;

		$nbt = $item->getNamedTag();
		$tagValue = $nbt->getTag($tag);


		if (!$tagValue) {
			$player->sendMessage(TextFormat::RED . "This tag value no longer exists");
			return;
		}
		$options = [
			new Input("value_input", "Value", "", $tagValue->getValue())
		];

		parent::__construct($item->getName() . "'s" . $tag . "Value", $options, Closure::fromCallable([$this, "onSubmit"]));
	}

	public function onSubmit(Player $player, CustomFormResponse $response): void
	{
		$nbt = $this->item->getNamedTag();
		if ($nbt->getTag($this->tag) instanceof IntTag) {
			$input = $response->getString("value_input");
			if (!is_int($input)) {
				$nbt->setInt($this->tag, (int)$input);
			} else  $player->sendMessage(TextFormat::RED . "Expected int go string");

		}
		if ($nbt->getTag($this->tag) instanceof StringTag) {
			$input = $response->getString("value_input");
			$nbt->setString($this->tag, $input);
		}
		$player->getInventory()->setItemInHand($this->item);

		$player->sendForm(new NBTItemForm($this->item));
	}
}