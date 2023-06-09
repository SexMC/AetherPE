<?php

declare(strict_types=1);

namespace skyblock\sessions;

use Hoa\Math\Sampler\Random;
use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\Server;
use skyblock\misc\pve\fishing\HotspotHandler;
use skyblock\misc\recipes\RecipeCraftingInstance;
use RedisClient\Client\Version\RedisClient6x0;
use RedisClient\Pipeline\PipelineInterface;
use skyblock\communication\CommunicationLogicHandler;
use skyblock\communication\packets\types\player\PlayerUpdateDataPacket;
use skyblock\Database;
use skyblock\events\economy\PlayerEssenceGainEvent;
use skyblock\islands\IslandInterface;
use skyblock\items\special\types\RandomTeleportItem;
use skyblock\items\special\types\TeleportItem;
use skyblock\misc\booster\Booster;
use skyblock\misc\quests\IQuest;
use skyblock\misc\quests\QuestHandler;
use skyblock\player\AetherPlayer;
use skyblock\utils\ScoreboardUtils;
use skyblock\utils\Utils;

trait SessionData {

	public function playerExists(): bool {
		$query = Database::getInstance()->redisGet("player.{$this->username}.wdxuid");

		if($query === null) return false;

		return true;
	}

	public function loadEnderchest(array $data = []): void {
		if(($player = $this->getPlayer()) !== null) {
			$raw = !empty($data) ? $data : json_decode(Database::getInstance()->redisGet("player.{$this->username}.enderchest") ?? "{}", true);
			$contents = [];
			foreach($raw as $k => $v){
				$contents[$k] = Item::jsonDeserialize($v);
			}

			$player->getEnderInventory()->setContents($contents);
			$player->enderchestLoaded = true;
		}
	}

	public function getEnderchestInventory(): array {
		$raw = json_decode(Database::getInstance()->redisGet("player.{$this->username}.enderchest") ?? "{}", true);
		$contents = [];
		foreach($raw as $k => $v){
			$contents[$k] = Item::jsonDeserialize($v);
		}

		return $contents;
	}

	public function loadInventory(array $data = []): void {
		if(($player = $this->getPlayer()) !== null) {
			$raw = !empty($data) ? $data : json_decode(Database::getInstance()->redisGet("player.{$this->username}.inventory") ?? "{}", true);
			$contents = [];
			foreach($raw as $k => $v){
				$contents[$k] = Item::jsonDeserialize($v);
			}

			$player->getInventory()->setContents($contents);
			$player->inventoryLoaded = true;
		}
	}

	public function getInventory(): array {
		$raw = json_decode(Database::getInstance()->redisGet("player.{$this->username}.inventory") ?? "{}", true);
		$contents = [];
		foreach($raw as $k => $v){
			$contents[$k] = Item::jsonDeserialize($v);
		}

		return $contents;
	}

	public function getArmorInventory(): array {
		$raw = json_decode(Database::getInstance()->redisGet("player.{$this->username}.armorInventory") ?? "{}", true);
		$contents = [];
		foreach($raw as $k => $v){
			$contents[$k] = Item::jsonDeserialize($v);
		}

		return $contents;
	}

	public function loadArmorInventory(array $data = []): void {
		if(($player = $this->getPlayer()) !== null) {
			$raw = !empty($data) ? $data : json_decode(Database::getInstance()->redisGet("player.{$this->username}.armorInventory") ?? "{}", true);
			$contents = [];
			foreach($raw as $k => $v){
				$contents[$k] = Item::jsonDeserialize($v);
			}

			$player->getArmorInventory()->setContents($contents);
			$player->armorInventoryLoaded = true;
		}
	}

	public function saveInventory(AetherPlayer $player, Inventory $inventory = null, PipelineInterface $pipeline = null): void {
		if(!$player->inventoryLoaded) return;
		($pipeline ?? $this->getRedis())->set("player.{$this->username}.inventory", json_encode(($inventory !== null ? $inventory->getContents() : $player->getInventory()->getContents())));
	}

	public function saveArmorInventory(AetherPlayer $player, Inventory $inventory = null, PipelineInterface $pipeline = null): void {
		if(!$player->armorInventoryLoaded) return;
		($pipeline ?? $this->getRedis())->set("player.{$this->username}.armorInventory", json_encode(($inventory !== null ? $inventory->getContents() :$player->getArmorInventory()->getContents())));
	}

	public function saveEnderchest(AetherPlayer $player, Inventory $inventory = null, PipelineInterface $pipeline = null): void {
		if(!$player->enderchestLoaded) return;
		($pipeline ?? $this->getRedis())->set("player.{$this->username}.enderchest", json_encode(($inventory !== null ? $inventory->getContents() :$player->getEnderInventory()->getContents())));
	}

	public function getXuid(): string {
		return Database::getInstance()->redisGet("player.{$this->username}.xuid");
	}

	public function setXuid(string $xuid): void {
		Database::getInstance()->redisSet("player.{$this->username}.xuid", $xuid);
	}

	public function getIslandName(): ?string {
		$name = Database::getInstance()->redisGet("player.{$this->username}.islandName");

		if($name === "" || $name === null) return null;

		return $name;
	}

	public function setIslandName(?string $name): void {
		if(($p = $this->getPlayer())){
			QuestHandler::getInstance()->increaseProgress(IQuest::ISLAND_CREATE_JOIN, $p, $this);
		}

		Database::getInstance()->redisSet("player.{$this->username}.islandName", $name);
	}

	public function getLastInterestUnix(): int {
		$p = $this->getPlayer();

		$time = 0;

		if($p){
			$time = $p->getCurrentProfile()->getCreationUnix();
		}

		return (int) (Database::getInstance()->redisGet("player.{$this->username}.lastInterestUnix") ?? $time);
	}

	public function setLastInterestUnix(int $unix): void {
		Database::getInstance()->redisSet("player.{$this->username}.lastInterestUnix", $unix);
	}


	public function getPurse(): int {
		return (int) (Database::getInstance()->redisGet("player.{$this->username}.money") ?? 0);
	}

	//TODO: https://i.imgur.com/gWNsr01.png
	public function getBank(): int {
		return (int) (Database::getInstance()->redisGet("player.{$this->username}.bank") ?? 0);
	}

	public function getBankLevel(): int {
		return (int) (Database::getInstance()->redisGet("player.{$this->username}.bankLevel") ?? 1);
	}

	public function setBankLevel(int $lvl): void {
		Database::getInstance()->redisSet("player.{$this->username}.bankLevel", $lvl);
	}

	public function getLastInterest(): int {
		return (int) (Database::getInstance()->redisGet("player.{$this->username}.bankLastInterest") ?? 0);
	}

	public function setLastInterest(int $lvl): void {
		Database::getInstance()->redisSet("player.{$this->username}.bankLastInterest", $lvl);
	}



	public function getSlotCredits(): int {
		return (int) (Database::getInstance()->redisGet("player.{$this->username}.slotcredits") ?? 0);
	}

	public function setSlotCredits(int $am): void {
		Database::getInstance()->redisSet("player.{$this->username}.slotcredits", $am);

		if($this->getSlotCredits() > 250){
			$this->setSlotCredits(250);
		}
	}

	public function increaseSlotCredits(int $am): void {
		$this->getRedis()->incrby("player.{$this->username}.slotcredits", $am);

		if($this->getSlotCredits() > 250){
			$this->setSlotCredits(250);
		}
	}

	public function decreaseSlotCredits(int $am): void {
		$this->getRedis()->decrby("player.{$this->username}.slotcredits", $am);
	}

	//returns playtime in seconds
	public function getPlayTime(): int {
		return (int) (Database::getInstance()->redisGet("player.{$this->username}.playTime") ?? 0);
	}

	public function setPlayTime(int $playtime, PipelineInterface $pipeline = null): void {
		($pipeline ?? $this->getRedis())->set("player.{$this->username}.playTime", $playtime);
	}

	public function setPurse(int $money): void {
		Database::getInstance()->redisSet("player.{$this->username}.money", $money);
		if($p = $this->getPlayer()){
			ScoreboardUtils::setLine($p, ScoreboardUtils::LINE_PERSONAL_MONEY, $money);
		}else {
			CommunicationLogicHandler::getInstance()->sendPacket(new PlayerUpdateDataPacket(
				$this->username,
				PlayerUpdateDataPacket::UPDATE_SCOREBOARD . ScoreboardUtils::LINE_PERSONAL_MONEY
			));
		}
	}

	public function setBank(int $money): void {
		Database::getInstance()->redisSet("player.{$this->username}.bank", $money);
	}

	public function increasePurse(int $amount = 1): void {
		$new = $this->getRedis()->incrby("player.{$this->username}.money", $amount);
		if($p = $this->getPlayer()){
			ScoreboardUtils::setLine($p, ScoreboardUtils::LINE_PERSONAL_MONEY, $new);
		}else {
			CommunicationLogicHandler::getInstance()->sendPacket(new PlayerUpdateDataPacket(
				$this->username,
				PlayerUpdateDataPacket::UPDATE_SCOREBOARD . ScoreboardUtils::LINE_PERSONAL_MONEY
			));
		}
	}

	public function decreasePurse(int $amount = 1): void {
		$new = $this->getRedis()->decrby("player.{$this->username}.money", $amount);
		if($p = $this->getPlayer()){
			ScoreboardUtils::setLine($p, ScoreboardUtils::LINE_PERSONAL_MONEY, $new);
		} else {
			CommunicationLogicHandler::getInstance()->sendPacket(new PlayerUpdateDataPacket(
				$this->username,
				PlayerUpdateDataPacket::UPDATE_SCOREBOARD . ScoreboardUtils::LINE_PERSONAL_MONEY
			));
		}
	}

	public function getTransactionHistory(int $elements = 15): array {
		$raw = $this->getRedis()->lrange("player.{$this->username}.bankTransactions", 0, $elements-1);
		$data = [];

		foreach($raw as $v){
			$data[] = json_decode($v, true);
		}

		return $data;
	}

	/**
	 * @param array<int: deposit or withdraw: if 0 then deposit else withdraw, int: amount, int: unix, string: player username>  $msg
	 *
	 * @return void
	 */
	public function addTransactionHistory(array $msg): void {
		$this->getRedis()->lpush("player.{$this->username}.bankTransactions", json_encode($msg));
	}


	public function setFarmingXP(int $xp): void  {
		Database::getInstance()->redisSet("player.{$this->username}.farmingXp", $xp);
	}

	public function getMinecraftXP(): int {
		return (int) (Database::getInstance()->redisGet("player.{$this->username}.minecraftXp") ?? 0);
	}

	public function setMinecraftXP(int $xp, PipelineInterface $pipeline = null): void {
		($pipeline ?? $this->getRedis())->set("player.{$this->username}.minecraftXp", $xp);
	}

	public function getEssence(): int {
		return (int) (Database::getInstance()->redisGet("player.{$this->username}.essence") ?? 0);
	}

	public function setEssence(int $essence): void {
		Database::getInstance()->redisSet("player.{$this->username}.essence", $essence);

		if(($p = $this->getPlayer())){
			ScoreboardUtils::setLine($p, ScoreboardUtils::LINE_PERSONAL_ESSENCE, $essence);
		}else {
			CommunicationLogicHandler::getInstance()->sendPacket(new PlayerUpdateDataPacket(
				$this->username,
				PlayerUpdateDataPacket::UPDATE_BOOSTERS . ScoreboardUtils::LINE_PERSONAL_ESSENCE
			));
		}
	}

	public function increaseEssence(int $amount = 1, bool $callEvent = true): int {
		if($callEvent && $this->getPlayer()){
			$event = new PlayerEssenceGainEvent($this->getPlayer(), $amount);
			$event->call();
			if($event->isCancelled()) return $this->getEssence();

			$amount = (int) ceil($event->getGain());
		}

		$new = $this->getRedis()->incrby("player.{$this->username}.essence", $amount);

		if($this->getPlayer()){
			ScoreboardUtils::setLine($this->getPlayer(), ScoreboardUtils::LINE_PERSONAL_ESSENCE, $new, $this);
		}else {
			CommunicationLogicHandler::getInstance()->sendPacket(new PlayerUpdateDataPacket(
				$this->username,
				PlayerUpdateDataPacket::UPDATE_BOOSTERS . ScoreboardUtils::LINE_PERSONAL_ESSENCE
			));
		}

		return (int) $new;
	}

	public function decreaseEssence(int $amount = 1): void {
		$new = $this->getRedis()->decrby("player.{$this->username}.essence", $amount);
		if(($p = $this->getPlayer())){
			ScoreboardUtils::setLine($p, ScoreboardUtils::LINE_PERSONAL_ESSENCE, $new, $this);
		}else {
			CommunicationLogicHandler::getInstance()->sendPacket(new PlayerUpdateDataPacket(
				$this->username,
				PlayerUpdateDataPacket::UPDATE_BOOSTERS . ScoreboardUtils::LINE_PERSONAL_ESSENCE
			));
		}
	}

	public function addInvitation(string $islandName): void {
		$this->getRedis()->lpush("player.{$this->username}.invitations", json_encode([$islandName, time()]));
	}
	
	public function setCollectItems(array $items): void {
		$this->getRedis()->del("player.{$this->username}.collect");

		foreach($items as $i){
			$this->addCollectItem($i);
		}
	}
	
	public function addCollectItem(Item $item): void {
		$this->getRedis()->lpush("player.{$this->username}.collect", json_encode($item->jsonSerialize()));
	}

	/**
	 * @return Item[]
	 */
	public function getCollectItems(): array {
		$data = $this->getRedis()->lrange("player.{$this->username}.collect", 0, -1) ?? [];
		
		$a = [];
		foreach($data as $k => $v){
			if($v === null) continue;
			$a[] = Item::jsonDeserialize(json_decode($v, true));
		}
		
		return $a;
	}

	public function getInvitations(): array {
		$invitations = $this->getRedis()->lrange("player.{$this->username}.invitations", 0, -1);

		foreach($invitations as $k => $v){
			$data = json_decode($v, true);

			if(time() - $data[1] >= IslandInterface::INVITATION_EXPIRE_TIME){
				unset($invitations[$k]);
			} else $invitations[$k] = $data;
		}

		return $invitations;
	}

	public function removeInvitation(array $invitationData): bool {
		return (bool) $this->getRedis()->lrem("player.{$this->username}.invitations", 1, json_encode($invitationData));
	}

	public function setXpBooster(?Booster $booster): void {
		if($booster === null){
			$this->getRedis()->del("player.{$this->username}.xpbooster");
			return;
		}

		Database::getInstance()->redisSet("player.{$this->username}.xpbooster", json_encode($booster));
	}

	public function getXpBooster(): ?Booster {
		$data = Database::getInstance()->redisGet("player.{$this->username}.xpbooster") ?? null;

		if($data === null || $data === ""){
			return null;
		}

		return Booster::jsonDeserialize(json_decode($data, true));
	}

	public function setFarmingXpBooster(?Booster $booster): void {
		if($booster === null){
			$this->getRedis()->del("player.{$this->username}.farmingbooster");
			return;
		}

		Database::getInstance()->redisSet("player.{$this->username}.farmingbooster", json_encode($booster));
	}

	public function getFarmingXpBooster(): ?Booster {
		$data = Database::getInstance()->redisGet("player.{$this->username}.farmingbooster") ?? null;

		if($data === null || $data === ""){
			return null;
		}

		return Booster::jsonDeserialize(json_decode($data, true));
	}

	public function getKills(): int {
		return (int) Database::getInstance()->redisGet("player.{$this->username}.kills" ?? 0);
	}

	public function setKills(int $kills): void {
		Database::getInstance()->redisSet("player.{$this->username}.kills", $kills);
	}

	public function getDeaths(): int {
		return (int) Database::getInstance()->redisGet("player.{$this->username}.deaths" ?? 0);
	}

	public function setDeaths(int $kills): void {
		Database::getInstance()->redisSet("player.{$this->username}.deaths", $kills);
	}

    public function enableStaffMode(): void {
        Database::getInstance()->redisSet("player.$this->username.staffmode", "1");
        if ($this->player !== null) {
            $items = [];
            foreach ($this->player->getInventory()->getContents() as $slot => $item) {
                $items[$slot] = $item->jsonSerialize();
            }

            Database::getInstance()->redisSet("player.$this->username.staffinventory", json_encode($items));
			$this->player->getInventory()->clearAll();
            $this->player->despawnFromAll();

			Utils::addItem($this->player, TeleportItem::getItem());
			Utils::addItem($this->player, RandomTeleportItem::getItem());
        }
    }

    public function disableStaffMode(): void {
        Database::getInstance()->redisSet("player.$this->username.staffmode", "0");
        if ($this->player !== null) {
            $serializedItems = json_decode(Database::getInstance()->redisGet("player.$this->username.staffinventory") ?? "", true);
            $items = [];
            foreach ($serializedItems as $slot => $serializedItem) {
                $items[$slot] = Item::jsonDeserialize($serializedItem);
            }

            $this->player->getInventory()->setContents($items);
            $this->player->spawnToAll();
        }
    }

    public function isStaffMode(): bool {
        return Database::getInstance()->redisGet("player.$this->username.staffmode") === "1";
    }

    public function setFrozen(bool $frozen = true): void {
        Database::getInstance()->redisSet("player.$this->username.frozen", $frozen ? "1" : "2");

        $player = $this->getPlayer();
        if ($player instanceof AetherPlayer) {
			$player->frozen = $frozen;
            $player->setImmobile($frozen);
        } else {
			CommunicationLogicHandler::getInstance()->sendPacket(new PlayerUpdateDataPacket(
				$this->username,
				PlayerUpdateDataPacket::UPDATE_FROZEN
			));
		}

    }

    public function isFrozen(): bool {
        return Database::getInstance()->redisGet("player.$this->username.frozen") === "1";
    }

	public function getQuestData(): array {
		$data = $this->getRedis()->get("player.{$this->username}.questData");

		if($data === null){
			return [];
		}

		return json_decode($data, true);
	}

	/**
	 * @return RecipeCraftingInstance[]
	 */
	public function getActiveRecipes(): array {
		$data = json_decode(($this->getRedis()->get("player.{$this->username}.activeRecipes") ?? "{}"), true);
		$recipes = [];

		foreach($data as $v){
			$rcp = RecipeCraftingInstance::fromData($v);
			$recipes[$rcp->slot] = $rcp;
		}

		return $recipes;
	}

	public function setRecipes(array $recipes): void {
		$this->getRedis()->set("player.{$this->username}.activeRecipes", json_encode($recipes));
	}

	public function setQuestData(array $data, PipelineInterface $pipeline = null): void {
		($pipeline ?? $this->getRedis())->set("player.{$this->username}.questData", json_encode($data));
	}

	public function getForgeData(string $key): int {
		return (int) ($this->getRedis()->get("player.{$this->username}.forge.$key") ?? 0);
	}

	public function setForgeData(string $key, int $val): void {
		$this->getRedis()->set("player.{$this->username}.forge.$key", $val);
	}

	public function increaseForgeData(string $key, int $am = 1): void {
		$this->getRedis()->incrby("player.{$this->username}.forge.$key", $am);
	}

	public function decreaseForgeData(string $key, int $am = 1): void {
		$this->getRedis()->decrby("player.{$this->username}.forge.$key", $am);
	}

	public function setAutoSell(bool $enabled): void {
		$this->getRedis()->set("player.{$this->username}.autosell", $enabled);
	}

	public function getAutoSell(): bool {
		return (bool) ($this->getRedis()->get("player.{$this->username}.autosell") ?? false);
	}

	public function setIslandChat(bool $enabled): void {
		$this->getRedis()->set("player.{$this->username}.islandchat", $enabled);
	}

	public function getIslandChat(): bool {
		return (bool) ($this->getRedis()->get("player.{$this->username}.islandchat") ?? false);
	}

	public function getNick(): ?string {
		return $this->getRedis()->get("player.{$this->username}.nick") ?? null;
	}

	public function setNick(?string $nick): void {
		if($nick === null){
			if($n = $this->getNick()){
				$this->getRedis()->del("server.nick." . strtolower($n));
			}
			$this->getRedis()->del("player.{$this->username}.nick");
		} else {
			$this->getRedis()->set("player.{$this->username}.nick", $nick);
			$this->getRedis()->set("server.nick." . strtolower($nick), $this->username);
		}
	}

	public function getWeeklyLootbox(string $lb): int {
		return (int) (Database::getInstance()->getRedis()->get("player.{$this->username}.weeklylb.$lb") ?? 0);
	}

	public function setWeeklyLootbox(string $lb, int $slot): void {
		Database::getInstance()->getRedis()->set("player.{$this->username}.weeklylb.$lb", $slot);
	}

    public function hasReclaimed(): bool {
        return (bool) ($this->getRedis()->get("player.$this->username.reclaimed") ?? false);
    }

    public function setReclaimed(bool $reclaimed): void {
        $this->getRedis()->set("player.$this->username.reclaimed", $reclaimed);
    }

	//will return -1 if player is done with quests
	public function getNpcStarterQuestProgress(): int {
		return (int) ($this->getRedis()->get("player.$this->username.starterNpcQuest") ?? 0);
	}

	public function setNpcStarterQuestProgress(int $progress): void {
		$this->getRedis()->set("player.$this->username.starterNpcQuest", $progress);
	}

	public function getLastProfileSwitchUnix(): int {
		return (int) ($this->getRedis()->get("player.{$this->username}.lastProfileSwitchUnix") ?? 0);
	}

	public function setLastProfileSwitchUnix(int $unix): void {
		$this->getRedis()->set("player.{$this->username}.lastProfileSwitchUnix", $unix);
	}


	public function getRedis(): RedisClient6x0 {
		return Database::getInstance()->getRedis();
	}
}