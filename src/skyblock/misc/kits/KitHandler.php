<?php

declare(strict_types=1);

namespace skyblock\misc\kits;

use Generator;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\VanillaItems;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat;
use skyblock\Database;
use skyblock\items\customenchants\ICustomEnchant;
use skyblock\items\lootbox\LootboxItem;
use skyblock\items\lootbox\types\rank\AetherPlusLootbox;
use skyblock\items\lootbox\types\rank\AstronomicalLootbox;
use skyblock\items\special\types\CustomEnchantmentBook;
use skyblock\items\special\types\HeroicUpgradeItem;
use skyblock\items\special\types\SlotBotTicket;
use skyblock\utils\Queries;

class KitHandler {
	use SingletonTrait;

	/** @var Kit[]  */
	private array $kits = [];

	public function __construct(){
		//$this->setup();
	}

	public function setup(): void {
		$this->registerKit("§r§l§7Traveler Kit",
			[
				VanillaItems::STONE_SWORD()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS())),
				VanillaItems::STONE_PICKAXE()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY())),
				VanillaItems::STONE_AXE()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY())),
				VanillaItems::STONE_SHOVEL()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY())),
				VanillaItems::STONE_HOE()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY())),

				VanillaItems::LEATHER_BOOTS()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION())),
				VanillaItems::LEATHER_CAP()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION())),
				VanillaItems::LEATHER_TUNIC()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION())),
				VanillaItems::LEATHER_PANTS()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION())),

				VanillaItems::LAVA_BUCKET(),
				VanillaItems::WATER_BUCKET(),
				VanillaItems::WHEAT_SEEDS()->setCount(16),
				VanillaItems::BEETROOT_SEEDS()->setCount(8),
				VanillaItems::POTATO()->setCount(4),
				VanillaItems::STEAK()->setCount(16),
			], [
				new LootboxItem(CustomEnchantmentBook::getItem(ICustomEnchant::RARITY_UNCOMMON), 15, 1, 1),
				new LootboxItem(CustomEnchantmentBook::getItem(ICustomEnchant::RARITY_ELITE), 10, 1, 1),
				new LootboxItem(CustomEnchantmentBook::getItem(ICustomEnchant::RARITY_RARE), 5, 1, 1),
				new LootboxItem(CustomEnchantmentBook::getItem(ICustomEnchant::RARITY_LEGENDARY), 3, 1, 1),
			],
			"aether.kit.traveler",
			86400,
		);


		$this->registerKit("§r§l§eSinon Kit",
			[
				VanillaItems::IRON_SWORD()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS()))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING())),
				VanillaItems::IRON_PICKAXE()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY()))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING())),
				VanillaItems::IRON_AXE()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY()))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING())),
				VanillaItems::IRON_SHOVEL()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY()))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING())),
				VanillaItems::IRON_HOE()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY()))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING())),

				VanillaItems::IRON_BOOTS()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION()))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING())),
				VanillaItems::IRON_HELMET()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION()))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING())),
				VanillaItems::IRON_CHESTPLATE()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION()))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING())),
				VanillaItems::IRON_LEGGINGS()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION()))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING())),

				VanillaItems::BOW()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING())),
				VanillaItems::ARROW()->setCount(64),
				VanillaItems::STEAK()->setCount(16),
				VanillaItems::GOLDEN_APPLE()->setCount(4),
				], [
				new LootboxItem(CustomEnchantmentBook::getItem(ICustomEnchant::RARITY_UNCOMMON), 15, 1, 2),
				new LootboxItem(CustomEnchantmentBook::getItem(ICustomEnchant::RARITY_ELITE), 10, 1, 2),
				new LootboxItem(CustomEnchantmentBook::getItem(ICustomEnchant::RARITY_RARE), 5, 1, 2),
				new LootboxItem(CustomEnchantmentBook::getItem(ICustomEnchant::RARITY_LEGENDARY), 3, 1, 2),
			],
			"aether.kit.sinon",
			86400,
		);


		$this->registerKit("§r§l§3Troje Kit",
			[
				VanillaItems::DIAMOND_SWORD()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS()))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 2)),
				VanillaItems::DIAMOND_PICKAXE()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY()))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 2)),
				VanillaItems::DIAMOND_AXE()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY()))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 2)),
				VanillaItems::DIAMOND_SHOVEL()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY()))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 2)),
				VanillaItems::DIAMOND_HOE()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY()))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 2)),

				VanillaItems::DIAMOND_BOOTS()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION()))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 2)),
				VanillaItems::DIAMOND_HELMET()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION()))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 2)),
				VanillaItems::DIAMOND_CHESTPLATE()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION()))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 2)),
				VanillaItems::DIAMOND_LEGGINGS()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION()))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 2)),

				VanillaItems::BOW()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::POWER()))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 2)),
				VanillaItems::ARROW()->setCount(64),
				VanillaItems::STEAK()->setCount(16),
				VanillaItems::GOLDEN_APPLE()->setCount(8),
				], [
				new LootboxItem(CustomEnchantmentBook::getItem(ICustomEnchant::RARITY_UNCOMMON), 15, 1, 3),
				new LootboxItem(CustomEnchantmentBook::getItem(ICustomEnchant::RARITY_ELITE), 10, 1, 3),
				new LootboxItem(CustomEnchantmentBook::getItem(ICustomEnchant::RARITY_RARE), 5, 1, 3),
				new LootboxItem(CustomEnchantmentBook::getItem(ICustomEnchant::RARITY_LEGENDARY), 3, 1, 3),
			],
			"aether.kit.troje",
			86400,
		);


		$this->registerKit("§r§l§dHydra Kit",
			[
				VanillaItems::DIAMOND_SWORD()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), 2))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3)),
				VanillaItems::DIAMOND_PICKAXE()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY(), 2))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3)),
				VanillaItems::DIAMOND_AXE()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY(), 2))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3)),
				VanillaItems::DIAMOND_SHOVEL()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY(), 2))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3)),
				VanillaItems::DIAMOND_HOE()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY(), 2))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3)),

				VanillaItems::DIAMOND_BOOTS()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 2))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3)),
				VanillaItems::DIAMOND_HELMET()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 2))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3)),
				VanillaItems::DIAMOND_CHESTPLATE()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 2))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3)),
				VanillaItems::DIAMOND_LEGGINGS()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 2))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3)),

				VanillaItems::BOW()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::POWER(), 2))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3)),
				VanillaItems::ARROW()->setCount(64),
				VanillaItems::STEAK()->setCount(16),
				VanillaItems::GOLDEN_APPLE()->setCount(12),
				], [
				new LootboxItem(CustomEnchantmentBook::getItem(ICustomEnchant::RARITY_UNCOMMON), 15, 1, 4),
				new LootboxItem(CustomEnchantmentBook::getItem(ICustomEnchant::RARITY_ELITE), 10, 1, 4),
				new LootboxItem(CustomEnchantmentBook::getItem(ICustomEnchant::RARITY_RARE), 5, 1, 4),
				new LootboxItem(CustomEnchantmentBook::getItem(ICustomEnchant::RARITY_LEGENDARY), 3, 1, 4),
			],
			"aether.kit.hydra",
			86400,
		);

		$this->registerKit("§r§l§6Theseus Kit",
			[
				VanillaItems::DIAMOND_SWORD()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), 3))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3)),
				VanillaItems::DIAMOND_PICKAXE()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY(), 3))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3)),
				VanillaItems::DIAMOND_AXE()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY(), 3))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3)),
				VanillaItems::DIAMOND_SHOVEL()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY(), 3))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3)),
				VanillaItems::DIAMOND_HOE()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY(), 3))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3)),

				VanillaItems::DIAMOND_BOOTS()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 3))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3)),
				VanillaItems::DIAMOND_HELMET()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 3))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3)),
				VanillaItems::DIAMOND_CHESTPLATE()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 3))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3)),
				VanillaItems::DIAMOND_LEGGINGS()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 3))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3)),

				VanillaItems::BOW()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::POWER(), 3))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3)),
				VanillaItems::ARROW()->setCount(64),
				VanillaItems::STEAK()->setCount(16),
				VanillaItems::GOLDEN_APPLE()->setCount(16),
				], [
				new LootboxItem(CustomEnchantmentBook::getItem(ICustomEnchant::RARITY_UNCOMMON), 15, 1, 5),
				new LootboxItem(CustomEnchantmentBook::getItem(ICustomEnchant::RARITY_ELITE), 10, 1, 5),
				new LootboxItem(CustomEnchantmentBook::getItem(ICustomEnchant::RARITY_RARE), 5, 1, 5),
				new LootboxItem(CustomEnchantmentBook::getItem(ICustomEnchant::RARITY_LEGENDARY), 3, 1, 5),
			],
			"aether.kit.theseus",
			86400,
		);

		$this->registerKit("§r§l§dAurora Kit",
			[
				VanillaItems::DIAMOND_SWORD()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), 4))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3)),
				VanillaItems::DIAMOND_PICKAXE()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY(), 4))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3)),
				VanillaItems::DIAMOND_AXE()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY(), 4))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3)),
				VanillaItems::DIAMOND_SHOVEL()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY(), 4))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3)),
				VanillaItems::DIAMOND_HOE()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY(), 4))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3)),

				VanillaItems::DIAMOND_BOOTS()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 4))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3)),
				VanillaItems::DIAMOND_HELMET()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 4))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3)),
				VanillaItems::DIAMOND_CHESTPLATE()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 4))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3)),
				VanillaItems::DIAMOND_LEGGINGS()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 4))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3)),

				VanillaItems::BOW()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::POWER(), 4))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3)),
				VanillaItems::ARROW()->setCount(64),
				VanillaItems::STEAK()->setCount(16),
				VanillaItems::GOLDEN_APPLE()->setCount(20),

				], [
				new LootboxItem(CustomEnchantmentBook::getItem(ICustomEnchant::RARITY_UNCOMMON), 15, 1, 6),
				new LootboxItem(CustomEnchantmentBook::getItem(ICustomEnchant::RARITY_ELITE), 10, 1, 6),
				new LootboxItem(CustomEnchantmentBook::getItem(ICustomEnchant::RARITY_RARE), 5, 1, 6),
				new LootboxItem(CustomEnchantmentBook::getItem(ICustomEnchant::RARITY_LEGENDARY), 3, 1, 6),
			],
			"aether.kit.aurora",
			86400,
		);

		$this->registerKit("§r§l§bAether Kit",
			[
				VanillaItems::DIAMOND_SWORD()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), 5))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3)),
				VanillaItems::DIAMOND_PICKAXE()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY(), 5))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3)),
				VanillaItems::DIAMOND_AXE()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY(), 5))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3)),
				VanillaItems::DIAMOND_SHOVEL()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY(), 5))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3)),
				VanillaItems::DIAMOND_HOE()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY(), 5))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3)),

				VanillaItems::DIAMOND_BOOTS()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 5))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3)),
				VanillaItems::DIAMOND_HELMET()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 5))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3)),
				VanillaItems::DIAMOND_CHESTPLATE()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 5))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3)),
				VanillaItems::DIAMOND_LEGGINGS()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 5))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3)),

				VanillaItems::BOW()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::POWER(), 5))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3)),
				VanillaItems::ARROW()->setCount(64),
				VanillaItems::STEAK()->setCount(16),
				VanillaItems::GOLDEN_APPLE()->setCount(24),
				], [
				new LootboxItem(CustomEnchantmentBook::getItem(ICustomEnchant::RARITY_UNCOMMON), 15, 1, 7),
				new LootboxItem(CustomEnchantmentBook::getItem(ICustomEnchant::RARITY_ELITE), 10, 1, 7),
				new LootboxItem(CustomEnchantmentBook::getItem(ICustomEnchant::RARITY_RARE), 5, 1, 7),
				new LootboxItem(CustomEnchantmentBook::getItem(ICustomEnchant::RARITY_LEGENDARY), 3, 1, 7),
			],
			"aether.kit.aether",
			86400,
		);
	}
	

	
	public function registerKit(string $name, array $items, array $randItems,string $permission, int $cooldown): void {
		$this->kits[strtolower(TextFormat::clean($name))] = new Kit($name, $items, $randItems, $permission, $cooldown);
	}

	/**
	 * @param string $player
	 *
	 * @return array|Generator|mixed returns an array of array<kitName, claim date in unix>
	 */
	public function getCooldownData(string $player) {
		$data = yield Database::getInstance()->getLibasynql()->asyncSelect(Queries::KITS_GET, ["username" => strtolower($player)]);

		if(isset($data[0])){
			return json_decode($data[0]["cooldowns"], true);
		}

		return [];
	}

	/**
	 * @param string $player
	 * @param array  $data
	 *
	 * @return bool|Generator bool if updated false if not updated successfully
	 */
	public function setCooldownData(string $player, array $data) {
		$data = yield Database::getInstance()->getLibasynql()->asyncInsert(Queries::KITS_UPDATE, ["username" => strtolower($player), "cooldowns" => json_encode($data)]);

		if($data[1] > 0){
			return true;
		}

		return false;
	}

	private function parseItem(array $data): Item {
		$item = ItemFactory::getInstance()->get($data["id"], $data["meta"], $data["count"]);

		if(($data["name"] ?? "") !== ""){

			if(strtolower($data["name"]) !== "default"){
				$item->setCustomName($data["name"]);
			}
		}

		foreach($data["enchants"] ?? [] as $id => $levelData){
			if(($e = EnchantmentIdMap::getInstance()->fromId((int) $id)) !== null){
				$item->addEnchantment(new EnchantmentInstance($e, $levelData["level"]));
			}
		}

		return $item;
	}

	public function get(string $kit): ?Kit {
		return $this->kits[$kit] ?? null;
	}

	/**
	 * @return Kit[]
	 */
	public function getAll() : array{
		return $this->kits;
	}
}