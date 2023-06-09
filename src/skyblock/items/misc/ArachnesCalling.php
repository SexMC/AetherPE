<?php

declare(strict_types=1);

namespace skyblock\items\misc;

use pocketmine\block\Block;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\Location;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\EventPriority;
use pocketmine\item\ItemBlock;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemUseResult;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\Server;
use skyblock\entity\boss\PveEntity;
use skyblock\items\rarity\Rarity;
use skyblock\items\SkyblockItem;
use skyblock\items\SkyblockItemProperties;
use skyblock\Main;
use skyblock\misc\areas\AreaHandler;
use skyblock\misc\pve\PveHandler;
use skyblock\traits\StaticStringCooldownTrait;
use SOFe\AwaitGenerator\Await;

class ArachnesCalling extends SkyblockItem {
	use StaticStringCooldownTrait;

	private static int $callingCount = 0;

	private AxisAlignedBB $bb;

	private static bool $checking = false;

	public function __construct(ItemIdentifier $identifier, string $name = "Unknown"){
		parent::__construct($identifier, $name);

		$this->bb = $bb = new AxisAlignedBB(-285, 48, -181, -281, 51, -177);

		$this->getProperties()->setDescription([
			"§r§7Place §a4§r§7 of these at the",
			"§r§5Altar §7in §cArachne's",
			"§r§cSanctuary§7 to summon her.",
			"§r",
			'§r§7§o"It is time"§r§7 - Uninformed Spider',
			]);

		$this->setCustomName("§r" . $this->properties->getRarity()->getColor() . "Arachne's Calling");

		$this->resetLore();

		$world = Server::getInstance()->getWorldManager()->getDefaultWorld();
		for($x = (int) floor($bb->minX); $x <= (int) floor($bb->maxX); $x++){
			for($z = (int) floor($bb->minZ); $z <= (int) floor($bb->maxZ); $z++){
				for($y = (int) floor($bb->minY); $y <= (int) floor($bb->maxY); $y++){
					$world->loadChunk($x >> 4, $z >> 4);
					if($world->getBlockAt((int) floor($x), (int) floor($y), (int) floor($z))->getId() === BlockLegacyIds::CHEMICAL_HEAT){
						self::$callingCount++;
					}
				}
			}
		}
	}

	public function onInteractBlock(Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector) : ItemUseResult{
		if($this->bb->isVectorInside($blockReplace->getPosition())){
			if(self::isOnCooldown("arachne")){
				$player->sendMessage(Main::PREFIX . "Arachne is on cooldown for §c" . $this->getCooldown("arachne") . "s");
				return ItemUseResult::FAIL();
			}


			$this->pop();
			$player->getInventory()->setItemInHand($this);

			$blockReplace->getPosition()->getWorld()->setBlock($blockReplace->getPosition(), $this->getBlock());
			self::$callingCount++;

			AreaHandler::SPIDERS_DEN()->message(Main::PREFIX . "Archane's Calling: §c" . self::$callingCount . "/4");
			if(self::$callingCount >= 4){
				$bb = $this->bb;
				//spawn

				//$this->setCooldown("arachne", 60 * 5);
				Await::f2c(function() use($bb) {
					yield $this->getStd()->sleep(5);

					$world = Server::getInstance()->getWorldManager()->getDefaultWorld();
					for($x = (int) floor($bb->minX); $x <= (int) floor($bb->maxX); $x++){
						for($z = (int) floor($bb->minZ); $z <= (int) floor($bb->maxZ); $z++){
							for($y = (int) floor($bb->minY); $y <= (int) floor($bb->maxY); $y++){
								$world->loadChunk($x >> 4, $z >> 4);
								if($world->getBlockAt((int) floor($x), (int) floor($y), (int) floor($z))->getId() === BlockLegacyIds::CHEMICAL_HEAT){
									$world->setBlockAt((int) floor($x), (int) floor($y), (int) floor($z), VanillaBlocks::AIR());
								}

								yield $this->getStd()->sleep(1);
							}
						}
					}
				});


				self::setCooldown("arachne", 30*10);
				self::$callingCount = 0;
				AreaHandler::SPIDERS_DEN()->message("§r§c[BOSS] Arachne: §fYou dare call me, the queen of the dark, to accept you. I'll accept no excuses, you shall die!");
				$d = PveHandler::getInstance()->getEntities()["arachne-boss-300"];
				$e = new PveEntity($d["networkID"], Location::fromObject($blockReplace->getPosition()->add(0, 1, 0), $player->getWorld()), $d["nbt"]);
				$e->spawnToAll();
			}
		} else {
			$player->sendMessage(Main::PREFIX . "You can only place this at §4§lArachne's Altar");
		}

		return ItemUseResult::FAIL();
	}


	public function buildProperties() : SkyblockItemProperties{
		return (new SkyblockItemProperties())->setRarity(Rarity::rare())->setCanAuction(false);
	}

	public function getBlock(?int $clickedFace = null) : Block{
		return VanillaBlocks::CHEMICAL_HEAT();
	}
}