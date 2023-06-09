<?php


namespace skyblock\forms\commands\nbt;


use Closure;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\item\Item;
use pocketmine\nbt\tag\Tag;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

//directly imported from s3 core so it's messy
class NBTItemForm extends MenuForm {

	/** @var MenuOption[] */
	private $options;

	/** @var Tag[] */
	private $nbtData;

	/** @var Item */
	private $item;

	public function __construct(Item $item) {
		$this->item = $item;
		$nbt = $item->getNamedTag();
		$this->nbtData = $nbt->getValue();

		foreach($this->nbtData as $name => $tag) {
			$this->options[] = new MenuOption($name);
		}

		$this->options[] = new MenuOption("Create new tag");

		parent::__construct($item->getName() . " NBT Data", "", $this->options, Closure::fromCallable([$this, "onSubmit"]));
	}

	public function onSubmit(Player $player, $data): void
	{
		$item = $this->item;
		$option = $this->getOption($data);
		if(!$option) {
			$player->sendMessage(TextFormat::RED . "This value no longer exists");
			return;
		}
		switch($option->getText()) {
			case "Create new tag":
				$player->sendForm(new NBTNewValueForm($this->item));
				break;
			default:
				$player->sendForm(new NBTForm($player, $this->item, $option->getText()));
		}
	}

}