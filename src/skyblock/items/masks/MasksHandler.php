<?php

declare(strict_types=1);

namespace skyblock\items\masks;

use muqsit\simplepackethandler\SimplePacketHandler;
use pocketmine\inventory\ArmorInventory;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\InventoryContentPacket;
use pocketmine\network\mcpe\protocol\InventorySlotPacket;
use pocketmine\network\mcpe\protocol\MobEquipmentPacket;
use pocketmine\network\mcpe\protocol\PlayerSkinPacket;
use pocketmine\utils\SingletonTrait;
use skyblock\items\masks\types\AstronautMask;
use skyblock\items\masks\types\BlazeMask;
use skyblock\items\masks\types\BubbleMask;
use skyblock\items\masks\types\BunnyMask;
use skyblock\items\masks\types\ChickenHeadMask;
use skyblock\items\masks\types\ClownfishMask;
use skyblock\items\masks\types\ClydeMask;
use skyblock\items\masks\types\FarmerMask;
use skyblock\items\masks\types\FishMask;
use skyblock\items\masks\types\JaxMask;
use skyblock\items\masks\types\LanternMask;
use skyblock\items\masks\types\SkeletonMask;
use skyblock\items\masks\types\SlimeHatMask;
use skyblock\items\masks\types\SpiderMask;
use skyblock\items\masks\types\TahmKenchMask;
use skyblock\items\masks\types\VladimirMask;
use skyblock\items\masks\types\YasuoMask;
use skyblock\items\masks\types\ZedMask;
use skyblock\items\masks\types\ZombiesHeartMask;
use skyblock\Main;
use skyblock\traits\InstanceTrait;
use skyblock\traits\StringIntCache;
use skyblock\traits\StringStringCache;

class MasksHandler {

	use StringStringCache;
	use InstanceTrait;

	/** @var Mask[] */
	private array $masks = [];

	public array $cachedMasks = [];

	public function __construct(){
		self::$instance = $this;

		$interceptor = SimplePacketHandler::createInterceptor(Main::getInstance());

		$interceptor->interceptIncoming(function(PlayerSkinPacket $packet, NetworkSession $session): bool {
			$player = $session->getPlayer();
			$player->sendMessage(Main::PREFIX . "You cannot change skins while playing on the server.");

			return false;
		});

		$this->register(new LanternMask());
		//$this->register(new FarmerMask());
		$this->register(new SkeletonMask());
		$this->register(new ZombiesHeartMask());
		$this->register(new SpiderMask());
		$this->register(new SlimeHatMask());
		$this->register(new ClownfishMask());
		$this->register(new FishMask());
		$this->register(new ChickenHeadMask());
	}

	public function register(Mask $mask): void {
		$this->masks[strtolower($mask->getName())] = $mask;

		foreach($mask->getDesiredEvents() as $desiredEvent){
			Main::getInstance()->getServer()->getPluginManager()->registerEvent(
				$desiredEvent,
				\Closure::fromCallable([$mask, "tryCall"]),
				$mask->getPriority(),
				Main::getInstance(),
			);
		}
	}

	public function getMask(string $mask): ?Mask {
		return $this->masks[strtolower($mask)] ?? null;
	}

	/**
	 * @return Mask[]
	 */
	public function getAllMasks() : array{
		return $this->masks;
	}
}