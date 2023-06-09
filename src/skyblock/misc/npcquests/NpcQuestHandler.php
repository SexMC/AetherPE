<?php

declare(strict_types=1);

namespace skyblock\misc\npcquests;

use Closure;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\block\BlockUpdateEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntitySpawnEvent;
use pocketmine\event\EventPriority;
use pocketmine\event\player\PlayerPostChunkSendEvent;
use pocketmine\network\mcpe\convert\RuntimeBlockMapping;
use pocketmine\network\mcpe\protocol\SpawnParticleEffectPacket;
use pocketmine\network\mcpe\protocol\types\BlockPosition;
use pocketmine\network\mcpe\protocol\types\ParticleIds;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\world\particle\EnchantParticle;
use skyblock\Main;
use skyblock\misc\npcquests\types\AdamQuest;
use skyblock\misc\npcquests\types\GaredQuest;
use skyblock\misc\npcquests\types\JadeQuest;
use skyblock\misc\npcquests\types\LukeQuest;
use skyblock\misc\npcquests\types\RennaQuest;
use skyblock\misc\npcquests\types\SelericQuest;
use skyblock\misc\npcquests\types\WalterQuest;
use skyblock\misc\npcquests\types\YekrutQuest;
use skyblock\player\AetherPlayer;
use skyblock\sessions\Session;
use skyblock\traits\AetherHandlerTrait;
use skyblock\traits\AwaitStdTrait;
use skyblock\traits\PlayerCooldownTrait;
use skyblock\utils\EntityUtils;
use slapper\entities\SlapperEntity;
use slapper\entities\SlapperHuman;
use slapper\events\SlapperHitEvent;
use SOFe\AwaitGenerator\Await;


//during this quest phase, the player will not receive any messages from any other players or any other external source such as event messages
//the players skyblock menu item at slot 8 will also be replaced with a quest book that will only show the npc quests.
//the player wont be able to create an island or run any commands either.
//this phase is called the "walkthrough/starting quest phase". it is to force players to try to understand the game before starting.
//topics told here: accessory, pets, recipes, collections, skills, core player stats (strength, health, ....)
class NpcQuestHandler{
	use AwaitStdTrait;
	use AetherHandlerTrait;
	use PlayerCooldownTrait; //it's used here to make it so u can't activate conversation with the same npc multiple times

	/** @var NpcQuest[] */
	private array $quests = [];
	private array $questsByIdentifier = [];
	private array $originalBlocks = [];

	public function onEnable() : void{
		Server::getInstance()->getPluginManager()->registerEvent(SlapperHitEvent::class, Closure::fromCallable([$this, "onHit"]), EventPriority::LOWEST, Main::getInstance());

		$this->register(new YekrutQuest());
		$this->register(new GaredQuest());
		$this->register(new SelericQuest());
		$this->register(new AdamQuest());
		$this->register(new RennaQuest());
		$this->register(new WalterQuest());
		$this->register(new LukeQuest());
		$this->register(new JadeQuest());

		
		/*Await::f2c(function() {
			foreach(Server::getInstance()->getOnlinePlayers() as $player){

			}
		});*/

		Await::f2c(function() {
			while(Server::getInstance()->isRunning()){
				/** @var EntitySpawnEvent $ev */
				$ev = yield $this->getStd()->awaitEvent(EntitySpawnEvent::class, fn(EntitySpawnEvent $e) => true, true, EventPriority::HIGHEST, false);

				$e = $ev->getEntity();

				$selectedQuest = null;
				if($e instanceof SlapperHuman){
					$name = strtolower(TextFormat::clean($e->getNameTag()));

					/** @var NpcQuest $quest */
					foreach($this->quests as $quest){
						if(str_contains(strtolower($quest->getIdentifier()), $name)){
							$selectedQuest = $quest;
							break;
						}
					}

					if($selectedQuest !== null){
						Await::f2c(function() use($e) {
							while(!$e->isClosed()){
								$packet = new SpawnParticleEffectPacket();
								$packet->position = $e->getPosition()->add(0, 2, 0);
								$packet->particleName = "minecraft:crop_growth_emitter";

								Server::getInstance()->broadcastPackets($e->getViewers(), [$packet]);
								yield $this->getStd()->sleep(40);
							}
						});
					}
				}

			}
		});

		Await::f2c(function() {
			while(Server::getInstance()->isRunning()){
				yield $this->getStd()->sleep(20);

				$world = Server::getInstance()->getWorldManager()->getDefaultWorld();
				foreach($world->getPlayers() as $player){
					yield $this->getStd()->sleep(1);

					$s = new Session($player);

					$progress = $s->getNpcStarterQuestProgress();

					if($progress === -1) continue;

					$pk = UpdateBlockPacket::create(BlockPosition::fromVector3($this->quests[$progress]->getNpcPosition()), RuntimeBlockMapping::getInstance()->toRuntimeId(VanillaBlocks::BEACON()->getFullId()), UpdateBlockPacket::FLAG_NETWORK, UpdateBlockPacket::DATA_LAYER_NORMAL);
					$player->getNetworkSession()->sendDataPacket($pk);

					$previous = $this->quests[$progress-1] ?? null;
					if($previous === null) continue;

					$pk = UpdateBlockPacket::create(BlockPosition::fromVector3($previous->getNpcPosition()), RuntimeBlockMapping::getInstance()->toRuntimeId($world->getBlock($previous->getNpcPosition())->getFullId()), UpdateBlockPacket::FLAG_NETWORK, UpdateBlockPacket::DATA_LAYER_NORMAL);
					$player->getNetworkSession()->sendDataPacket($pk);
				}
			}
		});
	}

	public function register(NpcQuest $quest) : void{
		$this->quests[$quest->getOrder()] = $quest;
		$this->quests[strtolower(TextFormat::clean($quest->getIdentifier()))] = $quest;
	}

	/**
	 * @return NpcQuest[]
	 */
	public function getQuests() : array{
		return $this->quests;
	}

	public function onHit(SlapperHitEvent $event) : void{
		$e = $event->getEntity();
		/** @var AetherPlayer $damager */
		$damager = $event->getDamager();


		$name = strtolower(TextFormat::clean($e->getNameTag()));
		$selectedQuest = null;
		$originalSelectedQuest = null;
		/** @var NpcQuest $quest */
		foreach($this->quests as $quest){
			if(str_contains(strtolower($quest->getIdentifier()), $name)){
				$selectedQuest = $quest;
				$originalSelectedQuest = $quest;
				break;
			}
		}

		if($selectedQuest === null) return;
		$session = new Session($damager);


		if($selectedQuest->finished($session)){
			EntityUtils::playSound("mob.villager.no", $damager->getLocation());
			$damager->sendMessage("you have already finished this quest, do next one");
			return;
		}

		$progress = $session->getNpcStarterQuestProgress();

		if($progress === -1){
			$damager->sendMessage("you have already finished all npc starter quests.");
			return;
		}

		/** @var NpcQuest $currentQuest */
		$currentQuest = $this->quests[$progress] ?? null;

		if($currentQuest === null){
			$damager->sendMessage("error?");
			return;
		}

		if($currentQuest->getOrder() === $selectedQuest->getOrder()){
			$selectedQuest->startConversation($damager);
			return;
		}


		$damager->sendMessage($originalSelectedQuest->getNotUnlockedMessage());

	}
}