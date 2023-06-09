<?php

declare(strict_types=1);

namespace skyblock\entity\minion;

use pocketmine\block\VanillaBlocks;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Living;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\inventory\SimpleInventory;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\ActorEventPacket;
use pocketmine\network\mcpe\protocol\MobEquipmentPacket;
use pocketmine\network\mcpe\protocol\types\ActorEvent;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\inventory\ContainerIds;
use pocketmine\network\mcpe\protocol\types\inventory\ItemStackWrapper;
use pocketmine\player\Player;
use pocketmine\Server;
use skyblock\items\SkyblockItem;
use skyblock\items\SkyblockItems;
use skyblock\items\special\SpecialItem;
use skyblock\items\special\types\minion\AutoSmelter;
use skyblock\items\special\types\minion\BioMinionFuel;
use skyblock\items\special\types\minion\CoalMinionFuel;
use skyblock\items\special\types\minion\Compactor;
use skyblock\items\special\types\minion\DiamondSpreading;
use skyblock\items\special\types\minion\DieselMinionFuel;
use skyblock\items\special\types\minion\EnchantedLavaBucket;
use skyblock\items\special\types\minion\SuperCompactor3000;
use skyblock\items\tools\SpecialWeapon;
use skyblock\items\tools\types\pve\FlintShovel;
use skyblock\menus\minions\MinionMenu;
use skyblock\misc\recipes\RecipesHandler;
use skyblock\utils\Utils;

abstract class BaseMinion extends Living {

	const FUEL_TYPES = [BioMinionFuel::class, CoalMinionFuel::class, DieselMinionFuel::class];

	/** @var Item[] */
	private static ?array $enchantedItems = null;
	/** @var Item[] */
	private static array $autoSmelt = 	[
		ItemIds::IRON_ORE => ItemIds::IRON_INGOT,
		ItemIds::GOLD_ORE => ItemIds::GOLD_INGOT,
		ItemIds::COBBLESTONE => ItemIds::STONE,
	];




	/** @var array<string, string> */
	private array $stringMap = [];
	/** @var array<string, int> */
	private array $intMap = [];
	private MobEquipmentPacket $itemPacket;
	private int $currentTick = 0;

	protected bool $exhausted = false;
	protected bool $perfectLocation = true;
	protected bool $inventoryFull = false;
	protected Item $itemInHand;

	protected SimpleInventory $minionInventory;
	protected SimpleInventory $fuelInventory;
	protected ?Item $upgradeItem1 = null;
	protected ?Item $upgradeItem2 = null;

	protected MinionLevel $level;

	protected function initEntity(CompoundTag $nbt) : void{
		parent::initEntity($nbt);
		$this->setScale(0.5);
		$this->setNameTagVisible(false);

		$this->minionInventory = new SimpleInventory(15);
		$this->fuelInventory = new SimpleInventory(10);


		foreach($this->getSavingKeys() as $key){

			if(($tag = $nbt->getTag($key)) !== null){
				if($tag instanceof StringTag){
					$this->stringMap[$key] = $tag->getValue();
				}

				if($tag instanceof IntTag){
					$this->intMap[$key] = $tag->getValue();
				}
			}
		}

		foreach(($nbt->getListTag("minionInventory")?->getValue() ?? []) as $tag){
			$this->minionInventory->addItem(Item::nbtDeserialize($tag));
		}

		foreach(($nbt->getListTag("fuelInventory")?->getValue() ?? []) as $tag){
			$this->fuelInventory->addItem(Item::nbtDeserialize($tag));
		}

		if($nbt->getCompoundTag("itemUpgrade1")) {
			$this->upgradeItem1 = Item::nbtDeserialize($nbt->getCompoundTag("itemUpgrade1"));
		}

		if($nbt->getCompoundTag("itemUpgrade2")) {
			$this->upgradeItem2 = Item::nbtDeserialize($nbt->getCompoundTag("itemUpgrade2"));
		}

		$this->setupAppearance();
		$this->itemPacket = MobEquipmentPacket::create($this->getId(), ItemStackWrapper::legacy(TypeConverter::getInstance()->coreItemStackToNet($this->itemInHand)), 0, 0, ContainerIds::INVENTORY);

		$this->onInitialize();
		$this->setupMinionLevelInstance();

		if($this->isFlaggedForDespawn()) return;

		$this->doOfflineCalculations();


		if(self::$enchantedItems !== null){
			self::$enchantedItems = [
				SkyblockItems::ENCHANTED_COBBLESTONE(),
				SkyblockItems::ENCHANTED_OBSIDIAN(),
				SkyblockItems::ENCHANTED_COAL(),
				SkyblockItems::ENCHANTED_EMERALD(),
				SkyblockItems::ENCHANTED_GOLD(),
				SkyblockItems::ENCHANTED_LAPIS_LAZULI(),
				SkyblockItems::ENCHANTED_IRON(),
				SkyblockItems::ENCHANTED_DIAMOND(),
				SkyblockItems::ENCHANTED_REDSTONE(),


				SkyblockItems::ENCHANTED_CHICKEN(),
				SkyblockItems::ENCHANTED_GUNPOWDER(),
				SkyblockItems::ENCHANTED_RABBIT_FOOT(),
				SkyblockItems::ENCHANTED_PORKCHOP(),
				SkyblockItems::ENCHANTED_MUTTON(),
				SkyblockItems::ENCHANTED_STRING(),
				SkyblockItems::ENCHANTED_SPIDER_EYE(),
				SkyblockItems::ENCHANTED_ROTTENFLESH(),
				SkyblockItems::ENCHANTED_SLIMEBALL(),


				SkyblockItems::ENCHANTED_HAY_BALE(),
				SkyblockItems::ENCHANTED_CARROT(),
				SkyblockItems::ENCHANTED_POTATO(),
				SkyblockItems::ENCHANTED_PUMPKIN(),
				SkyblockItems::ENCHANTED_MELON(),
				SkyblockItems::ENCHANTED_SUGARCANE(),
			];

			foreach(self::$enchantedItems as $v){
				self::$enchantedItems[$v->getId()] = $v;
			}
		}
	}

	public function attack(EntityDamageEvent $source) : void{
		$source->cancel();

		parent::attack($source);

		if($source instanceof EntityDamageByEntityEvent){
			$damager = $source->getDamager();

			if($damager instanceof Player){
				(new MinionMenu($this))->send($damager);
			}
		}
	}

	public function hasFuel(): bool {
		$item = $this->fuelInventory->getItem(0);
		if($item->isNull()) return false;

		if(!in_array(SpecialItem::getSpecialItem($item)::class, self::FUEL_TYPES)){
			return false;
		}

		return true;
	}

	public function reduceFuel(int $seconds): bool {
		$item = $this->fuelInventory->getItem(0);
		if($item->isNull()) return false;

		$class = SpecialItem::getSpecialItem($item)::class;
		if(!in_array($class, self::FUEL_TYPES)){
			return false;
		}

		$defaultDuration = match($class) {
			BioMinionFuel::class => 8 * 3600,
			CoalMinionFuel::class => 16 * 3600,
			DieselMinionFuel::class => 24 * 3600
		};

		$newDuration = $item->getNamedTag()->getInt("duration", $defaultDuration) - $seconds;
		$item->getNamedTag()->setInt("duration", $newDuration);
		$this->fuelInventory->setItem(0, $item);

		if($newDuration <= 0){
			//remove the exhausted fuel
			$this->fuelInventory->setItem(0, VanillaItems::AIR());
			//check if other fuels can be moved to slot 0

			foreach($this->fuelInventory->getContents() as $content){
				if($content->isNull()) continue;

				$this->fuelInventory->setItem(0, $content);
				break;
			}

			if($newDuration < 0){
				$this->reduceFuel(abs($newDuration));
			}
		}

		return true;
	}

	public function getTotalFuelTimeLeftInSeconds(): int {
		$total = 0;

		foreach($this->fuelInventory->getContents() as $item){
			if($item->isNull()) continue;

			$class = SpecialItem::getSpecialItem($item)::class;
			if(!in_array($class, self::FUEL_TYPES)){
				continue;
			}

			$defaultDuration = match($class) {
				BioMinionFuel::class => 8 * 3600,
				CoalMinionFuel::class => 16 * 3600,
				DieselMinionFuel::class => 24 * 3600
			};

			$total += $item->getNamedTag()->getInt("duration", $defaultDuration);
		}

		return $total;
	}

	public function addItem(Item|array $items, bool $checkForCan = true): bool {
		if(!is_array($items)){
			$items = [$items];
		}

		if($checkForCan){
			foreach($items as $item){
				if(!$this->minionInventory->canAddItem($item)){
					return false;
				}
			}
		}

		foreach($items as $item){
			$this->minionInventory->addItem($item);
		}

		return true;
	}

	public function isInventoryFull() : bool{
		return $this->inventoryFull;
	}

	public function setInventoryFull(bool $inventoryFull) : void{
		$this->inventoryFull = $inventoryFull;


		if($inventoryFull === true){
			$this->setNameTag("§cMinion inventory is full! :|");
			$this->setNameTagAlwaysVisible(true);
			return;
		}

		$this->setNameTag("§a{$this->getString("owner", "error")}'s Minion");
		$this->setNameTagAlwaysVisible(false);
	}

	public static function getStorageSizeByLevel(int $level): int {
		return [
			1 => 2,
			2 => 3,
			3 => 4,
			4 => 6,
			5 => 8,
			6 => 9,
			7 => 10,
			8 => 12,
			9 => 13,
			10 => 14,
			11 => 15,
		][$level];
	}

	public function onUpdate(int $currentTick) : bool{
		$update = parent::onUpdate($currentTick);

		if(++$this->currentTick >= $this->getSpeedInTicks()){
			$this->currentTick = 0;

			if(!$this->hasFuel()){
				if(!$this->isExhausted()){
					$this->setExhausted(true);
					return $update;
				}
			}

			if($this->isExhausted()){
				$this->setExhausted(false);
				return $update;
			}

			$this->onTick();


			if($currentTick % 20 === 0){
				//TODO enable this back : $this->onUpgradeTick();
			}
		}

		return $update;
	}

	public function onUpgradeTick(): void {
		if($this->hasMinionUpgrade(SkyblockItems::DIAMOND_SPREADING())){
			if(mt_rand(1, 1000) === 38){
				$this->getMinionInventory()->addItem(VanillaItems::DIAMOND());
			}
		}

		if($this->hasMinionUpgrade(SkyblockItems::FLINT_SHOVEL())){
			foreach($this->getMinionInventory()->getContents() as $content){
				$total = Utils::getTotalItemCount(VanillaBlocks::GRAVEL()->asItem(), $this->getMinionInventory());

				if($total > 0){
					$this->minionInventory->removeItem(VanillaBlocks::GRAVEL()->asItem()->setCount($total));
					$this->minionInventory->addItem(VanillaItems::FLINT()->setCount($total));
				}
			}
		}

		if($this->hasMinionUpgrade(SkyblockItems::COMPACTOR())){
			if(mt_rand(1, 3) === 1){
				foreach($this->getMinionInventory()->getContents() as $item){
					$data = \skyblock\items\misc\minion\Compactor::getBlockItemByItem($item);

					if($data === null) continue;

					$count = $data[0];
					$crafting = ItemFactory::getInstance()->get($data[1]);

					$total = Utils::getTotalItemCount($item, $this->getMinionInventory());
					$craftingCount = (int) floor($total / $count);
					$removing = clone $item;

					$removing->setCount($craftingCount * $count);
					$this->getMinionInventory()->removeItem($removing);

					$crafting->setCount($craftingCount);
					$this->getMinionInventory()->addItem($crafting);
					break;
				}
			}
		}

		if($this->hasMinionUpgrade(SkyblockItems::SUPER_COMPACTOR_3000())){
			if(mt_rand(1, 3) === 1){
				foreach($this->getMinionInventory()->getContents() as $item){
					if(!isset(self::$enchantedItems[$item->getId()])) continue;

					$crafting = self::$enchantedItems[$item->getId()];
					$recipe = RecipesHandler::getInstance()->getRecipeByItem($crafting);

					var_dump("hereee");
					if($recipe === null) {
						var_dump("recipe is null");
						continue;
					}

					$totalCountNeeded = 0;
					foreach($recipe->getInput() as $v){
						$totalCountNeeded += $v->getCount();
;					}

					if(Utils::getTotalItemCount($item, $this->getMinionInventory()) < $totalCountNeeded) {
						var_dump("not enough count");
						continue;
					}

					var_dump("crafts");
					$removing = clone $item;
					$removing->setCount($totalCountNeeded);
					$this->getMinionInventory()->removeItem($removing);

					$this->getMinionInventory()->addItem($crafting);
					break;
				}
			}
		}


		if($this->hasMinionUpgrade(SkyblockItems::AUTO_SMELTER())){
			if(mt_rand(1, 3) === 1){
				foreach($this->getMinionInventory()->getContents() as $content) {
					if(!isset(self::$autoSmelt[$content->getId()])) continue;

					$converting = clone self::$autoSmelt[$content->getId()];
					$converting->setCount($content->getCount());
					$this->getMinionInventory()->removeItem($content);
					$this->getMinionInventory()->addItem($converting);
					break;
				}
			}
		}
	}

	public function hasMinionUpgrade(SkyblockItem $item): bool {
		if($this->upgradeItem1 instanceof $item){
			return true;
		}

		if($this->upgradeItem2 instanceof $item){
			return true;
		}

		return false;
	}

	public function getSpeedInSeconds(): int {
		$speed = (int) $this->level->getBaseSpeed();

		/*TODO: if($this->hasMinionUpgrade(EnchantedLavaBucket::getItemTag())){
			return (int) ($speed * 0.75);
		}*/

		return $speed;
	}

	public function getSpeedInTicks(): int {
		return $this->getSpeedInSeconds() * 20;
	}

	public function getFuelInventory() : SimpleInventory{
		return $this->fuelInventory;
	}

	public function getMinionInventory() : SimpleInventory{
		return $this->minionInventory;
	}

	protected function getSavingKeys(): array {
		return [
			"owner",
			"level",
			"lastSave",
		];
	}

	public function spawnTo(Player $player) : void{
		parent::spawnTo($player);
		$player->getNetworkSession()->sendDataPacket($this->itemPacket);
	}

	public function saveNBT() : CompoundTag{
		$nbt = parent::saveNBT();


		$this->setInt("lastSave", time());

		foreach($this->intMap as $key => $value){
			$nbt->setInt($key, $value);
		}

		foreach($this->stringMap as $key => $value){
			$nbt->setString($key, $value);
		}

		$inventoryTag = new ListTag([], NBT::TAG_Compound);
		foreach($this->minionInventory->getContents() as $content){
			$inventoryTag->push($content->nbtSerialize());
		}
		$nbt->setTag("minionInventory", $inventoryTag);

		$inventoryTag = new ListTag([], NBT::TAG_Compound);
		foreach($this->fuelInventory->getContents() as $content){
			$inventoryTag->push($content->nbtSerialize());
		}
		$nbt->setTag("fuelInventory", $inventoryTag);

		if($this->upgradeItem1){
			$nbt->setTag("itemUpgrade1", $this->upgradeItem1->nbtSerialize());
		}

		if($this->upgradeItem2){
			$nbt->setTag("itemUpgrade2", $this->upgradeItem2->nbtSerialize());
		}

		return $nbt;
	}

	protected function swingArm(): void {
		Server::getInstance()->broadcastPackets($this->getViewers(), [ActorEventPacket::create($this->id, ActorEvent::ARM_SWING, 0)]);
	}

	public function isExhausted(): bool {
		return $this->exhausted;
	}

	public function setExhausted(bool $value): void {
		$this->exhausted = $value;

		if($value === true){
			$this->setNameTag("§cI need fuel to run! :|");
			$this->setNameTagAlwaysVisible(true);
			return;
		}

		$this->setNameTag("§a{$this->getString("owner", "error")}'s Minion");
		$this->setNameTagAlwaysVisible(false);
	}

	public function isLocationPerfect(): bool {
		return $this->perfectLocation;
	}
	
	public function setLocationPerfect(bool $value): void {
		$this->perfectLocation = $value;

		if($value === false){
			$this->setNameTag("§cThis location isn't perfect! :(");
			$this->setNameTagAlwaysVisible(true);
			return;
		}
		
		$this->setNameTag("§a{$this->getString("owner", "error")}'s Minion");
		$this->setNameTagAlwaysVisible(false);
	}

	public function getUpgradeItem1() : ?Item{
		return $this->upgradeItem1;
	}

	public function setUpgradeItem1(?Item $upgradeItem1) : void{
		$this->upgradeItem1 = $upgradeItem1;
	}

	public function getUpgradeItem2() : ?Item{
		return $this->upgradeItem2;
	}

	public function setUpgradeItem2(?Item $upgradeItem2) : void{
		$this->upgradeItem2 = $upgradeItem2;
	}



	public function getInt(string $key, int $default = null): ?int {
		return $this->intMap[$key] ?? $default;
	}

	public function getString(string $key, string $default = null) : ?string{
		return $this->stringMap[$key] ?? $default;
	}

	public function setString(string $key, string $value) : void{
		$this->stringMap[$key] = $value;
	}

	public function setInt(string $key, int $value): void {
		$this->intMap[$key] = $value;
	}

	protected function getInitialSizeInfo() : EntitySizeInfo{
		return new EntitySizeInfo(1.975, 0.5);
	}

	public static function getNetworkTypeId() : string{
		return EntityIds::ARMOR_STAND;
	}

	public function getName() : string{
		return "Test Minion";
	}

	protected function move(float $dx, float $dy, float $dz) : void{
		//disable movement
	}

	public function canBeMovedByCurrents() : bool{
		return false; //disable movement
	}

	public abstract function setupMinionLevelInstance(): void;

	abstract protected function setupAppearance(): void;
	abstract protected function onTick(): void;
	abstract protected function onInitialize(): void;
	abstract protected function doOfflineCalculations(): void;

	abstract public function getEggItem(): Item;
}