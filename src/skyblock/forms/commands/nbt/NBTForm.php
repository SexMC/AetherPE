<?php


namespace skyblock\forms\commands\nbt;


use Closure;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

//directly imported from s3 core so it's messy
class NBTForm extends MenuForm {

	/** @var Item; */
	private $item;

	/** @var string */
	private $tag;

	public function __construct(Player $player, $item, string $tag) {
		$this->item = $item;
		$this->tag = $tag;

		$nbt = $this->item->getNamedTag();

		if(!$nbt->getTag($tag)) {
			$player->sendMessage(TextFormat::RED . "This tag value no longer exists");
			return;
		}

		$options = [
			new MenuOption("Edit Value"),
			new MenuOption("Delete"),
			new MenuOption("Back")
		];

		parent::__construct($item->getName() . "'s " . $tag . " NBT Value", "", $options, Closure::fromCallable([$this, "onSubmit"]));
	}

	public function onSubmit(Player $player, $data): void
	{
		$option = $this->getOption($data);
		$nbt = $this->item->getNamedTag();
		switch($option->getText()) {
			case "Edit Value":
				$player->sendForm(new NBTEditValueForm($player, $this->item, $this->tag));
				return;
				break;
			case "Delete":
				$nbt->removeTag($this->tag);
				$player->sendForm(new NBTItemForm($this->item));
				break;
			case "Back":
				$player->sendForm(new NBTItemForm($this->item));
				return;
				break;
		}
		$this->item->setNamedTag($nbt);
		$player->getInventory()->setItemInHand($this->item);
	}



}