<?php

declare(strict_types=1);

namespace skyblock\blocks\custom;

use pocketmine\event\Event;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\world\Position;
use skyblock\forms\misc\CustomBlockForm;
use skyblock\Main;
use skyblock\sessions\Session;
use skyblock\traits\PlayerCooldownTrait;
use skyblock\utils\IslandUtils;

abstract class CustomBlock  implements ICustomBlock{
	use PlayerCooldownTrait;

	public const TAG_CUSTOM_BLOCK = "tag_custom_block";

	protected abstract static function buildItem(): Item;

	public function load(CustomBlockTile $tile): void {
		$this->onLoad($tile);
	}

	public function onPlace(Player $player, Position $pos) : bool{
		if($this->getLimitPerChunk() !== -1){
			$x = $pos->x;
			$z = $pos->z;

			$count = 0;
			foreach($pos->getWorld()->getChunk($x >> 4, $z >> 4)->getTiles() as $chunkTile){
				if($chunkTile instanceof CustomBlockTile){
					if($chunkTile->getSpecialBlock()::getIdentifier() === static::getIdentifier()) {
						$count++;
					}
				}
			}


			if($count >= $this->getLimitPerChunk()){
				$player->sendMessage(Main::PREFIX . "You can only place {$this->getLimitPerChunk()} of this custom block in a chunk");
				return false;
			}
		}

		$island = IslandUtils::getIslandByWorld($pos->getWorld());
		if($island->exists()){
			$island->increaseValue($this->getIslandValue());

			$name = static::buildItem()->getCustomName();
			$island->announce(Main::PREFIX . "§c{$player->getName()}§7 has placed a $name §r§7at x: §c{$pos->getFloorX()}§7, y: §c{$pos->getFloorY()}§7, z: §c{$pos->getFloorZ()}");
		}

		return true;
	}

	public function onBreak(Player $player, CustomBlockTile $tile) : bool{
		$island = IslandUtils::getIslandByWorld($player->getPosition()->getWorld());
		if($island->exists()){
			$island->decreaseValue($this->getIslandValue());

			$pos = $tile->getPosition();
			$name = static::buildItem()->getCustomName();
			$island->announce(Main::PREFIX . "§c{$player->getName()}§7 broke a $name §r§7at x: §c{$pos->getFloorX()}§7, y: §c{$pos->getFloorY()}§7, z: §c{$pos->getFloorZ()}");
		}

		if(time() - $tile->getTimePlaced() >= 5 * 60){
			$session = new Session($player);

			if($session->getPurse() < $this->getMineCost()){
				$player->sendMessage(Main::PREFIX . "You don't have enough money to mine this block");
				return false;
			}

			$session->decreasePurse($this->getMineCost());
		}


		$tile->close();
		return true;
	}

	public function hasEvent(Event $event) : bool{
		return in_array($event::class, $this->getDesiredEvents());
	}

	public function onInteract(Player $player, CustomBlockTile $tile, PlayerInteractEvent $event) : void{
		$event->cancel();

		if($this->isOnCooldown($player)) return;
		if($event->getAction() === PlayerInteractEvent::LEFT_CLICK_BLOCK) return;


		$player->sendForm(new CustomBlockForm($tile));
		$this->setCooldown($player, 1);
	}

	public static function getItem(): Item {
		$item = static::buildItem();
		$item->getNamedTag()->setString(self::TAG_CUSTOM_BLOCK, static::getIdentifier());

		//TODO: placing of this look at factions code
		//TODO: https://github.com/AetherPE38/factions/blob/b8646892ab36675fe05edcda2b1771f355afcf22/src/aetherpe38/factions/listeners/GameListener.php#L417

		return $item;
	}
}