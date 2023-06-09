<?php

declare(strict_types=1);

namespace skyblock\listeners;

use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\Crops;
use pocketmine\block\Furnace;
use pocketmine\block\Stem;
use pocketmine\block\tile\Chest;
use pocketmine\block\tile\Container;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockFormEvent;
use pocketmine\event\block\BlockGrowEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\LeavesDecayEvent;
use pocketmine\event\entity\EntityBlockChangeEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityExplodeEvent;
use pocketmine\event\entity\EntityItemPickupEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\entity\EntityTrampleFarmlandEvent;
use pocketmine\event\entity\ItemSpawnEvent;
use pocketmine\event\entity\ProjectileHitEntityEvent;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\event\inventory\CraftItemEvent;
use pocketmine\event\inventory\InventoryOpenEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerBucketEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerCreationEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerJumpEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerToggleSneakEvent;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use pocketmine\lang\Translatable;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\particle\BlockBreakParticle;
use pocketmine\world\sound\FizzSound;
use skyblock\blocks\custom\CustomBlock;
use skyblock\blocks\custom\CustomBlockHandler;
use skyblock\blocks\custom\CustomBlockTile;
use skyblock\caches\block\ChunkChestCache;
use skyblock\caches\combat\CombatCache;
use skyblock\caches\playtime\PlayTimeCache;
use skyblock\entity\boss\PveEntity;
use skyblock\entity\object\AetherItemEntity;
use skyblock\entity\object\EnvoyEntity;
use skyblock\events\economy\PlayerExperienceGainEvent;
use skyblock\events\economy\PlayerFarmingExperienceGainEvent;
use skyblock\events\player\PlayerCombatExitEvent;
use skyblock\events\pve\PveAttackPlayerEvent;
use skyblock\islands\Island;
use skyblock\islands\IslandInterface;
use skyblock\islands\upgrades\types\IslandFarmingExpertUpgrade;
use skyblock\islands\upgrades\types\IslandHoppersUpgrade;
use skyblock\islands\upgrades\types\IslandPersonalBoosterUpgrade;
use skyblock\islands\upgrades\types\IslandSpawnersUpgrade;
use skyblock\items\crates\Crate;
use skyblock\items\crates\CrateHandler;
use skyblock\items\crates\types\CommonCrate;
use skyblock\items\crates\types\LegendaryCrate;
use skyblock\items\crates\types\RareCrate;
use skyblock\items\itemmods\types\LuckyJackhammerItemMod;
use skyblock\items\itemmods\types\MutatedSpaceBeltItemMod;
use skyblock\items\itemmods\types\WardensHammerItemMod;
use skyblock\items\lootbox\Lootbox;
use skyblock\items\lootbox\LootboxHandler;
use skyblock\items\lootbox\types\abandoned\CommonAbandonedLootbox;
use skyblock\items\lootbox\types\abandoned\RareAbandonedLootbox;
use skyblock\items\lootbox\types\LuckyBlockLoottableLootbox;
use skyblock\items\misc\HyperFurnace;
use skyblock\items\pets\PetHandler;
use skyblock\items\pets\types\MetalDetectorPet;
use skyblock\items\sets\SpecialSetHandler;
use skyblock\items\sets\types\GucciSpecialSet;
use skyblock\items\SkyblockItem;
use skyblock\items\special\SpecialItem;
use skyblock\items\special\SpecialItemHandler;
use skyblock\items\special\types\ChunkChestItem;
use skyblock\items\special\types\IntergalacticWeirdCoinItem;
use skyblock\items\special\types\LuckyBlock;
use skyblock\logs\LogHandler;
use skyblock\logs\types\DeathLog;
use skyblock\Main;
use skyblock\menus\common\ViewGalaxyCrateMenu;
use skyblock\menus\recipe\CraftingMenu;
use skyblock\misc\arena\ArenaManager;
use skyblock\misc\booster\BoosterHandler;
use skyblock\misc\koth\Koth;
use skyblock\misc\quests\IQuest;
use skyblock\misc\quests\QuestHandler;
use skyblock\misc\skills\FarmingSkill;
use skyblock\misc\synchroniser\PlayerListSynchroniser;
use skyblock\player\AetherPlayer;
use skyblock\sessions\Session;
use skyblock\tiles\CropTile;
use skyblock\tiles\CustomChestTile;
use skyblock\tiles\HyperFurnaceTile;
use skyblock\tiles\LuckyBlockTile;
use skyblock\traits\AwaitStdTrait;
use skyblock\traits\PlayerCooldownTrait;
use skyblock\utils\IslandUtils;
use skyblock\utils\ScoreboardUtils;
use skyblock\utils\Utils;
use slapper\entities\SlapperEntity;
use SOFe\AwaitGenerator\Await;

//fuck island listener keep everything in one place
class EventListener implements Listener {
    use PlayerCooldownTrait;

    //enderpearl cooldown

    use AwaitStdTrait;

    private array $materialCooldown = [];
	private array $menuCd = [];
	private array $commandCooldown = [];


    public const GEN_BLOCKS =
        [
            BlockLegacyIds::COBBLESTONE, BlockLegacyIds::COBBLESTONE, BlockLegacyIds::COBBLESTONE, BlockLegacyIds::COBBLESTONE, BlockLegacyIds::COBBLESTONE, BlockLegacyIds::COBBLESTONE,
            BlockLegacyIds::COBBLESTONE, BlockLegacyIds::COBBLESTONE, BlockLegacyIds::COBBLESTONE, BlockLegacyIds::COBBLESTONE, BlockLegacyIds::COBBLESTONE, BlockLegacyIds::COBBLESTONE,
            BlockLegacyIds::COBBLESTONE, BlockLegacyIds::COBBLESTONE, BlockLegacyIds::COBBLESTONE, BlockLegacyIds::COBBLESTONE, BlockLegacyIds::COBBLESTONE, BlockLegacyIds::COBBLESTONE,
            BlockLegacyIds::COBBLESTONE, BlockLegacyIds::COBBLESTONE, BlockLegacyIds::COBBLESTONE, BlockLegacyIds::COBBLESTONE, BlockLegacyIds::COBBLESTONE, BlockLegacyIds::COBBLESTONE,
            BlockLegacyIds::IRON_ORE,
            BlockLegacyIds::GOLD_ORE,
            BlockLegacyIds::COAL_ORE,
            BlockLegacyIds::EMERALD_ORE,
            BlockLegacyIds::DIAMOND_ORE,
            BlockLegacyIds::REDSTONE_ORE,
            BlockLegacyIds::LAPIS_ORE
        ];

    public const ORES = [
        BlockLegacyIds::EMERALD_ORE,
        BlockLegacyIds::COAL_ORE,
        BlockLegacyIds::REDSTONE_ORE,
        BlockLegacyIds::LAPIS_ORE,
        BlockLegacyIds::DIAMOND_ORE,
        BlockLegacyIds::GOLD_ORE,
        BlockLegacyIds::IRON_ORE,
        BlockLegacyIds::LIT_REDSTONE_ORE
    ];

    public static EventListener $instance;


    public function __construct(protected Main $plugin) {
        self::$instance = $this;
    }

	public function onDecay(LeavesDecayEvent $event): void {
		if(!Utils::isIslandServer()){
			$event->cancel();
		}
	}


    public function onJoin(PlayerJoinEvent $event) : void {
        $player = $event->getPlayer();
        $event->setJoinMessage("");//done on the proxy

        ScoreboardUtils::setStartingScoreboard($player, true);
        BoosterHandler::getInstance()->check($player, new Session($player));
		PetHandler::getInstance()->updatePets($player);


        new PlayerListSynchroniser($player);

		Utils::checkForAHDupedItems($player);
    }

	public function onCommandProcess(PlayerCommandPreprocessEvent $event): void {
		$player = $event->getPlayer();

		if(isset($this->commandCooldown[$player->getName()]) && !Server::getInstance()->isOp($player->getName())){
			if(time() - $this->commandCooldown[$player->getName()] <= 1){
				$player->sendMessage(Main::PREFIX . "You're sending messages to fast, please slow down");
				$event->cancel();
				return;
			}
		}

		$this->commandCooldown[$player->getName()] = time();
	}

	public function onHold(PlayerItemHeldEvent $event): void {
		$item = $event->getItem();
		$player = $event->getPlayer();


	}

    public function onItemDrop(PlayerDropItemEvent $event) : void {
        $player = $event->getPlayer();
        $world = $player->getPosition()->getWorld();

		if(Utils::checkForAHDupedItems($player)){
			$event->cancel();
			return;
		}

        if (IslandUtils::isIslandWorld($world) && $player->isCreative() === false) {
            $session = new Session($player);

            $island = IslandUtils::getIslandByWorld($world);
            if ($session->getIslandName() !== $island->getName()) {
                if ($island->getSetting(IslandInterface::SETTINGS_DROP_ITEM) === false) {
                    $event->cancel();
                    $player->sendMessage(Main::PREFIX . "You cannot drop items on this island");
                }
            }
        }

    }

    public function onQuit(PlayerQuitEvent $event) : void {
        $player = $event->getPlayer();

        if (CombatCache::getInstance()->isInCombat($player) && $event->getQuitReason() !== "Internal Server Error") {
            if ($event->getQuitMessage() instanceof Translatable) {
                $str = $event->getQuitMessage()->getText();
            } else $str = $event->getQuitMessage();

            if (strtolower($str) !== "server restart") {
                $player->kill();
            }
        }

        CombatCache::getInstance()->removeFromCombat($player);
        PlayTimeCache::getInstance()->remove($player->getName());
		PetHandler::getInstance()->updatePets($player, true);


		$ip = $player->getNetworkSession()->getIp();
        Utils::executeLater(function () use ($ip) : void {
            Server::getInstance()->getNetwork()->unblockAddress($ip);
        }, 1);
    }

    public function onCraft(CraftItemEvent $event) : void {
        $player = $event->getPlayer();

        $outputs = $event->getOutputs();

        foreach ($outputs as $output) {
            if ($output->getId() === ItemIds::FISHING_ROD) {
                $player->sendMessage(Main::PREFIX . "If the crafted fishing rod does not work, move it around you inventory. That'll fix it.");
            }
        }
    }


    public function onConsume(PlayerItemConsumeEvent $event) : void {
        $player = $event->getPlayer();
        $item = $event->getItem();

        if ($item->getId() === ItemIds::GOLDEN_APPLE) {
            if ($this->isOnCooldownByName('golden_apple', $player)) {
                $player->sendMessage(Main::PREFIX . "GoldenApples are on cooldown for §c" . ($this->getCooldownByName('golden_apple', $player)) . "s");
                $event->cancel();
                return;
            }

            $this->setCooldownByName('golden_apple', $player, 10);
        }

        QuestHandler::getInstance()->increaseProgress(IQuest::EAT, $player, new Session($player), $item);
    }

    /**
     * @param PlayerInteractEvent $event
     * @priority LOWEST
     */
    public function onInteract(PlayerInteractEvent $event) : void {
        $player = $event->getPlayer();
        $block = $event->getBlock();
        $world = $player->getWorld();
        $item = $event->getItem();
        $session = new Session($player);

        if ($player->isCreative() === false && $block->getId() === BlockLegacyIds::ITEM_FRAME_BLOCK) {
            $player->sendMessage(Main::PREFIX . "Item frames are disabled");
            return;
        }

        $pos = $block->getPosition();
        if (IslandUtils::isIslandWorld($world) && $player->isCreative() === false) {
            $island = IslandUtils::getIslandByWorld($world);
            if ($session->getIslandName() === $island->getName()) {
                if ($island->isMember($player->getName()) || $island->isLeader($player)) {
					$tile = $world->getTile($pos);
                    if ($tile instanceof Container && $island->hasPermission($player, IslandInterface::PERMISSION_OPEN_CONTAINERS) === false) {
                        $player->sendMessage(Main::PREFIX . "§7You have no permissions to §copen containers§7.");
                        $event->cancel();
                        return;
                    }

					if($tile instanceof CustomBlockTile){
						$tile->getSpecialBlock()?->onInteract($player, $tile, $event);
					}
                } elseif (!$player->isCreative()) {
                    $event->cancel();
                    return;
                }
            } elseif (!$player->isCreative()) {
                $event->cancel();
                return;
            }
        }

        if (!IslandUtils::isIslandWorld($world) && $player->isSurvival()) {
            if ($item->getId() === ItemIds::FLINT_AND_STEEL || $item->getId() === ItemIds::PAINTING) {
                $event->cancel();
                return;
            }
        }

		if($block->getId() === BlockLegacyIds::CRAFTING_TABLE && $event->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK){
			$event->cancel();
			if(!$this->isOnCooldownByName("crafting_table", $player)){
				$this->setCooldownByName("crafting_table", $player, 1);
				(new CraftingMenu($player))->send($player);
			}

			return;
		}

        if ($block->getId() === BlockLegacyIds::CHEST) {
			if(Utils::checkForAHDupedItems($player)){
				$event->cancel();
				return;
			}

            if (($tile = $block->getPosition()->getWorld()->getTile($block->getPosition()))) {
                if ($tile instanceof Chest) {
                    if (strpos($tile->getName(), "Envoy") !== false) {
                        $tile->getPosition()->getWorld()->addParticle($tile->getPosition(), new BlockBreakParticle(VanillaBlocks::DIAMOND()));
                        foreach ($tile->getInventory()->getContents() as $content) {
                            Utils::addItem($player, $content);
                        }
                        $block->getPosition()->getWorld()->setBlock($block->getPosition(), VanillaBlocks::AIR());
                        QuestHandler::getInstance()->increaseProgress(IQuest::ENVOY_CLAIM, $player, $session);
                        return;
                    }
                }
            }
        }

        if (($string = $item->getNamedTag()->getString(SpecialItem::TAG_SPECIAL_ITEM, "")) !== "") {
			if(Utils::checkForAHDupedItems($player)){
				$event->cancel();
				return;
			}

            SpecialItemHandler::call($string, $player, $event, $item);
        }

        if (($string = $item->getNamedTag()->getString("lootbox", "")) !== "") {
			if(Utils::checkForAHDupedItems($player)){
				$event->cancel();
				return;
			}

            $event->cancel();
            $lootbox = LootboxHandler::getInstance()->getLootbox($string);
            if ($lootbox instanceof Lootbox) {
                $lootbox->open($player);
                $item->pop();
                $player->getInventory()->setItemInHand($item);
            }
        }

        if ($item->getId() === ItemIds::SPLASH_POTION) {
            $player->setHealth($player->getHealth() + 2);
            $event->cancel();
            $slot = $player->getInventory()->getHeldItemIndex();

            Utils::executeLater(function () use ($player, $slot) : void {
                if ($player->isOnline()) {
                    $player->getInventory()->clear($slot);
                }
            }, 1);
            return;
        }

        if ($block->getId() === BlockLegacyIds::BEACON && $block->getPosition()->getWorld()->getDisplayName() === "spawn") {
			if(Utils::checkForAHDupedItems($player)){
				$event->cancel();
				return;
			}

            if (($crate = CrateHandler::getInstance()->getCrate($item->getNamedTag()->getString(Crate::TAG_GALAXY_KEY, ""))) !== null) {
				if($event->getAction() === PlayerInteractEvent::LEFT_CLICK_BLOCK){
					if(isset($this->menuCd[$player->getName()])){
						if(time() - $this->menuCd[$player->getName()] <= 1){
							$event->cancel();
							return;
						}
					}

					$this->menuCd[$player->getName()] = time();

					(new ViewGalaxyCrateMenu($crate))->send($player);


					return;
				}

                $crate->open($player, $item);
                $event->cancel();
            }
        }
	}

    /**
     * @param ItemSpawnEvent $event
     * @priority HIGH
     */
    public function onItemEntitySpawn(ItemSpawnEvent $event) : void {
        $e = $event->getEntity();
		$e->setScale(2);

		if(!$e instanceof AetherItemEntity){
			$entity = $event->getEntity();

			$entity->flagForDespawn();

			Utils::executeLater(function() use ($entity){
				$e = new AetherItemEntity($entity->getLocation(), $entity->getItem());
				$e->setMotion($entity->getMotion());
				$e->spawnToAll();
				}, 1);

			return;
		}

        if ($e->isFlaggedForDespawn()) return;
        if ($e->isClosed()) return;

        $identifier = CustomChestTile::getCacheIdentifier($e->getPosition());

        if (($arr = ChunkChestCache::getInstance()->get($identifier))) {
            /** @var CustomChestTile $tile */
            foreach ($arr as $tile) {
                if ($tile->isClosed()) continue;

                if ($tile->getInventory()->canAddItem($e->getItem())) {
                    $tile->getInventory()->addItem($e->getItem());
                    $e->flagForDespawn();
                    break;
                }
            }
        }
    }

    public function onTeleport(EntityTeleportEvent $event) : void {
        $from = $event->getFrom();
        $to = $event->getTo();
        $player = $event->getEntity();

        if (!$player instanceof Player) return;

        $fromWorld = $from->getWorld()->getDisplayName();
        $toWorld = $to->getWorld()->getDisplayName();

        if (str_contains($fromWorld, "a-")) {
            if (($arena = ArenaManager::getInstance()->getArenaByWorld($from->getWorld()))) {
                $enter = $arena->onWorldExit;
                $enter($event->getEntity(), $arena);
            }
        }

        if (str_contains($toWorld, "a-")) {
            if (($arena = ArenaManager::getInstance()->getArenaByWorld($to->getWorld()))) {
                $enter = $arena->onWorldEnter;
                $enter($event->getEntity(), $arena);
            }
        }

        if ($fromWorld === "Koth" && $toWorld !== "Koth") {
            //exists out of koth
            ScoreboardUtils::clearCache($player);
            ScoreboardUtils::setStartingScoreboard($player);
        }


        if ($fromWorld === "pvp" && $toWorld !== "pvp") {
            //exists out of koth
            ScoreboardUtils::clearCache($player);
            ScoreboardUtils::setStartingScoreboard($player);
        }




		Utils::checkForAHDupedItems($player);
    }

    /**
     * @param ProjectileHitEvent $event
     * @priority LOW
     */
    public function onProjectileHit(ProjectileHitEvent $event) : void {
        $projectile = $event->getEntity();

        if ($projectile instanceof \pocketmine\entity\projectile\EnderPearl) {
            $owning = $projectile->getOwningEntity();
            if (!$owning instanceof Player) return;

            $world = $projectile->getWorld();

            $arrayMaxY = [
                "pvp"  => 90,
                "koth" => 100
            ];

            if (isset($arrayMaxY[strtolower($world->getDisplayName())]) && $event->getRayTraceResult()->getHitVector()->getY() >= $arrayMaxY[strtolower($world->getDisplayName())]) {
                $projectile->setOwningEntity(null);

                $this->removeCooldown($owning);
                $owning->sendMessage(Main::PREFIX . "Your ender pearl cooldown has been reset.");
            }
            $owning->despawnFromAll();

            /** @var AetherPlayer $owning */
            Utils::executeLater(function () use ($owning, $event) {

                if ($owning->isOnline()) {
                    $owning->spawnToAll();
                }
            }, 1);
        }
    }

    public function onItemUse(PlayerItemUseEvent $event) : void {
        $player = $event->getPlayer();
        $item = $event->getItem();

        if (($string = $item->getNamedTag()->getString(SpecialItem::TAG_SPECIAL_ITEM, "")) !== "") {
            SpecialItemHandler::call($string, $player, $event, $item);
        }

        if (($string = $item->getNamedTag()->getString("lootbox", "")) !== "") {
            $event->cancel();
            $lootbox = LootboxHandler::getInstance()->getLootbox($string);
            if ($lootbox instanceof Lootbox) {
                $lootbox->open($player);
                $item->pop();
                $player->getInventory()->setItemInHand($item);
            }
        }

        if ($item->getId() === ItemIds::ENDER_PEARL) {
            if ($this->isOnCooldown($player)) {
                $event->cancel();
                $player->sendMessage(Main::PREFIX . "Enderpearl is on cooldown for §c" . ($this->getCooldown($player)) . "s");
                return;
            }

            $this->setCooldown($player, 20);
        }
    }

	/**
	 * @param ProjectileHitEntityEvent $event
	 * @ignoreCancelled false
	 * @priority HIGH
	 * @return void
	 */
	public function projectileHit(ProjectileHitEntityEvent $event): void {

		$arrow = $event->getEntity();
		$p = $event->getEntityHit();
		$pve = $arrow->getOwningEntity();

		if($p instanceof AetherPlayer && $pve instanceof PveEntity){
			(new PveAttackPlayerEvent($pve, $p, $pve->damage))->call();
		}
	}

    /**
     * @param PlayerDeathEvent $event
     * @priority HIGHEST
     */
    public function onPlayerDeath(PlayerDeathEvent $event) : void {
        $player = $event->getPlayer();
        $session = new Session($player);
        $session->setDeaths($session->getDeaths() + 1);
        $xp = $event->getXpDropAmount();

        $drops = $event->getDrops();
        $lastDmg = $player->getLastDamageCause();
        $damager = null;
        CombatCache::getInstance()->removeFromCombat($player);

        $sent = false;
        if ($lastDmg instanceof EntityDamageByEntityEvent) {
            $damager = $lastDmg->getDamager();
            if ($damager instanceof Player) {
                QuestHandler::getInstance()->increaseProgress(IQuest::KILL_ENTITY, $damager, new Session($damager), $player::getNetworkTypeId());


				$item = $damager->getInventory()->getItemInHand();


                $log = new DeathLog($player, "got killed by {$damager->getName()}", $damager->getName());
                $log->extraData = [
                    "player"          => $player->getName(),
                    "killer"          => $damager->getName(),
                    "unix"            => time(),
                    "item"            => json_encode($damager->getInventory()->getItemInHand()),
                    "itemsLostString" => json_encode(array_merge($player->getInventory()->getContents(), $player->getArmorInventory()->getContents())),
                    "world"           => $player->getWorld()->getFolderName(),
                    "x"               => $player->getPosition()->getFloorX(),
                    "y"               => $player->getPosition()->getFloorY(),
                    "z"               => $player->getPosition()->getFloorZ(),
                ];

                LogHandler::getInstance()->log($log);
                $sent = true;
				
				Await::f2c(function() use($player, $damager) {
					//TODO: bounty
					yield $this->getStd()->sleep(1);
				});
            }
        }





        if (!$sent) {
            $msg = ($event->getDeathMessage() instanceof Translatable ? $event->getDeathMessage()->getText() : $event->getDeathMessage());
            $log = new DeathLog($player, $msg);

            $log->extraData = [
                "player"          => $player->getName(),
                "killer"          => $msg,
                "unix"            => time(),
                "item"            => json_encode(VanillaItems::AIR()),
                "itemsLostString" => json_encode(array_merge($player->getInventory()->getContents(), $player->getArmorInventory()->getContents())),
                "world"           => $player->getWorld()->getFolderName(),
                "x"               => $player->getPosition()->getFloorX(),
                "y"               => $player->getPosition()->getFloorY(),
                "z"               => $player->getPosition()->getFloorZ(),
            ];

            LogHandler::getInstance()->log($log);
        }


        if ($damager instanceof Player) {

            $s = new Session($damager);
            $s->setKills($s->getKills() + 1);
        }

        shuffle($drops);



        $event->setDrops($drops);
        $event->setXpDropAmount(0);


        if ($damager instanceof Player && $damager->isOnline()) {
            foreach ($drops as $drop) {
                Utils::addItem($damager, $drop, true);
            }

            $event->setDrops([]);
        }
    }

    /**
     * @param PlayerCombatExitEvent $event
     * @priority NORMAL
     *
    public function onLeaveCombat(PlayerCombatExitEvent $event) : void {
        $player = $event->getPlayer();
        Await::f2c(function() use($player) {
            if(!$player->hasPermission("skyblock.command.fly")){
                $voted = yield Voting::getInstance()->hasVoted($player->getName(), false);
                if (!$player instanceof Player) {
                    return;
                }

                if($voted === false){
                    return;
                }
            }

            if ($player->isOnline() && $player->hasPermission("skyblock.fly.auto")) {
                $player->setAllowFlight(true);
            }
        });
    }*/

    /**
     * @param EntityDamageByEntityEvent $event
     * @priority LOW
     */
    public function onEntityDamageByEntityEvent(EntityDamageByEntityEvent $event) : void {
        $event->uncancel();
        $entity = $event->getEntity();
        $damager = $event->getDamager();
        $world = $event->getEntity()->getWorld();

		if($entity instanceof SlapperEntity){
			$event->cancel();
			return;
		}

        if ($damager instanceof Player) {
            if (IslandUtils::isIslandWorld($world) && $damager->isCreative() === false) {
                $is = IslandUtils::getIslandByWorld($world);
                if (!$is->isLeader($damager)) {
                    if ($is->isMember($damager)) {
                        if ($is->hasPermission($damager, IslandInterface::PERMISSION_KILL_MOBS) === false) {
                            $damager->sendMessage(Main::PREFIX . "§7You have no permissions to §ckill entities§7.");
                            $event->cancel();
                            return;
                        }
                    } elseif ($is->getSetting(IslandInterface::SETTINGS_KILL_MOBS) === false) {
                        $event->cancel();
                        $damager->sendMessage(Main::PREFIX . "You cannot kill mobs here");
                        return;
                    }
                }
            }

            if ($entity instanceof Player) {
                $session = new Session($damager);
                $island = $session->getIslandOrNull();
                if ($island !== null && $island->isMemberOrLeader($entity->getName())) {
                    $event->cancel();
                    return;
                }

                CombatCache::getInstance()->setInCombat($damager);
                CombatCache::getInstance()->setInCombat($entity);
                if ($damager->isSurvival() || $damager->isAdventure()) {
                    $damager->setFlying(false);
                    $damager->setAllowFlight(false);
                }

                if ($entity->isSurvival() || $entity->isAdventure()) {
                    $entity->setFlying(false);
                    $entity->setAllowFlight(false);
                }
            }
        }
    }

    /**
     * @param EntityTrampleFarmlandEvent $event
     * @priority NORMAL
     */
    public function onEntityTrample(EntityTrampleFarmlandEvent $event) : void {
        $event->cancel();
    }

    public function onEntityExplode(EntityExplodeEvent $event) : void {
        $event->cancel();
    }

    /**
     * @param EntityItemPickupEvent $event
     * @priority HIGH
     * TODO: autosell code here but it is not a feature in the update season
    public function onItemPickup(EntityItemPickupEvent $event) {
        $player = $event->getEntity();

        if ($player instanceof AetherPlayer) {
            if ($player->autosell) {
                Utils::executeLater(function () use ($player) {
                    if ($player->isOnline()) {
                        $gain = Shop::getInstance()->sellInventory($player, $player->getInventory());
                        if ($gain > 0) {
                            $player->sendTip("§a+$" . number_format($gain));
                        }
                    }
                }, 1);
            }
        }
    }*/

    /**
     * @param BlockBreakEvent $event
     * @priority LOWEST
     */
    public function onBlockBreak(BlockBreakEvent $event) : void {
        /** @var AetherPlayer $player */
        $player = $event->getPlayer();
        $block = $event->getBlock();
        $world = $event->getPlayer()->getWorld();
        $session = new Session($player);

        if (IslandUtils::isIslandWorld($world) && $player->isCreative() === false) {
            $island = IslandUtils::getIslandByWorld($world);
            if ($session->getIslandName() === $island->getName()) {
                if ($island->isMember($player->getName())) {
                    if ($block->getId() === BlockLegacyIds::MOB_SPAWNER && $island->hasPermission($player, IslandInterface::PERMISSION_BREAK_SPAWNERS) === false) {
                        $player->sendMessage(Main::PREFIX . "§7You have no permissions to §cbreak spawners§7.");
                        $event->cancel();
                        return;
                    }

                    if ($island->hasPermission($player, IslandInterface::PERMISSION_BREAK_BLOCKS) === false) {
                        $player->sendMessage(Main::PREFIX . "§7You have no permissions to §cbreak blocks§7.");
                        $event->cancel();
                        return;
                    }
                }


				if(FarmingSkill::isCropBlock($block->getId(), $block->getMeta())){
					$session->increaseForgeData((string) $block->getId());
				}


                if ($block instanceof Crops && ($level = ($island->getIslandUpgrade(IslandFarmingExpertUpgrade::getIdentifier()))) > 0) {
                    if (mt_rand(1, 100) <= (5 * $level)) {
                        $event->cancel();
                        $player->sendTip("§aIsland Farming Expert");
                        foreach ($event->getDrops() as $drop) {
                            Utils::addItem($player, $drop);
                            return;
                        }
                    }
                }

                if ($block instanceof Stem) {
                    if (mt_rand(1, 8) <= 7) {
                        $event->setDrops([]);
                    }
                }

                if ($block->getId() === BlockLegacyIds::HOPPER_BLOCK) {
                    $island->decreaseLimit(IslandInterface::LIMIT_HOPPER);
                }


                /*if ($block->getId() === BlockLegacyIds::CHEST) {
                    if (($tile = $world->getTile($block->getPosition()))) {
                        if ($tile instanceof CustomChestTile && $tile->isChunkChest()) {
                            $arr = [];
                            $added = false;
                            foreach ($event->getDrops() as $drop) {
                                if (!$added && $drop->getId() === ItemIds::CHEST) {
                                    $drop->pop();
                                    $added = true;
                                    $arr[] = ChunkChestItem::getItem();
                                }

                                if (!$drop->isNull()) {
                                    $arr[] = $drop;
                                }
                            }

                            $event->setDrops($arr);
                        }
                    }
                }*/

				if($block->getId() === BlockLegacyIds::BREWING_STAND_BLOCK){
					$tile = $world->getTile($block->getPosition());

					if($tile instanceof CustomBlockTile){
						$type = $tile->getSpecialBlock();

						$result = $type->onBreak($player, $tile);

						if($result === false){
							$event->cancel();
							return;
						} else $event->setDrops([$type::getItem()]);
					}
				}

                QuestHandler::getInstance()->increaseProgress(IQuest::MINE_BLOCK, $player, $session, $block);

                return;
            } else $event->cancel();
        }

        if (!$event->isCancelled()) {
            if (!$player->isCreative()) {
                $event->cancel();
            }
        }
    }

    /**
     * @param BlockBreakEvent $event
     * @priority HIGHEST
     *
    public function onParentBlockBreak(BlockBreakEvent $event) : void {
        if ($event->isCancelled()) return;
        $player = $event->getPlayer();
        foreach ($event->getDrops() as $drop) {
            Utils::addItem($player, $drop);
        }


		var_dump("drops;", $event->getDrops());
        $player->getXpManager()->addXp((int)$event->getXpDropAmount(), true, true);
        $event->setDrops([]);
        $event->setXpDropAmount(0);
    }*/

    public function onBucketPlace(PlayerBucketEvent $event) : void {
        $player = $event->getPlayer();
        $world = $event->getPlayer()->getWorld();
        $session = new Session($player);

        if (IslandUtils::isIslandWorld($world) && $player->isCreative() === false) {
            $island = IslandUtils::getIslandByWorld($world);
            if ($session->getIslandName() === $island->getName()) {
                return;
            }
        }

        if (!$player->isCreative()) {
            $event->cancel();
        }
    }

    public function onBlockChange(EntityBlockChangeEvent $event) : void {
        $e = $event->getEntity();

        if ($e instanceof EnvoyEntity) {
            $block = $event->getBlock();

            Utils::executeLater(function () use ($block, $e) : void {
                $tile = $block->getPosition()->getWorld()->getTile($block->getPosition());
                if ($tile instanceof Chest) {
                    $tile->setName("Envoy Chest NOT DONE");

                    /*$count = mt_rand(0, 1);

                    $crate = match ($e->getRarity()) {
                        ICustomEnchant::RARITY_UNCOMMON => CommonCrate::getInstance(),
                        ICustomEnchant::RARITY_RARE => RareCrate::getInstance(),
                        ICustomEnchant::RARITY_LEGENDARY => LegendaryCrate::getInstance(),
                        default => CommonCrate::getInstance()
                    };

                    for ($i = 0; $i <= $count; $i++) {
                        $tile->getInventory()->addItem($crate->getRandomReward());
                    }*/
                }
            }, 1);
        }
    }

    /**
     * @param BlockPlaceEvent $event
     * @priority LOWEST
     */
    public function onBlockPlace(BlockPlaceEvent $event) : void {
        $player = $event->getPlayer();
        $block = $event->getBlock();
        $item = $event->getItem();
        $world = $event->getPlayer()->getWorld();
        $session = new Session($player);

        if (!$player instanceof AetherPlayer) {
            return;
        }

        if ($player->isCreative() === false && $block->getId() === BlockLegacyIds::ITEM_FRAME_BLOCK) {
            $player->sendMessage(Main::PREFIX . "Item frames are disabled");
            return;
        }

		//hyper furnace start
		if($block instanceof Furnace){
			if($item instanceof HyperFurnace){
				Utils::executeLater(function() use($block) {
					$tile = $block->getPosition()->getWorld()->getTile($block->getPosition());
					if($tile instanceof \pocketmine\block\tile\Furnace){
						$tile->close();
						$block->getPosition()->getWorld()->addTile($s = new HyperFurnaceTile($block->getPosition()->getWorld(), $block->getPosition()));
						$s->isHyperFurnace = true;

						var_dump("adds new tile");
						var_dump("closes tile");
					}
				}, 1);
			}
		}
		//hyper furnace end

        if (IslandUtils::isIslandWorld($world) && $player->isCreative() === false) {
            $island = IslandUtils::getIslandByWorld($world);
            if ($session->getIslandName() === $island->getName()) {
                if (!$island->getBoundingBox()->isVectorInside($block->getPosition())) {
                    $player->sendMessage(Main::PREFIX . "Cannot place blocks outside of the island size limit");
                    $event->cancel();
                    return;
                }

				if(Utils::checkForAHDupedItems($player)){
					$event->cancel();
					return;
				}

                if ($island->isMember($player->getName())) {
                    if ($block->getId() === BlockLegacyIds::MOB_SPAWNER && $island->hasPermission($player, IslandInterface::PERMISSION_PLACE_SPAWNERS) === false) {
                        $player->sendMessage(Main::PREFIX . "§7You have no permissions to §cplace spawners§7.");
                        $event->cancel();
                        return;
                    }

                    if ($island->hasPermission($player, IslandInterface::PERMISSION_PLACE_BLOCKS) === false) {
                        $player->sendMessage(Main::PREFIX . "§7You have no permissions to §cplace blocks§7.");
                        $event->cancel();
                        return;
                    }
                }

                if ($block->getId() === BlockLegacyIds::HOPPER_BLOCK) {
                    $hopperCount = $island->getLimit(IslandInterface::LIMIT_HOPPER);
                    $maxCount = 20 + ($island->getIslandUpgrade(IslandHoppersUpgrade::getIdentifier()) * 20);

                    if ($hopperCount >= $maxCount) {
                        $player->sendMessage(Main::PREFIX . "You cannot place more hoppers on this island. (§c{$hopperCount}§7/§c{$maxCount}§7)");
                        $event->cancel();
                        return;
                    }

                    $island->increaseLimit(IslandInterface::LIMIT_HOPPER);
                }


                if ($block->getId() === BlockLegacyIds::MOB_SPAWNER) {
                    $hopperCount = $island->getLimit(IslandInterface::LIMIT_SPAWNER);
                    $maxCount = 25 + ($island->getIslandUpgrade(IslandSpawnersUpgrade::getIdentifier()) * 25);

                    if ($hopperCount >= ($maxCount)) {
                        $player->sendMessage(Main::PREFIX . "You cannot place more spawners on this island. (§c{$hopperCount}§7/§c{$maxCount}§7)");
                        $event->cancel();
                        return;
                    }

                    $island->increaseLimit(IslandInterface::LIMIT_SPAWNER);
                    $id = $item->getNamedTag()->getString("entityID", "");
                    if ($id !== "") {
                        $island?->increaseValue(IslandUtils::getValueBySpawner($id));
                    }
                }

                if ($block->getId() === BlockLegacyIds::MELON_BLOCK || $block->getId() === BlockLegacyIds::PUMPKIN) {
                    Await::f2c(function () use ($block) {
                        yield $this->getStd()->sleep(2);
                        $tile = $block->getPosition()->getWorld()->getTile($block->getPosition());
                        if ($tile instanceof CropTile) {
                            $tile->setPlaced(true);
                        }
                    });
                }

                if ($block->getId() === BlockLegacyIds::CHEST && $item->getNamedTag()->getString(SpecialItem::TAG_SPECIAL_ITEM, "") === ChunkChestItem::getItemTag()) {
                    if (!empty(ChunkChestCache::getInstance()->get(CustomChestTile::getCacheIdentifier($event->getBlockReplaced()->getPosition())))) {
                        $player->sendMessage(Main::PREFIX . "There's already a chunk chest in this chunk.");
                        $event->cancel();
                        return;
                    }

                    Await::f2c(function () use ($event, $player, $block, $world) {
                        yield $this->getStd()->sleep(2);
                        $tile = $world->getTile($event->getBlockReplaced()->getPosition());
                        if ($tile instanceof CustomChestTile) {
                            $tile->setIsChunkChest(true);
                        }
                    });
                }

				if($block->getId() === BlockLegacyIds::BREWING_STAND_BLOCK){ //custom blocks
					$type = CustomBlockHandler::getInstance()->getBlock($item->getNamedTag()->getString(CustomBlock::TAG_CUSTOM_BLOCK, ""));

					if($type instanceof CustomBlock){
						$result = $type->onPlace($player, $event->getBlock()->getPosition());

						if($result === false) {
							$event->cancel();
							return;
						}

						Utils::executeLater(function() use ($player, $event, $block, $item, $type) {


							$world = $player->getPosition()->getWorld();
							$pos = $event->getBlock()->getPosition();
							$chunk = $pos->getWorld()->getChunk($pos->x >> 4, $pos->z >> 4);

							$current = $world->getTile($pos);
							if($current !== null){
								$current->close();
							}


							$tile = new CustomBlockTile($world, $pos->floor());
							$tile->setPlacer($player->getName());
							$tile->setSpecialId($type::getIdentifier());
							$chunk->addTile($tile);

							$type->load($tile);
						}, 1);
					}
				}

				if(SpecialItem::getSpecialItem($item) instanceof LuckyBlock){
					Utils::executeLater(function() use ($world, $block, $island, $player, $item) {
						$pos = $block->getPosition();
						$world->addTile(new LuckyBlockTile($world, $pos));
						$island->announce(Main::PREFIX . "§c{$player->getName()}§7 has placed a {$item->getName()} §r§7at x: §c{$pos->getFloorX()}§7, y: §c{$pos->getFloorY()}§7, z: §c{$pos->getFloorZ()}");
					}, 1);
				}

                QuestHandler::getInstance()->increaseProgress(IQuest::PLACE_BLOCK, $player, $session, $block);

                return;
            } else $event->cancel();
        }


        if (!$event->isCancelled()) {
            if (!$player->isCreative()) {
                $event->cancel();
            }
        }
    }

    /**
     * @param PlayerFarmingExperienceGainEvent $event
     * @priority HIGHEST
     */
    public function onFarmingXp(PlayerFarmingExperienceGainEvent $event) : void {
        $player = $event->getPlayer();
        $session = new Session($player);

        if (($booster = $session->getFarmingXpBooster())) {
            $event->addGain((int)($event->getGain() * ($booster->getBoost() - 1)), "farming xp booster");
        }

        if (($island = $session->getIslandOrNull()) !== null) {
            if (($level = $island->getIslandUpgrade(IslandPersonalBoosterUpgrade::getIdentifier())) > 0) {
                $event->addGain((int)($event->getGain() * 0.5), "Island Personal Booster Upgrade");
            }
        }

        $item = null;
        if (mt_rand(1, 30000) === 38) {
            $item = CommonAbandonedLootbox::getItem();
        }

        if (mt_rand(1, 50000) === 38) {
            $item = RareAbandonedLootbox::getItem();
        }

        if ($item instanceof Item) {
            Utils::addItem($player, $item);
            $msg = [
                "§r ",
                "§r§a§l{$player->getName()} §r§7just found a {$item->getCustomName()} §r§7from farming! ",
                "§r "
            ];

            Utils::announce(implode("\n", $msg));
        }
    }

    /**
     * @param PlayerExperienceGainEvent $event
     * @priority HIGHEST
     */
    public function onXpGain(PlayerExperienceGainEvent $event) : void {
        $player = $event->getPlayer();
        $session = new Session($player);

        if (($name = $session->getIslandName())) {
            $island = new Island($name);

            if (($booster = $island->getXpBooster())) {
                $event->addBoost($booster->getBoost() - 1);
            }
        }

        if (($booster = $session->getXpBooster())) {
            $event->addBoost($booster->getBoost() - 1);
        }
    }

	public function onGrow(BlockGrowEvent $event): void {
		CustomBlockHandler::attemptCall($event->getBlock()->getPosition(), $event);
	}

	public function inventoryOpenEvent(InventoryOpenEvent $event): void {
		$inv = $event->getPlayer()->getInventory();

		foreach($inv->getContents() as $k => $content){
			if($content instanceof SkyblockItem){
				if($content->getProperties()->isUnique()){
					$content->makeUnique();
					$inv->setItem($k, $content);
				}
			}
		}
	}

	public function onInventoryTransaction(InventoryTransactionEvent $event): void {
		$transaction = $event->getTransaction();
		$player = $transaction->getSource();

		foreach($transaction->getActions() as $action){
			if($action instanceof SlotChangeAction){
				//var_dump("slot: " . $action->getSlot());
				$itemClickedWith = $action->getSourceItem();
				$itemClicked = $action->getTargetItem();

				//var_dump("itemClickedWith: " . $itemClickedWith->getId());
				//var_dump("itemClicked: " . $itemClicked->getId());

				if($itemClickedWith instanceof SkyblockItem) {
					//if($itemClicked->isNull() || $itemClickedWith->isNull()) continue;
					$levelAction = null;
					$action = null;

					foreach ($event->getTransaction()->getActions() as $a) {
						if ($a instanceof SlotChangeAction) {
							if ($itemClickedWith->equals($a->getSourceItem())) {
								$levelAction = $a;
								//$itemClicked = $a->getTargetItem();
							} else {
								$action = $a;
								//$itemClicked = $a->getSourceItem();
							}
						}

					}

					if($action !== null && $levelAction !== null){
						//var_dump("calls:");
						$itemClickedWith->onTransaction($player, $itemClicked, $itemClickedWith, $action, $levelAction, $event);
					}
				}
			}
		}
	}

    /**
     * @param BlockFormEvent $event
     * @priority MONITOR
     */
    public function onBlockForm(BlockFormEvent $event) : void {
        if ($event->getNewState()->getId() === BlockLegacyIds::COBBLESTONE) {
            $block = $event->getBlock();
            $choice = BlockFactory::getInstance()->get(self::GEN_BLOCKS[array_rand(self::GEN_BLOCKS)], 0);
            $event->cancel();
            $block->getPosition()->getWorld()->setBlock($block->getPosition(), $choice, true);
            $block->getPosition()->getWorld()->addSound($block->getPosition(), new FizzSound());
        }
    }

    public function onJump(PlayerJumpEvent $event) : void {
        $session = new Session($event->getPlayer());
        $session->increaseForgeData("jump");

        QuestHandler::getInstance()->increaseProgress(IQuest::JUMP, $event->getPlayer(), $session);
    }

    public function onSneak(PlayerToggleSneakEvent $event) : void {
        QuestHandler::getInstance()->increaseProgress(IQuest::SNEAK, $event->getPlayer(), new Session($event->getPlayer()));
    }


    public function onPlayerCreation(PlayerCreationEvent $event) : void {
        $event->setPlayerClass(AetherPlayer::class);
    }
}
