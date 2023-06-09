<?php

declare(strict_types=1);

namespace skyblock\items\tools;

use customiesdevs\customies\item\CreativeInventoryInfo;
use customiesdevs\customies\item\ItemComponents;
use customiesdevs\customies\item\ItemComponentsTrait;
use pocketmine\entity\Location;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\AnimatePacket;
use pocketmine\player\Player;
use pocketmine\world\sound\ThrowSound;
use skyblock\entity\projectile\GraplingHookEntity;
use skyblock\items\rarity\Rarity;
use skyblock\items\SkyblockTool;
use skyblock\traits\StaticPlayerCooldownTrait;

class GraplingHook extends SkyblockTool implements ItemComponents{
	use ItemComponentsTrait;
	use StaticPlayerCooldownTrait;

	private static array $hookCache = [];

	public function __construct(ItemIdentifier $identifier, string $name = "Unknown"){
		//TODO: requires fishing skill 15

		parent::__construct($identifier, $name);

		$this->initComponent("grapple", new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_EQUIPMENT, CreativeInventoryInfo::GROUP_SWORD));

		$this->properties->setDescription([
			"§r§7Travel around in style using",
			"§r§7this Grapling Hook",
			"§r§82 Second Cooldown",
		]);


		$this->properties->setRarity(Rarity::uncommon());

		$this->resetLore();
	}

	public function onClickAir(Player $player, Vector3 $directionVector) : ItemUseResult{
		$location = $player->getLocation();
		$world = $player->getWorld();


		if(self::getFishingHook($player) === null) {
			if(self::isOnCooldown($player)) {
				$player->sendActionBarMessage("§cCooldown (§b" . number_format($this->getCooldown($player), 2) . "§c)");

				return parent::onClickAir($player, $directionVector);
			}
			self::setCooldown($player, 2);

			$hook = new GraplingHookEntity(Location::fromObject(
				$player->getEyePos(),
				$world,
				($location->yaw > 180 ? 360 : 0) - $location->yaw,
				-$location->pitch
			), $player);

			$ev = new ProjectileLaunchEvent($hook);
			if($ev->isCancelled()) {
				$hook->flagForDespawn();
				return parent::onClickAir($player, $directionVector);
			}

			//self::setFishingHook($hook, $player);
			$hook->spawnToAll();
		} else {
			$hook = self::getFishingHook($player);
			$hook->handleHookRetraction();
			self::setFishingHook(null, $player);
		}

		$world->broadcastPacketToViewers($location, AnimatePacket::create($player->getId(), AnimatePacket::ACTION_SWING_ARM));
		$world->addSound($player->getPosition(), new ThrowSound());



		return parent::onClickAir($player, $directionVector);
	}

	public static function getFishingHook(Player $player) : ?GraplingHookEntity {
		return self::$hookCache[$player->getId()] ?? null;
	}

	public static function setFishingHook(?GraplingHookEntity $fish, Player $player) {
		self::$hookCache[$player->getId()] = $fish;
	}
}