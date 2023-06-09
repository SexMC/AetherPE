<?php

declare(strict_types=1);

namespace skyblock\items\itemmods;



use pocketmine\event\Event;
use pocketmine\event\EventPriority;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;
use skyblock\items\customenchants\ICustomEnchant;
use skyblock\items\special\SpecialItem;
use skyblock\traits\PlayerCooldownTrait;
use skyblock\utils\CustomEnchantUtils;

abstract class ItemMod implements Listener, ICustomEnchant {
	use PlayerCooldownTrait;

	const TAG_ITEM_MOD = "tag_item_mod";
	const TAG_ITEM_MOD_ITEM = "tag_item_mod_item";

	public static function getItem(string $skin = ""): Item{
		$item = ItemFactory::getInstance()->get(ItemIds::ENDER_EYE);
		$item->getNamedTag()->setString(self::TAG_ITEM_MOD_ITEM, $skin);
		$skin = ItemModHandler::getInstance()->getItemMod($skin);

		if($skin instanceof ItemMod){
			$lore = $skin->getExtraLore();
			$lore[] = "";

			$item->setLore(array_merge($lore, self::getDefaultLore(CustomEnchantUtils::itemTypeIntToString($skin->getType()))));
			$item->setCustomName("§r§l§fItem Mod ({$skin->getFormat()}§r§l§f)");
		} else {
			$item->setLore(self::getDefaultLore());
			$item->setCustomName("§r§l§fItem Mod ()");
		}

		$item->getNamedTag()->setString(SpecialItem::TAG_SPECIAL_ITEM, "itemmoddddd");


		return $item;
	}

	public function getPriority(): int {
		return EventPriority::MONITOR;
	}

	public static function getDefaultLore(string $type = "{TYPE}"): array {
		return [
			"§r§7§oAttach this item mod to any $type",
			"§r§7§oto gain the special abilities.",
			"§r",
			"§r§7Drag n' Drop onto item to attach.",
			"§r§7Execute /removeitemmod to deatach skin. (must be holding item)",
		];
	}

	public function getActivationMessage(): string {
		return "§r§l§c* {$this->getFormat()} §c*";
	}

	public function unsetCooldown(Player $player): void {
		unset($this->cooldowns[$player->getName()]);
	}
	public function onApply(Player $player, Item $item): void {}

	public function onRemove(Player $player, Item $item): void {}

	/**
	 * @param Event $event
	 *
	 * if returns null then it wont proc else it will return array with [Player, Event] which will be passed to onActive(Player, Event)
	 * @return array|null
	 */
	public abstract function tryCall(Event $event): ?array; //if not returning null, negate here the other itemmods/ces

	abstract public function onActivate(Player $player, Event $event): void;
	abstract public function getDesiredEvents(): array;
	abstract public static function getUniqueID(): string;
	abstract public function getFormat(): string;
	abstract public function getType(): int;
	abstract public function getExtraLore(): array;
}