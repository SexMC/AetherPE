<?php

declare(strict_types=1);

namespace skyblock;

use CortexPE\Commando\PacketHooker;
use czechpmdevs\multiworld\generator\void\VoidGenerator;
use developerdino\profanityfilter\Filter;
use muqsit\invmenu\InvMenuHandler;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockBreakInfo as BreakInfo;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockIdentifierFlattened as BIDFlattened;
use pocketmine\block\BlockLegacyIds as Ids;
use pocketmine\block\BlockToolType;
use pocketmine\block\BlockToolType as ToolType;
use pocketmine\block\Chest;
use pocketmine\block\Melon;
use pocketmine\block\Pumpkin;
use pocketmine\block\tile\TileFactory;
use pocketmine\block\VanillaBlocks;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\data\bedrock\PotionTypeIdMap;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\Rarity;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;
use pocketmine\item\PotionType;
use pocketmine\item\ToolTier;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use pocketmine\world\generator\GeneratorManager;
use skyblock\blocks\custom\CustomBlockTile;
use skyblock\blocks\CustomFarmland;
use skyblock\blocks\CustomFurnaceBlock;
use skyblock\blocks\CustomHopper;
use skyblock\blocks\CustomLever;
use skyblock\blocks\CustomWoodenButton;
use skyblock\blocks\Ice;
use skyblock\blocks\SkyBlockBrewingStand;
use skyblock\blocks\Water;
use skyblock\caches\combat\CombatCache;
use skyblock\caches\playtime\PlayTimeCache;
use skyblock\caches\skin\SkinCache;
use skyblock\communication\CommunicationLogicHandler;
use skyblock\entity\EntityHandler;
use skyblock\islands\IslandHandler;
use skyblock\items\itemmods\ItemModHandler;
use skyblock\items\lootbox\LootboxHandler;
use skyblock\items\masks\MasksHandler;
use skyblock\items\pets\PetHandler;
use skyblock\items\SkyblockItemFactory;
use skyblock\items\special\SpecialItemHandler;
use skyblock\items\tools\SpecialWeaponHandler;
use skyblock\items\transactions\TransactionAPI;
use skyblock\items\vanilla\SplashPotion;
use skyblock\items\vanilla\VanillaItemHandler;
use skyblock\listeners\ListenerManager;
use skyblock\misc\areas\AreaHandler;
use skyblock\misc\arena\ArenaManager;
use skyblock\misc\fishing\FishingHandler;
use skyblock\misc\floatingtext\FloatingTextHandler;
use skyblock\misc\npcquests\NpcQuestHandler;
use skyblock\misc\pve\PveHandler;
use skyblock\misc\recipes\RecipesHandler;
use skyblock\tasks\IslandUnloadTask;
use skyblock\tasks\RestartTask;
use skyblock\tiles\BrewingStandTile;
use skyblock\tiles\CropTile;
use skyblock\tiles\CustomChestTile;
use skyblock\tiles\HyperFurnaceTile;
use skyblock\tiles\LuckyBlockTile;
use skyblock\utils\ProfileUtils;
use skyblock\utils\ScoreboardUtils;
use skyblock\utils\Utils;
use SOFe\AwaitStd\AwaitStd;

class Main extends PluginBase{

	public const PREFIX = "§r§4(§l!§r§4) §7";
	public const PLANET = "§r§l§5P§di§5x§de§5l §dR§5e§da§5l§di§5t§dy"; //revert back if you want idm -reza

	private static Main $instance;

	private CommunicationLogicHandler $communicationLogicHandler;

	private static bool $debug = true;

	private AwaitStd $std;

	private array $handlers = [];



	public static function debug(string $message): void {
		if (self::$debug) {
			self::$instance->getLogger()->info("DEBUG: " . $message);
		}
	}
	protected function onEnable() : void{
		date_default_timezone_set('Europe/London');

		$this->saveResource("sell.yml", true);

		$this->saveDefaultConfig();
		$this->std = AwaitStd::init($this);

		if(!InvMenuHandler::isRegistered()){
			InvMenuHandler::register($this);
		}
		if(!PacketHooker::isRegistered()) {
			PacketHooker::register($this);
		}


		Utils::$serverType = $this->getServer()->getConfigGroup()->getConfigString("server-type");
		Utils::$serverName = $this->getServer()->getConfigGroup()->getConfigString("server-name", "error");
		Utils::$isDev = $this->getServer()->getConfigGroup()->getConfigBool("is-dev", false);
		var_dump(Utils::$isDev);


		self::$instance = $this;
		require self::locateComposerAutoloader();




		new Database();


		ScoreboardUtils::init();
		EnchantmentIdMap::getInstance()->register(138, new Enchantment("GlowItem", Rarity::MYTHIC, ItemFlags::ALL, ItemFlags::ALL,5));

		AreaHandler::initialise();
		new SkinCache();
		new CombatCache();
		new PlayTimeCache();

		new VanillaItemHandler();
		new ItemModHandler($this);
		new MasksHandler();
		$this->communicationLogicHandler = new CommunicationLogicHandler($this->getServer());
		new Filter();
		IslandHandler::initialise();
		//SpecialSetHandler::initialise();
		new SpecialItemHandler();
		new LootboxHandler();
		new CommandHandler($this);
		new ListenerManager($this);
		new EntityHandler();
		new TransactionAPI();
		new SpecialWeaponHandler();
		//new QuestHandler();
		new FloatingTextHandler();
		//AccessoryHandler::initialise();
		FishingHandler::initialise();
		NpcQuestHandler::initialise();
		PveHandler::initialise();

		PetHandler::getInstance();
		RecipesHandler::initialise();
		SkyblockItemFactory::initialise();

		//var_dump(ItemFactory::getInstance()->get(941,1 ));
		//ItemTranslator::


		//new SpecialItemHandler();
		//new CustomEnchantHandler();

        $itemFactory = ItemFactory::getInstance();
        $blockFactory = BlockFactory::getInstance();
        $tileFactory = TileFactory::getInstance();

        foreach(PotionType::getAll() as $type){
            $typeId = PotionTypeIdMap::getInstance()->toId($type);
            $itemFactory->register(new SplashPotion(new ItemIdentifier(ItemIds::SPLASH_POTION, $typeId), $type->getDisplayName() . " Splash Potion", $type), true);
        }

	//	$itemFactory::getInstance()->register(new CustomFishingRod(new ItemIdentifier(ItemIds::FISHING_ROD, 0), "Fishing Rod"), true);

        $blockFactory->register(new Water(), true);
        $blockFactory->register(new Ice(), true);
		$blockFactory->register(new CustomWoodenButton(new BlockIdentifier(Ids::WOODEN_BUTTON, 0), "Wooden Button",new BreakInfo(0.5, ToolType::AXE)), true);
		$blockFactory->register(new CustomLever(new BlockIdentifier(Ids::LEVER, 0), "Lever", new BreakInfo(0.5)), true);
		$blockFactory->register(new SkyBlockBrewingStand(), true);

		$chestBreakInfo = new BlockBreakInfo(2.5, BlockToolType::AXE);
		$blockFactory->register(new Chest(new BlockIdentifier(Ids::CHEST, 0, null, CustomChestTile::class), "Chest", $chestBreakInfo), true);
        $blockFactory->register(new Melon(new BlockIdentifier(Ids::MELON_BLOCK, 0, null, CropTile::class), "Melon Block", new BlockBreakInfo(1.0, BlockToolType::AXE)), true);
        $blockFactory->register(new Pumpkin(new BlockIdentifier(Ids::PUMPKIN, 0, null,CropTile::class), "Pumpkin", new BlockBreakInfo(1.0, BlockToolType::AXE)), true);


		$hopper = VanillaBlocks::HOPPER();
		$farmland = VanillaBlocks::FARMLAND();
		$blockFactory->register(new CustomHopper($hopper->getIdInfo(), $hopper->getName(), $hopper->getBreakInfo()), true);
		$blockFactory->register(new CustomFarmland($farmland->getIdInfo(), $farmland->getName(), $farmland->getBreakInfo()), true);
		$blockFactory->register(new CustomFurnaceBlock(new BIDFlattened(Ids::FURNACE, [Ids::LIT_FURNACE], 0, null, HyperFurnaceTile::class), "Furnace", new BreakInfo(3.5, ToolType::PICKAXE, ToolTier::WOOD()->getHarvestLevel())), true);


		$tileFactory->register(CustomChestTile::class, ["Chest", "minecraft:chest"]);
        $tileFactory->register(CropTile::class, ["minecraft:melon", "minecraft:pumpkin"]);
        $tileFactory->register(CustomBlockTile::class, ["aetherpe:custom_block_tile"]);
        $tileFactory->register(LuckyBlockTile::class, ["aetherpe:lucky_block_tile"]);
        $tileFactory->register(BrewingStandTile::class, ["aetherpe:brewing_stand_tile"]);
        $tileFactory->register(HyperFurnaceTile::class, ["Furnace", "minecraft:furnace"]);

		$this->setupTasks();

		GeneratorManager::getInstance()->addGenerator(VoidGenerator::class, "largeBiomes", fn() => null, true);


		//$array = [];
		//foreach(CustomEnchantHandler::getAllCustomEnchantsByName() as $k => $v){
		//	/** @var CustomEnchant $ce */
		//	$ce = $v;
		//	$array[$ce->getIdentifier()->getName()] = ["description" => $ce->getDescription(), "rarity" => TextFormat::clean($ce->getRarity()->getDisplayName()), "max" => $ce->getMaxLevel(), "important" => $ce->getIdentifier()->isImportant()];
		//}
		//file_put_contents($this->getDataFolder() . "ces.json", json_encode($array));
	}
	
	public function setupTasks(): void {
		/*$this->getScheduler()->scheduleRepeatingTask(new IntervalExecuteTask(900, [15*60, 5*60, 10*60, 3*60, 2*60, 1*60, 30, 15, 10, 5, 4, 3, 2, 1, 0], function(string $time, int $seconds): void {
			if($seconds === 0){
				EntityHandler::getInstance()->clearlag(false);
				
				return;
			}
			
			Server::getInstance()->broadcastMessage(Main::PREFIX . "Clearlag will commence in §c$time §7on §c" . Utils::getServerName());
		}), 20);*/

		$this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function(){
			Server::getInstance()->getNetwork()->unblockAddress("172.18.0.1");
		}), 1);

		if(Utils::isIslandServer()){
			$this->getScheduler()->scheduleDelayedRepeatingTask(new IslandUnloadTask(), 20, 20 * 60);
		}

		$this->getScheduler()->scheduleRepeatingTask(new RestartTask(), 20);

	}

	protected function onDisable() : void{
		foreach($this->getServer()->getOnlinePlayers() as $player){
		  $player->disconnect("Server Restart", "Server Restart");
		}

		if(Utils::isHubServer()){
			ArenaManager::getInstance()->closeAll();
		}

		if(Utils::isIslandServer()){
			ProfileUtils::saveProfilesSynchronously();
		}

		foreach (Utils::$importantTasks as $importantTask) {
			$importantTask->run();
		}

		foreach($this->handlers as $handler){
			$handler->onDisable($this);
		}

		CommunicationLogicHandler::getInstance()->thread->setIsRunning(false);
		Database::getInstance()->getLibasynql()->waitAll();

	}

	public static function getInstance() : Main{
		return self::$instance;
	}

	public function getCommunicationLogicHandler() : CommunicationLogicHandler{
		return $this->communicationLogicHandler;
	}

	public function getStd() : AwaitStd{
		return $this->std;
	}

	private static function locateComposerAutoloader(): string {
		if(\Phar::running(true) !== "") {
			return \Phar::running(true) . "/vendor/autoload.php";
		} elseif(is_file($path = dirname(__DIR__, 2) . "/vendor/autoload.php")) {
			return $path;
		} else {
			trigger_error("[AetherPE] Couldn't find composer autoloader", E_USER_ERROR);
		}
	}

	public function addHandler($hander): void {
		$this->handlers[] = $hander;
	}
}
