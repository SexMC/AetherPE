<?php

declare(strict_types=1);

namespace skyblock\misc\quests;

use pocketmine\block\VanillaBlocks;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use skyblock\items\crates\types\AetherCrate;
use skyblock\items\crates\types\CommonCrate;
use skyblock\items\crates\types\RareCrate;
use skyblock\items\customenchants\ICustomEnchant;
use skyblock\items\lootbox\types\armor\ArmorSetPieceGeneratorLootbox;
use skyblock\items\lootbox\types\destruction\HeroicDestructionEssenceLootbox;
use skyblock\items\lootbox\types\farming\Level40Lootbox;
use skyblock\items\lootbox\types\farming\Level60Lootbox;
use skyblock\items\lootbox\types\MemoryLaneLootbox;
use skyblock\items\lootbox\types\MysteryRankGeneratorLootbox;
use skyblock\items\lootbox\types\quest\HeroicQuestTokenGeneratorLootbox;
use skyblock\items\lootbox\types\quest\QuestTokenGeneratorLootbox;
use skyblock\items\lootbox\types\SpawnerLootbox;
use skyblock\items\special\types\CustomEnchantmentBook;
use skyblock\items\special\types\EssencePouch;
use skyblock\items\special\types\MoneyPouch;
use skyblock\items\special\types\SpawnerItem;
use skyblock\items\special\types\XpPouch;
use skyblock\items\vanilla\CustomFishingRod;
use skyblock\Main;
use skyblock\misc\quests\types\BlockBreakQuest;
use skyblock\misc\quests\types\BlockPlaceQuest;
use skyblock\misc\quests\types\ChatQuest;
use skyblock\misc\quests\types\CoinflipWinQuest;
use skyblock\misc\quests\types\CommandQuest;
use skyblock\misc\quests\types\EatQuest;
use skyblock\misc\quests\types\FishQuest;
use skyblock\misc\quests\types\KillEntityQuest;
use skyblock\misc\quests\types\LootboxOpenQuest;
use skyblock\misc\quests\types\reward\QuestItemReward;
use skyblock\misc\quests\types\SellQuest;
use skyblock\misc\quests\types\UniversalQuest;
use skyblock\misc\quests\types\VoteQuest;
use skyblock\misc\quests\types\XpSpendQuest;
use skyblock\player\AetherPlayer;
use skyblock\player\ranks\AstronomicalRank;
use skyblock\sessions\Session;
use skyblock\traits\AetherSingletonTrait;

class QuestHandler {
	use AetherSingletonTrait;

	private int $id = 0;

	public array $dailyQuests = [];
	public array $normalQuests = [];
	public array $islandQuests = [];

	private VoteQuest $voteQuest;


	public function __construct(){
		if(self::$instance !== null) return;
		self::setInstance($this);

		//$this->setupNormalQuests();
		//$this->setupDailyQuests();
		//$this->setupIslandQuests();
	}

	public function setupDailyQuests(): void {
		$this->addDailyQuest($this->voteQuest = new VoteQuest(1, "Vote for the server", "Vote for the server", "1x Quest Token Generator", [new QuestItemReward(QuestTokenGeneratorLootbox::getItem(), 1)]));
	
		$this->addDailyQuest(new XpSpendQuest(250000, "Enchanter Quest I", "Spend 250,000 XP on /enchanter", "", [new QuestItemReward(QuestTokenGeneratorLootbox::getItem(), 1)]));
		$this->addDailyQuest(new XpSpendQuest(500000, "Enchanter Quest II", "Spend 500,000 XP on /enchanter", "", [new QuestItemReward(QuestTokenGeneratorLootbox::getItem(), 2)]));
		$this->addDailyQuest(new XpSpendQuest(2500000, "Enchanter Quest III", "Spend 2,500,000 XP on /enchanter", "", [new QuestItemReward(QuestTokenGeneratorLootbox::getItem(), 3)]));
		$this->addDailyQuest(new XpSpendQuest(5000000, "Enchanter Quest IV", "Spend 5,000,000 XP on /enchanter", "", [new QuestItemReward(QuestTokenGeneratorLootbox::getItem(), 4), new QuestItemReward(HeroicQuestTokenGeneratorLootbox::getItem(), 1)]));
		$this->addDailyQuest(new XpSpendQuest(10000000, "Enchanter Quest V", "Spend 10,000,000 XP on /enchanter", "", [new QuestItemReward(QuestTokenGeneratorLootbox::getItem(), 4), new QuestItemReward(HeroicQuestTokenGeneratorLootbox::getItem(), 2)]));
		$this->addDailyQuest(new XpSpendQuest(20000000, "Enchanter Quest VI", "Spend 20,000,000 XP on /enchanter", "", [new QuestItemReward(QuestTokenGeneratorLootbox::getItem(), 4), new QuestItemReward(HeroicQuestTokenGeneratorLootbox::getItem(), 4)]));
	
	
		$this->addDailyQuest(new FishQuest(25, "Basic Fishing I", "Fish and catch something 25 times", "", [new QuestItemReward(QuestTokenGeneratorLootbox::getItem(), 1)]));
		$this->addDailyQuest(new FishQuest(50, "Basic Fishing II", "Fish and catch something 50 times", "", [new QuestItemReward(QuestTokenGeneratorLootbox::getItem(), 2)]));
		$this->addDailyQuest(new FishQuest(100, "Getting good at fishing III", "Fish and catch something 100 times", "", [new QuestItemReward(QuestTokenGeneratorLootbox::getItem(), 3), new QuestItemReward(HeroicQuestTokenGeneratorLootbox::getItem(), 1)]));
		$this->addDailyQuest(new FishQuest(150, "Getting good at fishing IV", "Fish and catch something 150 times", "", [new QuestItemReward(QuestTokenGeneratorLootbox::getItem(), 3), new QuestItemReward(HeroicQuestTokenGeneratorLootbox::getItem(), 2)]));
		$this->addDailyQuest(new FishQuest(250, "Insane at fishing V", "Fish and catch something 250 times", "", [new QuestItemReward(QuestTokenGeneratorLootbox::getItem(), 3), new QuestItemReward(HeroicQuestTokenGeneratorLootbox::getItem(), 3)]));

		$this->addDailyQuest(new CoinflipWinQuest(5, "Coinflip Master I", "Win 5 coinflips", "", [new QuestItemReward(QuestTokenGeneratorLootbox::getItem(), 1)]));
		$this->addDailyQuest(new CoinflipWinQuest(10, "Coinflip Master II", "Win 10 coinflips", "", [new QuestItemReward(QuestTokenGeneratorLootbox::getItem(), 2)]));
		$this->addDailyQuest(new CoinflipWinQuest(15, "Coinflip Master III", "Win 15 coinflips", "", [new QuestItemReward(QuestTokenGeneratorLootbox::getItem(), 1), new QuestItemReward(HeroicQuestTokenGeneratorLootbox::getItem(), 1)]));
	
		$this->addDailyQuest(new LootboxOpenQuest(SpawnerLootbox::getName(), 1, "Mob Examiner I", "Open 1 §r§l§fSpawner \"§r§7???§l§f\"", "", [new QuestItemReward(QuestTokenGeneratorLootbox::getItem(), 1)]));
		$this->addDailyQuest(new LootboxOpenQuest(SpawnerLootbox::getName(), 3, "Mob Examiner II", "Open 3 §r§l§fSpawner \"§r§7???§l§f\"", "", [new QuestItemReward(QuestTokenGeneratorLootbox::getItem(), 2)]));
		$this->addDailyQuest(new LootboxOpenQuest(SpawnerLootbox::getName(), 6, "Mob Examiner III", "Open 6 §r§l§fSpawner \"§r§7???§l§f\"", "", [new QuestItemReward(QuestTokenGeneratorLootbox::getItem(), 2), new QuestItemReward(HeroicQuestTokenGeneratorLootbox::getItem(), 1)]));
		$this->addDailyQuest(new LootboxOpenQuest(SpawnerLootbox::getName(), 10, "Mob Examiner IV", "Open 10 §r§l§fSpawner \"§r§7???§l§f\"", "", [new QuestItemReward(QuestTokenGeneratorLootbox::getItem(), 2), new QuestItemReward(HeroicQuestTokenGeneratorLootbox::getItem(), 2)]));
	
	
		$this->addDailyQuest(new KillEntityQuest(EntityIds::BLAZE, 2500, "Blaze Slayer I", "Slay 2,500 blazes", "", [new QuestItemReward(QuestTokenGeneratorLootbox::getItem(), 1), new QuestItemReward(HeroicQuestTokenGeneratorLootbox::getItem(), 1)]));
		$this->addDailyQuest(new KillEntityQuest(EntityIds::BLAZE, 10000, "Blaze Slayer II", "Slay 10,000 blazes", "", [new QuestItemReward(QuestTokenGeneratorLootbox::getItem(), 2), new QuestItemReward(HeroicQuestTokenGeneratorLootbox::getItem(), 2)]));
		$this->addDailyQuest(new KillEntityQuest(EntityIds::ZOMBIE_PIGMAN, 2500, "Zombie Pigman Slayer I", "Slay 2,500 zombie pigmans", "", [new QuestItemReward(QuestTokenGeneratorLootbox::getItem(), 1), new QuestItemReward(HeroicQuestTokenGeneratorLootbox::getItem(), 1)]));
		$this->addDailyQuest(new KillEntityQuest(EntityIds::ZOMBIE_PIGMAN, 10000, "Zombie Pigman Slayer II", "Slay 10,000 zombie pigmans", "", [new QuestItemReward(QuestTokenGeneratorLootbox::getItem(), 2), new QuestItemReward(HeroicQuestTokenGeneratorLootbox::getItem(), 2)]));
		$this->addDailyQuest(new KillEntityQuest(EntityIds::COW, 2500, "Cow Slayer I", "Slay 2,500 cows", "", [new QuestItemReward(QuestTokenGeneratorLootbox::getItem(), 1), new QuestItemReward(HeroicQuestTokenGeneratorLootbox::getItem(), 1)]));
		$this->addDailyQuest(new KillEntityQuest(EntityIds::COW, 10000, "Cow Slayer II", "Slay 10,000 cows", "", [new QuestItemReward(QuestTokenGeneratorLootbox::getItem(), 2), new QuestItemReward(HeroicQuestTokenGeneratorLootbox::getItem(), 2)]));


		$this->addDailyQuest(new UniversalQuest(IQuest::ENVOY_CLAIM, 4, "Envoy Seeker I", "Open 4 envoys", "", [new QuestItemReward(QuestTokenGeneratorLootbox::getItem(), 2)]));
		$this->addDailyQuest(new UniversalQuest(IQuest::ENVOY_CLAIM, 12, "Envoy Seeker II", "Open 12 envoys", "", [new QuestItemReward(QuestTokenGeneratorLootbox::getItem(), 6), new QuestItemReward(HeroicQuestTokenGeneratorLootbox::getItem(), 2)]));




		$quest = new BlockBreakQuest(VanillaBlocks::AIR(), 500, "Material Planet I", "Mine 500 ores at material planet", "", [new QuestItemReward(QuestTokenGeneratorLootbox::getItem(), 2), new QuestItemReward(HeroicQuestTokenGeneratorLootbox::getItem(), 1)]);
		$quest->setWorldName("MaterialPlanet");
		$this->addDailyQuest($quest);

		$quest = new BlockBreakQuest(VanillaBlocks::AIR(), 2000, "Material Planet II", "Mine 2,000 ores at material planet", "", [new QuestItemReward(QuestTokenGeneratorLootbox::getItem(), 4), new QuestItemReward(HeroicQuestTokenGeneratorLootbox::getItem(), 2)]);
		$quest->setWorldName("MaterialPlanet");
		$this->addDailyQuest($quest);

		$this->addDailyQuest(new FishQuest(75, "Fisher I", "Fish 75 times", "", [new QuestItemReward(QuestTokenGeneratorLootbox::getItem(), 1)]));
		$this->addDailyQuest(new FishQuest(150, "Fisher II", "Fish 150 times", "", [new QuestItemReward(QuestTokenGeneratorLootbox::getItem(), 2), new QuestItemReward(HeroicQuestTokenGeneratorLootbox::getItem(), 1)]));
		$this->addDailyQuest(new FishQuest(400, "Fisher III", "Fish 400 times", "", [new QuestItemReward(QuestTokenGeneratorLootbox::getItem(), 4), new QuestItemReward(HeroicQuestTokenGeneratorLootbox::getItem(), 2)]));

		$this->addDailyQuest(new BlockBreakQuest(VanillaBlocks::CARROTS(), 1000, "Carrot Lover I", "Farm 1,000 carrots", "", [new QuestItemReward(QuestTokenGeneratorLootbox::getItem(), 1)]));
		$this->addDailyQuest(new BlockBreakQuest(VanillaBlocks::CARROTS(), 5000, "Carrot Lover II", "Farm 5,000 carrots", "", [new QuestItemReward(QuestTokenGeneratorLootbox::getItem(), 3), new QuestItemReward(HeroicQuestTokenGeneratorLootbox::getItem(), 1)]));


		$this->addDailyQuest(new BlockBreakQuest(VanillaBlocks::POTATOES(), 1000, "Potato Lover I", "Farm 1,000 potatoes", "", [new QuestItemReward(QuestTokenGeneratorLootbox::getItem(), 1)]));
		$this->addDailyQuest(new BlockBreakQuest(VanillaBlocks::POTATOES(), 5000, "Potato Lover II", "Farm 5,000 potatoes", "", [new QuestItemReward(QuestTokenGeneratorLootbox::getItem(), 3), new QuestItemReward(HeroicQuestTokenGeneratorLootbox::getItem(), 1)]));

	}

	public function setupNormalQuests(): void {
		$this->addNormalQuest(new ChatQuest("hi", 1, "Socialize", "Say hi in chat", "", [new QuestItemReward(VanillaItems::DIAMOND_PICKAXE(), 1)]));
		$this->addNormalQuest($q = new BlockBreakQuest(VanillaBlocks::BIRCH_LOG(), 5, "Collect some wood", "Break 5 birch logs", "", [new QuestItemReward(VanillaBlocks::OAK_SAPLING()->asItem(), 5)]));
		$q->setSecond(VanillaBlocks::BIRCH_WOOD());


		$this->addNormalQuest(new BlockBreakQuest(VanillaBlocks::COBBLESTONE(), 25, "Cobblestone Master", "Break 25 cobblestone", "", [new QuestItemReward(VanillaItems::WHEAT_SEEDS(), 15), new QuestItemReward(VanillaItems::BONE(), 3)]));
		$this->addNormalQuest(new BlockBreakQuest(VanillaBlocks::WHEAT(), 16, "Winning that bread", "Harvest 16 wheat", "", [new QuestItemReward(VanillaItems::BREAD(), 5)]));
	
		$this->addNormalQuest(new BlockPlaceQuest(VanillaBlocks::OAK_SAPLING(), 10,"#SaveTheEnvironment", "Plant 10 oak saplings", "$2,500", [new QuestItemReward(MoneyPouch::getItem(2500, 3000), 1)]));
		$this->addNormalQuest(new CommandQuest("dailyquests", 1,"Discover Daily Quests", "Run the /dailyquests command", "", [new QuestItemReward(XpPouch::getItem(1000, 5000), 1)]));
		$this->addNormalQuest(new BlockBreakQuest(VanillaBlocks::COBBLESTONE(), 120, "Mine for that spawner!", "Break 120 cobblestone", "", [new QuestItemReward(SpawnerItem::getItem(EntityIds::CHICKEN), 2), new QuestItemReward(VanillaItems::BONE(), 3)]));
		$this->addNormalQuest(new KillEntityQuest(EntityIds::CHICKEN, 175, "Chicken Slayer!", "Kill 175 chickens", "", [new QuestItemReward(EssencePouch::getItem(500, 1000), 1)]));
		$this->addNormalQuest(new SellQuest(VanillaItems::COOKED_CHICKEN(), 115, "Chicken Dealer", "Sell 115 cooked chickens", "", [new QuestItemReward(CommonCrate::getInstance()->getKeyItem(1), 1)]));
		$this->addNormalQuest(new CommandQuest("ah", 1, "Discover the auction house", "Run the /ah command", "", [new QuestItemReward(CustomEnchantmentBook::getItem(ICustomEnchant::RARITY_LEGENDARY), 1)]));
		$this->addNormalQuest(new BlockPlaceQuest(VanillaBlocks::COBBLESTONE(), 300,"Island Expander", "Place 300 cobblestones", "", [new QuestItemReward(MoneyPouch::getItem(7500, 8000), 1)]));
		$this->addNormalQuest(new BlockBreakQuest(VanillaBlocks::WHEAT(), 300, "Hard Worker", "Harvest 300 wheat", "", [new QuestItemReward(RareCrate::getInstance()->getKeyItem(1), 1)]));
		$this->addNormalQuest(new UniversalQuest(IQuest::ENVOY_CLAIM, 3, "Envoy Seeker", "Open 3 envoys", "", [new QuestItemReward(QuestTokenGeneratorLootbox::getItem(), 1)]));
		$this->addNormalQuest(new KillEntityQuest(EntityIds::SKELETON, 700, "Skeleton Slayer", "Kill 700 skeletons", "", [new QuestItemReward(HeroicQuestTokenGeneratorLootbox::getItem(), 1)]));
		$this->addNormalQuest(new FishQuest(60, "Fisher I", "Fish 60 items", "", [new QuestItemReward(HeroicQuestTokenGeneratorLootbox::getItem(), 1), new QuestItemReward(QuestTokenGeneratorLootbox::getItem(), 2), new QuestItemReward(CustomFishingRod::getItem(4), 1)]));
		$this->addNormalQuest(new BlockBreakQuest(VanillaBlocks::DIAMOND_ORE(), 40, "Diamond Finder", "Break 40 diamond ores", "", [new QuestItemReward(QuestTokenGeneratorLootbox::getItem(), 2), new QuestItemReward(HeroicQuestTokenGeneratorLootbox::getItem(), 1)]));


		$this->addNormalQuest(new KillEntityQuest(EntityIds::PLAYER, 3, "Player Slayer", "Kill 3 players", "", [new QuestItemReward(QuestTokenGeneratorLootbox::getItem())]));
		$this->addNormalQuest(new UniversalQuest(IQuest::JUMP, 1538, "Jumper", "Jump 1,500 times", "", [new QuestItemReward(QuestTokenGeneratorLootbox::getItem(), 2)]));
		$this->addNormalQuest(new UniversalQuest(IQuest::BANK_DEPOSIT, 10000038, "Depositer", "Deposit $10,000,038 to your island", "", [new QuestItemReward(QuestTokenGeneratorLootbox::getItem(), 2), new QuestItemReward(HeroicQuestTokenGeneratorLootbox::getItem())]));
		$this->addNormalQuest(new XpSpendQuest(100000, "XP Spender", "Spend 100,000 XP", "", [new QuestItemReward(SpawnerLootbox::getItem(), 3)]));
		$this->addNormalQuest(new UniversalQuest(IQuest::SNEAK, 750, "Sneakers", "Sneak 750 times", "", [new QuestItemReward(MemoryLaneLootbox::getItem()), new QuestItemReward(QuestTokenGeneratorLootbox::getItem())]));
		$this->addNormalQuest(new BlockBreakQuest(VanillaBlocks::CARROTS(), 2750, "Carrots <3", "Harvest 2,750 carrots", "", [new QuestItemReward(HeroicQuestTokenGeneratorLootbox::getItem()->setCount(3))]));
		$this->addNormalQuest(new CoinflipWinQuest(25, "Gambling addict", "Win 25 coinflips", "", [new QuestItemReward(ArmorSetPieceGeneratorLootbox::getItem(), 3)]));
		$this->addNormalQuest(new ChatQuest("anybananagame is cool", 10, "We love AnyBananaGAME", 'Say "anybananagame is cool" 10 times', "", [new QuestItemReward(QuestTokenGeneratorLootbox::getItem(), 2)]));
		$this->addNormalQuest(new BlockPlaceQuest(VanillaBlocks::AIR(), 5038, "Block Placer", "Place 5,000 blocks", "", [new QuestItemReward(HeroicDestructionEssenceLootbox::getItem(), 2)]));
		$this->addNormalQuest(new EatQuest(VanillaItems::POISONOUS_POTATO(), 3, "Poison yourself :D", "Eat 3 poisonous potatoes", "", [new QuestItemReward(Level40Lootbox::getItem()), new QuestItemReward(AetherCrate::getInstance()->getKeyItem(2), 2)]));

		$this->addNormalQuest(new FishQuest(175, "Amateur Fisher", "Fish 125 times", "", [new QuestItemReward(HeroicQuestTokenGeneratorLootbox::getItem()), new QuestItemReward(QuestTokenGeneratorLootbox::getItem(), 2)]));
		$this->addNormalQuest(new SellQuest(VanillaItems::BLAZE_ROD(), 3838, "Blaze dealer", "Sell 3838 fishing rods", "", [new QuestItemReward(HeroicQuestTokenGeneratorLootbox::getItem()), new QuestItemReward(ArmorSetPieceGeneratorLootbox::getItem(), 3)]));
		//$this->addNormalQuest(new KillEntityQuest(EntityIds::ZOMBIE, 1738, "Zombies, Zombies, ...", "Kill 1738 zombies", "", []));

	}

	public function setupIslandQuests(): void {
	}

	public function increaseProgress(int $type, AetherPlayer $player, Session $session, $object = null): void {
		return; //TODO: check this out

		if(isset($player->quests[$type])){
			/**
			 * @var  $k
			* @var QuestInstance  $instance */
			foreach($player->quests[$type] as $k => $instance){
				if($instance->finished) continue;

				if($instance instanceof DailyQuestInstance){
					if($instance->isExpired()) continue;

					$instance->progress += $instance->quest->shouldIncreaseProgress($object);

					if($instance->progress >= $instance->quest->goal) {
						$this->onQuestDone($instance, $player, $session);
					}

					continue;
				}
			}
		}

		if(($instance = $this->getActiveRegularQuest($player)) !== null){
			if($instance->quest->getType() === $type){
				$instance->progress += ($am = $instance->quest->shouldIncreaseProgress($object));
				$player->questProgress += $am;

				if($instance->progress >= $instance->quest->goal) {
					$this->onQuestDone($instance, $player, $session);
				}
			}
		}

		//TODO: island quest checking
	}

	public function onQuestDone(QuestInstance $instance, AetherPlayer $player, Session $session): void {
		$instance->finished = true;

		foreach($instance->quest->rewards as $reward){
			$reward->give($player);
		}

		$player->sendMessage(Main::PREFIX . "You've completed the §c{$instance->quest->name}§7 quest. The rewards are in your collect (/collect)");


		if($instance instanceof DailyQuestInstance) return;

		$session->getRedis()->incrby("player.{$session->getUsername()}.questIndex", 1);
		$player->currentQuestIndex++;
		$player->questProgress = 0;
	}

	public function getActiveRegularQuest(AetherPlayer $player): ?QuestInstance {
		$quest = $this->getNormalQuest($player->currentQuestIndex);
		if($quest instanceof Quest){
			return new QuestInstance($quest, $player->questProgress, false);
		}


		return null;
	}

	public function checkDailyQuests(AetherPlayer $player, bool $addNew = true): void {
		return; //TODO: check this out

		$count = 0;
		$hasVote = false;

		foreach($player->quests as $id => $list) {
			foreach($list as $key => $quest) {
				if($quest instanceof DailyQuestInstance){
					if($quest->isExpired()){
						unset($player->quests[$id][$key]);
						$player->sendMessage(Main::PREFIX . "Your §c{$quest->quest->name}§7 quest has expired!");
						continue;
					} else $count++;

					if($quest->quest instanceof VoteQuest) $hasVote = true;
				}
			}
		}

		$amount = 7;

		if((new Session($player))->getTopRank()->getTier() >= (new AstronomicalRank())->getTier()){
			$amount = 8;
		}



		if($addNew === true && $count < $amount){
			for($i = 1; $i <= ($amount - $count); $i++){
				if($hasVote === false){
					$hasVote = true;
					$quest = $this->voteQuest ?? null;
					if($quest !== null){
						$this->addNewDailyQuest($player, $quest);
						continue;
					}
				}

				$this->addNewDailyQuest($player);
			}
		}
	}

	public function addNewDailyQuest(AetherPlayer $player, ?Quest $quest = null): void {
		if($quest === null){
			if(empty($this->dailyQuests)) return;

			$quest = $this->dailyQuests[array_rand($this->dailyQuests)];

			if($quest instanceof VoteQuest){
				$this->addNewDailyQuest($player);
				return;
			}
		}

		if($quest instanceof Quest) {
			$player->quests[$quest->getType()][] = new DailyQuestInstance($quest, 0, false, (time() + 86400));
			$player->sendMessage(Main::PREFIX . "You got a new daily quest! (/dailyquests)");
		}
	}




	public function addDailyQuest(Quest $quest): void {
		$this->dailyQuests[$quest->name] = $quest;
	}

	public function addIslandQuest(Quest $quest): void {
		$this->islandQuests[$quest->name] = $quest;
	}

	public function addNormalQuest(Quest $quest): void {
		$this->normalQuests[] = $quest;
	}

	public function getDailyQuest(string $id): ?Quest  {
		return $this->dailyQuests[$id] ?? null;
	}

	public function getNormalQuest(int $index): ?Quest  {
		return $this->normalQuests[$index] ?? null;
	}
}