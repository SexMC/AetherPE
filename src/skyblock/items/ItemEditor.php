<?php

declare(strict_types=1);

namespace skyblock\items;

use pocketmine\color\Color;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\Armor;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\Item;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use skyblock\items\customenchants\CustomEnchantFactory;
use skyblock\items\customenchants\BaseCustomEnchant;
use skyblock\items\customenchants\CustomEnchantHandler;
use skyblock\items\customenchants\CustomEnchantInstance;
use skyblock\items\customenchants\ICustomEnchant;
use skyblock\items\itemmods\ItemMod;
use skyblock\items\itemmods\ItemModHandler;
use skyblock\items\masks\Mask;
use skyblock\items\masks\MasksHandler;
use skyblock\items\sets\SpecialSet;
use skyblock\items\sets\SpecialSetHandler;
use skyblock\items\tools\SpecialWeapon;
use skyblock\items\tools\SpecialWeaponHandler;
use skyblock\utils\CustomEnchantUtils;

class ItemEditor {

	/** @var array<string, ItemMod[]> */
	private static array $itemMods = [];
	/** @var array<string, \skyblock\items\customenchants\BaseCustomEnchant[]> */
	private static array $enchantments = [];

	public static function setNotTradable(Item $item): void {
		$item->getNamedTag()->setByte("ban_trading", 1);
	}

	public static function isTradable(Item $item): bool {
		return $item->getNamedTag()->getByte("ban_trading", 0) === 0;
	}

	public static function setNotAuctionable(Item $item): void {
		$item->getNamedTag()->setByte("ban_auction", 1);
	}

	public static function isAuctionable(Item $item): bool {
		return $item->getNamedTag()->getByte("ban_auction", 0) === 0;
	}

	public static function glow(Item $item): Item {
		return $item->addEnchantment(new EnchantmentInstance(EnchantmentIdMap::getInstance()->fromId(138)));
	}

	public static function isGlowing(Item $item): bool {
		return $item->hasEnchantment(EnchantmentIdMap::getInstance()->fromId(138));
	}

	public static function removeGlow(Item $item): Item {
		return $item->removeEnchantment(EnchantmentIdMap::getInstance()->fromId(138));
	}

	public static function getMask(Item $item): ?Mask {
		return MasksHandler::getInstance()->getMask($item->getNamedTag()->getString("mask", ""));
	}

	public static function setMask(Item $item, ?Mask $mask): void {
		if($mask !== null){
			$item->getNamedTag()->setString("mask", strtolower($mask::getName()));
		} else $item->getNamedTag()->removeTag("mask");

		self::updateCosmetics($item);
	}


	public static function addUniqueID(Item $item): void {
		$item->getNamedTag()->setString("uniqueid", uniqid());
	}

	public static function getUniqueId(Item $item): ?string {
		return (($val = $item->getNamedTag()->getString("UniqueId", "")) === "" ? null : $val);
	}

	public static function hasUniqueId(Item $item): bool {
		return $item->getNamedTag()->getString("UniqueId", "error38") !== "error38";
	}

	public static function addItemMod(Item $item, string $skin): void {
		if ($list = $item->getNamedTag()->getListTag(ItemMod::TAG_ITEM_MOD)) {
			$list->push(new StringTag($skin));
		} else $item->getNamedTag()->setTag(ItemMod::TAG_ITEM_MOD, new ListTag([new StringTag($skin)]));

		if (self::hasItemModCache($item)) {
			self::updateItemModCache($item, array_merge(self::getItemMods($item), [$skin]));
		} else self::createItemModCache($item, self::getItemMods($item));

		self::updateCosmetics($item);
	}

	public static function removeItemMod(Item $item, string $skin): void {
		$skins = self::getItemMods($item);
		unset($skins[array_search($skin, $skins)]);

		$item->getNamedTag()->setTag(ItemMod::TAG_ITEM_MOD, new ListTag(array_values(array_map(fn(string $skin) => new StringTag($skin), $skins))));

		self::updateItemModCache($item, $skins);
		self::updateCosmetics($item);
	}

	public static function setEnchantmentExpander(Item $item, int $expand): void {
		$item->getNamedTag()->setInt("enchantment_expander", $expand);
		self::updateCosmetics($item);
	}

	public static function getEnchantmentExpander(Item $item): int {
		return $item->getNamedTag()->getInt("enchantment_expander", 0);
	}

	public static function getItemModExpander(Item $item, string $type): int {
		return $item->getNamedTag()->getInt("itemmod_expander_$type", 0);
	}

	public static function setItemModExpander(Item $item, string $type, int $slots): void {
		$item->getNamedTag()->setInt("itemmod_expander_$type", $slots);
		self::updateCosmetics($item);
	}

	public static function hasItemMod(Item $item, string $skin): bool {
		return in_array($skin, self::getItemMods($item));
	}

	public static function createItemModCache(Item $item, array $array): void {
		if (!self::hasUniqueId($item)) {
			self::addUniqueID($item);
		}

		self::$itemMods[self::getUniqueId($item)] = $array;
	}

	/**
	 * @param Item $item
	 *
	 * @return string[]
	 */
	public static function getItemMods(Item $item): array {
		if (self::hasItemModCache($item)) {
			return self::$itemMods[self::getUniqueId($item)];
		}

		$array = [];
		if ($list = $item->getNamedTag()->getListTag(ItemMod::TAG_ITEM_MOD)) {
			foreach ($list as $tag) {
				$array[] = $tag->getValue();
			}
		}

		if(!empty($array)){
			self::updateItemModCache($item, $array);
		}

		return $array;
	}

	public static function hasItemModCache(Item $item): bool {
		return self::hasUniqueId($item) && isset(self::$itemMods[self::getUniqueId($item)]);
	}

	public static function updateItemModCache(Item $item, array $skins): void {
		self::$itemMods[self::getUniqueId($item)] = $skins;
	}

	public static function getCustomEnchantLevel(Item $item, string $ce): int {
		$all = self::getCustomEnchantments($item);

		return isset($all[$ce]) ? $all[$ce]->getLevel() : 0;
	}

	public static function clearCustomEnchants(Item $item): void {
		if(self::hasEnchantmentCache($item)){
			self::updateEnchantmentCache($item, []);
		}

		$item->getNamedTag()->removeTag("custom_enchants");
		self::updateCosmetics($item);
	}

	public static function removeCustomEnchant(Item $item, string $id): void {
		$list = self::getCustomEnchantments($item);

		foreach($list as $k => $v){
			if($v->getCustomEnchant()->getIdentifier()->getId() === $id){
				unset($list[$k]);

				$compound = $item->getNamedTag()->getCompoundTag("custom_enchantments") ?? new CompoundTag();
				$compound->removeTag($id);
				$item->getNamedTag()->setTag("custom_enchantments", $compound);

				self::updateEnchantmentCache($item, $list);
				self::updateCosmetics($item);
				break;
			}
		}
	}

	/**
	 * @param Item $item
	 *
	 * @return \skyblock\items\customenchants\CustomEnchantInstance[]
	 */
	public static function getCustomEnchantments(Item $item, bool $checkCache = true): array {
		if (self::hasEnchantmentCache($item) && $checkCache === true) {
			return self::$enchantments[self::getUniqueId($item)];
		}

		$list = $item->getNamedTag()->getCompoundTag("custom_enchantments");

		if($list === null) {
			return [];
		}

		$enchantments = [];


		/**
		 * @var string $k
		* @var IntTag  $v
		 */
		foreach ($list->getValue() as $k => $v) {
			$id = CustomEnchantFactory::getInstance()->get($k);
			$enchantments[$k] = new CustomEnchantInstance($id, $v->getValue());
		}

		self::createEnchantmentCache($item, $enchantments);
		return $enchantments;
	}

	public static function hasEnchantment(Item $item, string $id): bool {
		return isset(self::getCustomEnchantments($item)[$id]) ?? false;
	}

	/**
	 * @param Item $item
	 * @param CustomEnchantInstance[] $enchantments
	 */
	public static function createEnchantmentCache(Item $item, array $enchantments): void {
		if (!self::hasUniqueId($item)) {
			self::addUniqueID($item);
		}

		self::updateEnchantmentCache($item, $enchantments);
	}

	public static function updateEnchantmentCache(Item $item, array $enchantments): void {
		self::$enchantments[self::getUniqueId($item)] = $enchantments;
	}

	public static function hasEnchantmentCache(Item $item): bool {
		return self::hasUniqueId($item) && isset(self::$enchantments[self::getUniqueId($item)]);
	}

	public static function addCustomEnchantment(Item $item, CustomEnchantInstance $enchantmentInstance) {
		if(!self::hasUniqueId($item)){
			self::addUniqueID($item);
		}

		$compound = $item->getNamedTag()->getCompoundTag("custom_enchantments") ?? new CompoundTag();
		$compound->setInt($enchantmentInstance->getCustomEnchant()->getIdentifier()->getId(), $enchantmentInstance->getLevel());
		$item->getNamedTag()->setTag("custom_enchantments", $compound);

		self::updateEnchantmentCache($item, self::getCustomEnchantments($item, false));
		self::updateCosmetics($item);
	}

	public static function getDescription(Item $item): string {
		return $item->getNamedTag()->getString("description", "");
	}

	public function setDescription(Item $item, string $description): void {
		$item->getNamedTag()->setString("description", $description);
		self::updateCosmetics($item);
	}

	public static function isProtected(Item $item): bool {
		return $item->getNamedTag()->getByte("protected", 0) === 1;
	}

	public static function setProtected(Item $item, bool $value = true): void {
		if($value === true){
			$item->getNamedTag()->setByte("protected", (int) $value);
		} else $item->getNamedTag()->removeTag("protected");
		self::updateCosmetics($item);
	}

	public static function setEnhancedHolyIngot(Item $item, bool $value = true): void {
		$item->getNamedTag()->setByte("enhanced_holy_ingot", (int) $value);
		self::updateCosmetics($item);
	}

	public static function isEnhancedHolyIngoted(Item $item): bool {
		return (bool) $item->getNamedTag()->getByte("enhanced_holy_ingot", 0);
	}

	public static function isEnhanced(Item $item): bool {
		return (bool) $item->getNamedTag()->getByte("enhanced", 0);
	}

	public static function setEnhanced(Item $item, bool $value = true): void {
		$item->getNamedTag()->setByte("enhanced", (int) $value);
		self::updateCosmetics($item);
	}

	public static function setEnhancedHolyIngotCorruption(Item $item, int $corruption): void {
		$item->getNamedTag()->setInt("enhanced_holy_ingot_corruption", $corruption);
		self::updateCosmetics($item);
	}

	public static function getEnhancedHolyIngotCorruption(Item $item): int {
		return $item->getNamedTag()->getInt("enhanced_holy_ingot_corruption", -1);
	}

	public static function isHeroic(Item $item): bool {
		return $item->getNamedTag()->getByte("heroic", 0) === 1;
	}

	public static function setHeroic(Item $item, bool $value = true): void {
		$item->getNamedTag()->setByte("heroic", $value ? 1 : 0);

		if($item instanceof Armor){
			$item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 5));
			$item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));

			if($item->getCustomColor() === null){
				$item->setCustomColor(new Color(255,255,0));
			}
		}

		self::updateCosmetics($item);
	}

	public static function getAmuletSlots(Item $item): int {
		return $item->getNamedTag()->getInt("amulets", 0);
	}

	public static function setAmuletSlots(Item $item, int $amount): void {
		$item->getNamedTag()->setInt("amulets", $amount);
		self::updateCosmetics($item);
	}

	public static function getBeltSlots(Item $item): int {
		return $item->getNamedTag()->getInt("belts", 0);
	}

	public static function setBeltSlots(Item $item, int $amount): void {
		$item->getNamedTag()->setInt("belts", $amount);
		self::updateCosmetics($item);
	}

	public static function getMaxEnchantments(Item $item): int {
		return $item->getNamedTag()->getInt("MaxEnchantments", 4);
	}

	public static function setMaxEnchantments(Item $item, int $amount): void {
		$item->getNamedTag()->setInt("MaxEnchantments", $amount);
	}


	public static function updateCosmetics(Item $item): void {
		$lore = [];

		$arr = [];
		foreach(self::getCustomEnchantments($item) as $ce){
			$tier = $ce->getCustomEnchant()->getRarity()->getTier();

			if(!isset($arr[$tier])) $arr[$tier] = [];
			$arr[$tier][] = $ce;
		}

		$ceString = "";
		foreach ($arr as $ces) {
			/** @var CustomEnchantInstance $enchantmentInstance */
			foreach($ces as $enchantmentInstance){
				$add = "";

				if($enchantmentInstance->getCustomEnchant()->getRarity()->getTier() === ICustomEnchant::RARITY_MASTERY){
					$add = "§l";
				}

				$k = "§r" . $enchantmentInstance->getCustomEnchant()->getRarity()->getColor() . $add . $enchantmentInstance->getCustomEnchant()->getIdentifier()->getName() . " " . CustomEnchantUtils::roman($enchantmentInstance->getLevel()) . "§7, ";
				$ceString .= str_replace(" ", "_-", $k) . " ";
			}
		}

		if($ceString !== ""){
			$lore[] = str_replace(",", ", ", mb_substr(str_replace(["_- ", "_-"], ["", " "], wordwrap($ceString, 60, "\n§7")), 0, -3));
		}

		$extra = PvEItemEditor::getCosmeticsLore($item);
		if(!empty($extra) && $ceString !== ""){
			$lore[] = "§r";
		}

		foreach($extra as $v){
			$lore[] = $v;
		}

		/*if($item instanceof FishingRod){
			$level = CustomFishingRod::getLevel($item);
			$xp = CustomFishingRod::getXP($item);
			$required = number_format(CustomFishingRod::getXpNeededForLevel(min(100, $level + 1)));

			$lore = array_merge($lore, [
				"§r",
				"§r§f§lFishing Rod Level",
				"§r§l§f* §r§f$level",
				"§r",
				"§r§f§lROD EXPERIENCE",
				"§r§l§f* §r§f($xp/$required)",
				"§r",
				"§r§f§lFISHING ROD SPEED",
				"§r§l§f* §r§f" . CustomFishingRod::getMinFishTimeInSeconds($level) . " - " . CustomFishingRod::getMaxFishTimeInSeconds($level) . "s",
				"§r",
				"§r§7Forged with Aether Essence",
				"§r§7this fishing rod can find",
				"§r§7treasure, more than just fishes.",
			]);
		}*/

		/*if(($setName = $item->getNamedTag()->getString(SpecialSet::TAG_SPECIAL_SET, "")) !== ""){
			if(($set = SpecialSetHandler::getInstance()->getSet($setName)) !== null){
				$lore[] = "";
				$lore = array_merge($lore, $set->getLore($item));

				if($set->isOwnable() && ($owner = $item->getNamedTag()->getString("owner", ""))){
					$lore[] = "";
					$lore[] = "§r§l§cPiece Creator: §r§c$owner";
					$lore[] = "§r§c(Piece Creator can only wear this piece)";
				}
			}
		}*/

		if(($weaponName = $item->getNamedTag()->getString(SpecialWeapon::TAG_SPECIAL_TOOL, "")) !== ""){
			if(($weapon = SpecialWeaponHandler::getInstance()->getWeapon($weaponName)) !== null){
				$lore[] = "";
				$lore = array_merge($lore, $weapon->getExtraLore());
			}
		}

		if(($allItemMods = self::getItemMods($item)) !== null){
			/** @var ItemMod[] $types */
			$types = ["amulet" => [], "belt" => [], "backpack" => [], "other" => []];
			foreach($allItemMods as $v){
				if($mod = ItemModHandler::getInstance()->getItemMod($v)){
					switch($mod->getType()){
						case BaseCustomEnchant::ITEM_AMULET:
							$types["amulet"][] = $mod;
							break;
						case BaseCustomEnchant::ITEM_BACKPACK:
							$types["backpack"][] = $mod;
							break;
						case BaseCustomEnchant::ITEM_BELT:
							$types["belt"][] = $mod;
							break;
						default:
							$types["other"][] = $mod;
							break;
					}
				}
			}

			if(($amulet = self::getItemModExpander($item, "amulet")) > 0){
				for($i = 1; $i <= $amulet; $i++){
					if (isset($types["amulet"][$i - 1])) {
						$lore[] = "§r§l§3AMULET (§r{$types["amulet"][$i - 1]->getFormat()}§l§3)";
					} else {
						$lore[] = "§r§l§3AMULET (§r§7N/A§l§3)";
					}
				}
			}

			if(($backpack = self::getItemModExpander($item, "backpack")) > 0){
				for($i = 1; $i <= $backpack; $i++){
					if (isset($types["backpack"][$i - 1])) {
						$lore[] = "§r§l§3BACKPACK (§r{$types["backpack"][$i - 1]->getFormat()}§l§3)";
					} else {
						$lore[] = "§r§l§3BACKPACK (§r§7N/A§l§3)";
					}
				}
			}

			if(($belt = self::getItemModExpander($item, "belt")) > 0){
				for($i = 1; $i <= $belt; $i++){
					if (isset($types["belt"][$i - 1])) {
						$lore[] = "§r§l§3BELT (§r{$types["belt"][$i - 1]->getFormat()}§l§3)";
					} else {
						$lore[] = "§r§l§3BELT (§r§7N/A§l§3)";
					}
				}
			}

			foreach($types["other"] as $itemmod){
				$lore[] = "§r§l§fItem Skin §7({$itemmod->getFormat()}§7)";
			}
		}

		if(($mask = self::getMask($item))){
			$lore[] = "§r§l§6MASK: {$mask->getFormat()}";
		}

		if(($expand = self::getEnchantmentExpander($item)) > 0){
			$lore[] = "§r§l§6ENCHANT EXPANDER (§r§a+{$expand}§l§6)§r";
		}

		if(self::isProtected($item)){
			$lore[] = "§l§fPROTECTED§r§7§o";
		}

		if(($corruption = self::getEnhancedHolyIngotCorruption($item)) >= 0){
			if($corruption > 2){
				$lore[] = "§r§l§cCORRUPTED HOLY INGOT §4* BROKEN *";
			} else {

				if(self::isEnhanced($item)){
					$lore[] = "§r§l§fENHANCED HOLY INGOTED (§r§l§6*ENHANCED§l*§f)";
				} else $lore[] = "§r§l§fENHANCED HOLY INGOTED (§r§l§c*NOT ENHANCED§l*§f)";
				if($corruption === 1){
					$lore[] = "§r§l§cFIRST-STAGE CORRUPTION (§r§c1/2§c§l)";
				} elseif($corruption === 2){
					$lore[] = "§r§l§cSECOND-STAGE CORRUPTION (§r§c2/2§c§l)";
				}
			}
		}



		$item->setLore($lore);
	}

}