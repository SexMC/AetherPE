<?php

declare(strict_types=1);

namespace skyblock\player;

use pocketmine\block\BlockLegacyIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\animation\ArmSwingAnimation;
use pocketmine\entity\Skin;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\form\Form;
use pocketmine\item\Durable;
use pocketmine\item\Item;
use pocketmine\lang\Translatable;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\permission\PermissionAttachment;
use pocketmine\player\Player;
use pocketmine\timings\Timings;
use pocketmine\world\sound\FireExtinguishSound;
use pocketmine\world\sound\ItemBreakSound;
use RedisClient\Pipeline\PipelineInterface;
use skyblock\caches\playtime\PlayTimeCache;
use skyblock\Database;
use skyblock\entity\object\AetherItemEntity;
use skyblock\events\CustomEntityDamageByEntityEvent;
use skyblock\items\potions\AetherPotionInstance;
use skyblock\Main;
use skyblock\misc\pve\PveBossbarUpdater;
use skyblock\profile\Profile;
use skyblock\sessions\Session;
use skyblock\traits\AwaitStdTrait;
use skyblock\traits\StringCooldownTrait;
use skyblock\utils\ProfileUtils;

class AetherPlayer extends Player{
	use AwaitStdTrait;
	use StringCooldownTrait;

	/** @var AetherExperienceManager */
	protected $xpManager;

	protected bool $fullyInitialized = false;

	private ?PermissionAttachment $attachment = null;

	private ?CustomEntityDamageByEntityEvent $lastCustomEntityDamageByEntityEvent = null;

	private string $rank = "Traveler";

	private Skin $originalSkin;

	public bool $inStaffMode = false;

	public bool $frozen = false;

	public string $wdXUID = "";

	public bool $initilized = false;
	public bool $inventoryLoaded = false;
	public bool $armorInventoryLoaded = false;
	public bool $enderchestLoaded = false;
	public bool $xpLoaded = false;
	/** @var AetherPotionInstance[] */
	public array $aetherPotions = [];


	private array $profileIds = [];
	private ?string $selectedProfileId = null;


	private CachedPlayerSkillData $skillData;
	private CachedPlayerPveData $pveData;
	private CachedPlayerPetData $petData;
	private CachedPlayerAccessoryData $accessoryData;
	private ?PveBossbarUpdater $pveBossbarUpdater = null;
	private CachedPlayerPotionData $potionData;

	private ?CustomSurvivalBlockBreakHandler $customBlockBreakHandler = null;

	public function getXuid() : string{
		return $this->wdXUID;
	}


	protected function initEntity(CompoundTag $nbt) : void{
		$this->profileIds = Database::getInstance()->getRedis()->smembers("player.{$this->username}.profiles");
		$this->setSelectedProfileId(Database::getInstance()->getRedis()->get("player.{$this->username}.selectedProfile"));
		if($this->getSelectedProfileId() === null){
			$this->addProfile($p = ProfileUtils::createNewProfile($this->username, []));
			$this->setSelectedProfileId($p->getUniqueId());
		}


		$this->setOriginalSkin($this->getSkin());


		parent::initEntity($nbt);

		$this->initilized = true;

		$this->xpManager = new AetherExperienceManager($this);
		$this->fullyInitialized = true;

		$session = new Session($this);

		$username = $this->getCurrentProfilePlayerSession()->getUsername();
		//$this->skillData = new CachedPlayerSkillData($username);
		//$this->pveData = new CachedPlayerPveData($username);
		//$this->petData = new CachedPlayerPetData($username);
		//$this->accessoryData = new CachedPlayerAccessoryData($username);
		//$this->potionData = new CachedPlayerPotionData($username);

		$data = Database::getInstance()->getRedis()->pipeline(function(PipelineInterface $pipeline) use ($session){
			$username = strtolower($this->username);


			$pipeline->get("player.$this->username.staffmode");
			$pipeline->get("player.$username.wdxuid");
			$pipeline->get("player.{$username}.aetherPotions");
		});
		$this->inStaffMode = (bool) ($data[0] ?? false);
		$this->wdXUID = $data[1] ?? "";


		$rank = $session->getTopRank();
		$this->setRank($rank->getColour() . $rank->getName());
	}

	public function getCurrentProfilePlayerSession() : Session{
		return $this->getCurrentProfile()->getPlayerSession($this->username);
	}

	public function getCurrentProfile() : Profile{
		return new Profile($this->getSelectedProfileId());
	}

	public function addProfile(Profile $profile) : void{
		$this->profileIds[] = $profile->getUniqueId();
		Database::getInstance()->getRedis()->sadd("player.{$this->username}.profiles", $profile->getUniqueId());
	}

	public function removeProfile(Profile $profile) : void{
		unset($this->profileIds[array_search($profile->getUniqueId(), $this->profileIds)]);
		$this->profileIds = array_values($this->profileIds); //re order the array indexes

		Database::getInstance()->getRedis()->srem("player.{$this->username}.profiles", $profile->getUniqueId());
	}

	public function getProfileIds() : array{
		return $this->profileIds;
	}

	public function getSelectedProfileId() : ?string{
		return $this->selectedProfileId;
	}

	public function setSelectedProfileId(?string $id, bool $load = true) : void{
		$this->selectedProfileId = $id;
		Database::getInstance()->getRedis()->set("player.{$this->username}.selectedProfile", $id);

		if($id !== null && $load){
			$username = $this->getCurrentProfilePlayerSession()->getUsername();
			$this->skillData = new CachedPlayerSkillData($username);
			$this->pveData = new CachedPlayerPveData($username);
			$this->petData = new CachedPlayerPetData($username);
			$this->accessoryData = new CachedPlayerAccessoryData($username);
			$this->potionData = new CachedPlayerPotionData($username);
		}
	}


	public function getPotionData() : CachedPlayerPotionData{
		return $this->potionData;
	}

	public function getPveBossbarUpdater() : ?PveBossbarUpdater{
		return $this->pveBossbarUpdater;
	}

	public function setPveBossbarUpdater(?PveBossbarUpdater $pveBossbarUpdater) : void{
		$this->pveBossbarUpdater = $pveBossbarUpdater;
	}


	public function getSkillData() : CachedPlayerSkillData{
		return $this->skillData;
	}

	public function getAccessoryData() : CachedPlayerAccessoryData{
		return $this->accessoryData;
	}

	public function getPveData() : CachedPlayerPveData{
		return $this->pveData;
	}

	public function getPetData() : CachedPlayerPetData{
		return $this->petData;
	}

	public function setRank(string $rank) : void{
		$this->rank = $rank;
		$this->setNameTag($this->getNameTag());
	}

	public function getRank() : string{
		return $this->rank;
	}

	public function sendForm(Form $form) : void{
		if($this->isOnline()){
			parent::sendForm($form);
		}else $this->getServer()->getLogger()->critical("Tried to send a form to offline user ({$this->getName()}");
	}

	public function fixAfterTeleport(){
		$this->broadcastMovement(true);
		$this->broadcastMotion();
	}

	public function sendMessage(Translatable|string|array $message) : void{
		if($this->isOnline()){
			if(is_array($message)){
				$message = implode("\n", $message);
			}

			parent::sendMessage($message);
		}else $this->getServer()->getLogger()->critical("Tried to send a message to offline user ({$this->getName()}");
	}


	public function getXpManager() : AetherExperienceManager{
		return $this->xpManager;
	}

	public function attack(EntityDamageEvent $source) : void{
		if($this->initilized === false) return;
		if($this->effectManager === null) return;

		parent::attack($source);
	}

	public function attackBlock(Vector3 $pos, int $face) : bool{
		if($pos->distanceSquared($this->location) > 10000){
			return false; //TODO: maybe this should throw an exception instead?
		}

		$target = $this->getWorld()->getBlock($pos);

		$ev = new PlayerInteractEvent($this, $this->inventory->getItemInHand(), $target, null, $face, PlayerInteractEvent::LEFT_CLICK_BLOCK);
		if($this->isSpectator()){
			$ev->cancel();
		}
		$ev->call();
		if($ev->isCancelled()){
			return false;
		}
		$this->broadcastAnimation(new ArmSwingAnimation($this), $this->getViewers());
		if($target->onAttack($this->inventory->getItemInHand(), $face, $this)){
			return true;
		}

		$block = $target->getSide($face);
		if($block->getId() === BlockLegacyIds::FIRE){
			$this->getWorld()->setBlock($block->getPosition(), VanillaBlocks::AIR());
			$this->getWorld()->addSound($block->getPosition()->add(0.5, 0.5, 0.5), new FireExtinguishSound());
			return true;
		}

		if(!$this->isCreative() && !$block->getBreakInfo()->breaksInstantly()){
			$this->customBlockBreakHandler = new CustomSurvivalBlockBreakHandler($this, $pos, $target, $face, 16);
		}

		return true;
	}

	protected function destroyCycles() : void{
		parent::destroyCycles();
		$this->customBlockBreakHandler = null;
	}

	public function onPostDisconnect(string $reason, Translatable|string|null $quitMessage) : void{
		parent::onPostDisconnect($reason, $quitMessage);
		$this->customBlockBreakHandler = null;
	}

	public function stopBreakBlock(Vector3 $pos) : void{
		if($this->customBlockBreakHandler !== null && $this->customBlockBreakHandler->getBlockPos()->distanceSquared($pos) < 0.0001){
			$this->customBlockBreakHandler = null;
		}
	}

	public function continueBreakBlock(Vector3 $pos, int $face) : void{
		if($this->customBlockBreakHandler !== null && $this->customBlockBreakHandler->getBlockPos()->distanceSquared($pos) < 0.0001){
			$this->customBlockBreakHandler->setTargetedFace($face);
		}
	}

	public function teleport(Vector3 $pos, ?float $yaw = null, ?float $pitch = null) : bool{
		$this->customBlockBreakHandler = null;
		return parent::teleport($pos, $yaw, $pitch);
	}


	public function doHitAnimationCustom() : void{
		$this->doHitAnimation();
	}

	/*public function damageArmor(float $damage) : void{
		$durabilityRemoved = (int) max(floor($damage / 4), 1);

		$armor = $this->armorInventory->getContents(true);
		foreach($armor as $item){
			if($item instanceof Armor){
				if($item instanceof HeroicArmor && mt_rand(1, 15) !== 1) continue;

				$this->damageItem($item, $durabilityRemoved);
			}
		}

		$this->armorInventory->setContents($armor);
	}*/

	private function damageItem(Durable $item, int $durabilityRemoved) : void{
		$item->applyDamage($durabilityRemoved);
		if($item->isBroken()){
			$this->broadcastSound(new ItemBreakSound());
		}
	}


	public function dropItem(Item $item) : void{
		$profileId = $this->getSelectedProfileId();
		if($profileId === null){
			return; //Players shouldn't be able to drop items if they haven't selected a profile
		}

		if(!$this->isOnCooldown("drop-alert")){
			$this->sendMessage(Main::PREFIX . "Players that are not in the same profile as you will not be able to pick up or see your items.");
		}else $this->setCooldown("drop-alert", 1200);

		$item->getNamedTag()->setString(AetherItemEntity::TAG_OWNING_PROFILE, $profileId);

		parent::dropItem($item);
	}

	public function setHealth(float $amount) : void{
		if(PlayTimeCache::getInstance()->get($this->getName()) > 5 && $this->isCreative()) return;
		if($amount > $this->getMaxHealth()){
			$amount = $this->getMaxHealth();
		}
		parent::setHealth($amount);
		$this->setNameTag($this->getNameTag());
	}


	public function onUpdate(int $currentTick) : bool{
		$tickDiff = $currentTick - $this->lastUpdate;

		if($tickDiff <= 0){
			return true;
		}

		$this->messageCounter = 2;

		$this->lastUpdate = $currentTick;

		if($this->justCreated){
			$this->onFirstUpdate($currentTick);
		}

		if(!$this->isAlive() && $this->spawned){
			$this->onDeathUpdate($tickDiff);
			return true;
		}

		$this->timings->startTiming();

		if($this->spawned){
			$this->processMostRecentMovements();
			$this->motion = new Vector3(0, 0, 0); //TODO: HACK! (Fixes player knockback being messed up)
			if($this->onGround){
				$this->inAirTicks = 0;
			}else{
				$this->inAirTicks += $tickDiff;
			}

			Timings::$entityBaseTick->startTiming();
			$this->entityBaseTick($tickDiff);
			Timings::$entityBaseTick->stopTiming();

			if(!$this->isSpectator() && $this->isAlive()){
				Timings::$playerCheckNearEntities->startTiming();
				$this->checkNearEntities();
				Timings::$playerCheckNearEntities->stopTiming();
			}

			if($this->customBlockBreakHandler !== null && !$this->customBlockBreakHandler->update()){
				$this->breakBlock($this->customBlockBreakHandler->getBlockPos());
				$this->customBlockBreakHandler = null;
			}
		}

		$this->timings->stopTiming();

		return true;
	}

	public function hasPermission($name) : bool{
		if($this->getServer()->isOp($this->getName())){
			return true;
		}

		return parent::hasPermission($name);
	}

	public function sendTip(string $message) : void{
		if($this->isOnline()){
			parent::sendTip($message);
		}
	}

	/**
	 * @return PermissionAttachment|null
	 */
	public function getAttachment() : ?PermissionAttachment{
		return $this->attachment;
	}

	/**
	 * @param PermissionAttachment|null $attachment
	 */
	public function setAttachment(?PermissionAttachment $attachment) : void{
		$this->attachment = $attachment;
	}

	public function setMovementSpeed(float $v, bool $fit = false) : void{
		$this->moveSpeedAttr->setValue($v, $fit);

		$this->networkPropertiesDirty = true;
	}

	public function setOriginalSkin(Skin $originalSkin) : void{
		$this->originalSkin = $originalSkin;
	}

	public function getOriginalSkin() : Skin{
		return $this->originalSkin;
	}


	public function canSee(Player $player) : bool{
		if($player instanceof AetherPlayer){
			if($player->inStaffMode && !$this->inStaffMode){
				return false;
			}
		}

		return parent::canSee($player);
	}

	/**
	 * @return AetherPotionInstance[]
	 */
	public function getAetherPotions() : array{
		foreach($this->aetherPotions as $k => $v){
			if($v->leftDuration <= 0){
				unset($this->aetherPotions[$k]);
			}
		}

		return $this->aetherPotions;
	}
}