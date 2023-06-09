<?php

declare(strict_types=1);

namespace skyblock\menus\profile;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use skyblock\items\ItemEditor;
use skyblock\Main;
use skyblock\menus\AetherMenu;
use skyblock\menus\items\SkyblockMenu;
use skyblock\misc\recipes\RecipesHandler;
use skyblock\misc\skills\SkillHandler;
use skyblock\player\AetherPlayer;
use skyblock\player\CachedPlayerSkillData;
use skyblock\player\ranks\AetherPlusRank;
use skyblock\player\ranks\AetherRank;
use skyblock\player\ranks\AstronomicalRank;
use skyblock\player\ranks\AuroraRank;
use skyblock\player\ranks\BaseRank;
use skyblock\player\ranks\HydraRank;
use skyblock\player\ranks\TrojeRank;
use skyblock\profile\Profile;
use skyblock\sessions\Session;
use skyblock\utils\CustomEnchantUtils;
use skyblock\utils\TimeUtils;

class ProfileManagementMenu extends AetherMenu {

	private array $slots = [];
	
	const BLOCKS = [ItemIds::EMERALD_BLOCK, ItemIds::GRASS, ItemIds::DIRT, ItemIds::STONE, ItemIds::COBBLESTONE, ItemIds::DIAMOND_BLOCK, ItemIds::GOLD_BLOCK, ItemIds::GOLD_BLOCK, ItemIds::OBSIDIAN];

	public function __construct(private AetherPlayer $player, private ?AetherMenu $aetherMenu){
		$this->slots = range(18, 26);


		parent::__construct();
	}

	public function constructMenu() : InvMenu{
		$menu = $this->aetherMenu !== null ? $this->aetherMenu->getMenu() : InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$menu->getInventory()->clearAll();
		$this->menu = $menu;

		$menu->setName("Profile Management");

		$glass = VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::GRAY())->asItem()->setCustomName(" ");
		for($i = 0; $i <= 53; $i++){
			if(in_array($i, $this->slots)) continue;

			$menu->getInventory()->setItem($i, $glass);
		}

		foreach($this->player->getProfileIds() as $key => $profileId){
			$this->getMenu()->getInventory()->addItem($this->getProfileItem($key, (new Profile($profileId))));
		}

		$topRank = (new Session($this->player))->getTopRank(true);
		if(count($this->player->getProfileIds()) < (2 + $topRank->getExtraProfiles())){
			$diff = (2 + $topRank->getExtraProfiles()) - count($this->player->getProfileIds());
			for($i = 1; $i <= $diff; $i++){
				$this->getMenu()->getInventory()->addItem($this->getEmptySlot());
			}
		}

		foreach($this->getMenu()->getInventory()->getContents(true) as $slot => $content){
			if($content->isNull()){
				$this->getMenu()->getInventory()->addItem($this->getLockedProfileItem($slot));
			}
		}

		$this->getMenu()->getInventory()->setItem(49, VanillaBlocks::BARRIER()->asItem()->setCustomName("§r§cClose"));
		$this->getMenu()->getInventory()->setItem(48, VanillaItems::ARROW()->setCustomName("§r§aGo Back")->setLore(["§r§7To SkyBlock Menu"]));



		return $menu;
	}


	public function onReadonlyTransaction(InvMenuTransaction $transaction) : void{
		parent::onReadonlyTransaction($transaction);

		//TODO: finish this

		$player = $transaction->getPlayer();
		$slot = $transaction->getAction()->getSlot();
		$item = $transaction->getOut();


		assert($player instanceof AetherPlayer);

		$id = $item->getNamedTag()->getString("profile", "");

		if($id === $player->getSelectedProfileId()){
			$player->sendMessage(Main::PREFIX . "If you want to manage this profile, switch to another profile first!");
			return;
		}

		if($id !== ""){
			(new ProfileSwitchMenu($player, new Profile($id), $this))->send($player);
			return;
		}


		if($item->getId() === ItemIds::WOODEN_BUTTON){
			(new ProfileCreateMenu($player, $this))->send($player);
			return;
		}

		if($slot === 48){
			(new SkyblockMenu($player, $this))->send($player);
		}

		if($slot === 49){
			$player->removeCurrentWindow();
		}
	}

	public function getEmptySlot(): Item {
		$item = VanillaBlocks::OAK_BUTTON()->asItem();
		$item->setCustomName("§r§eEmpty Profile Slot");
		$item->setLore([
			"§r§8Available",
			"§r§7",
			"§r§7Use this slot if you want to",
			"§r§7start a new SkyBlock",
			"§r§7adventure.",
			"§r§7",
			"§r§7Each profile has its own:",
			"§r§7§r§7§l» Personal Island",
			"§r§7§r§7§l» Inventory",
			"§r§7§r§7§l» Enderchest",
			"§r§7§r§7§l» Bank & Purse",
			"§r§7§r§7§l» Quests",
			"§r§7§r§7§l» Collections",
			"§r§7",
			"§r§4§lWARNING: §r§cCreation of",
			"§r§cprofiles which boost other",
			"§r§cprofiles will be considered",
			"§r§cabusive and will be punished.",
			"§r",
			"§r§eClick to create a new profile",
		]);

		ItemEditor::addUniqueID($item);


		return $item;
	}

	public function getLockedProfileItem(int $slot): Item {
		$r = match($slot) {
			20 => TrojeRank::class,
			21 => HydraRank::class,
			22 => AuroraRank::class,
			23 => AetherRank::class,
			24 => AetherPlusRank::class,
			25, 26 => AstronomicalRank::class,
		};



		/** @var BaseRank $rank */
		$rank = new $r;
		$item = VanillaBlocks::BEDROCK()->asItem();
		$item->setCustomName("§r§cLocked profile slot");
		$item->setLore([
			"§r§8Unavailable",
			"§r",
			"§r§7Requires: §l{$rank->getColour()}" . $rank->getName() . " Rank",
			"§r§6https://store.aetherpe.net",
		]);

		ItemEditor::addUniqueID($item);

		return $item;
	}

	public function getProfileItem(int $key, Profile $profile): Item {
		$item = ItemFactory::getInstance()->get(self::BLOCKS[$key]);
		$item->setCustomName("§r§eProfile: §a" . $profile->getName());
		$equals = $this->player->getSelectedProfileId() === $profile->getUniqueId();

		$lore = [
			"§r§8" . ($equals ? "Selected slot" : "Slot in use"),
			"§r",
		];

		$session = $profile->getPlayerSession($this->player);
		$cachedSkills = $equals ? $this->player->getSkillData() : new CachedPlayerSkillData($session->getUsername());

		foreach(SkillHandler::getInstance()->getSkills() as $skill){
			$level = $cachedSkills->getSkillLevel($skill::id());

			if($level === 0) continue;

			$lore[] = "§r§7" . $skill::id() . ": §e" . CustomEnchantUtils::roman($level);
		}

		if(count($lore) === 2){
			$lore[] = "§r§cNo skills yet!";
		}

		$lore[] = "§r";
		$profileSession = $profile->getProfileSession();

		$total = count(RecipesHandler::getInstance()->getRecipes());
		$unlocked = count($profileSession->getAllUnlockedRecipesIdentifiers());
		$progress = round($unlocked / $total * 100, 2);


		$lore[] = "§r§7Recipes unlocked: §e{$progress}% §r§7(§e{$unlocked}§6/§e{$total}§7)";
		$lore[] = "§r";
		$lore[] = "§r§7Bank Coins: §6" . number_format($profileSession->getBank());
		$lore[] = "§r§7Purse Coins: §6" . number_format($profileSession->getPurse());
		$lore[] = "§r§7Age: §5" . TimeUtils::getFullyFormattedTime(time() - $profile->getCreationUnix());

		$lore[] = "§r";
		$lore[] = "§r§8§oID: " . $profile->getUniqueId();

		if($equals){
			$lore[] = "§r§7";
			$lore[] = "§r§aYou are playing on this profile!";
		} else {
			$lore[] = "§r";
			$lore[] = "§r§eClick to manage!";
		}

		$item->setLore($lore);
		$item->getNamedTag()->setString("profile", $profile->getUniqueId());


		return $item;
	}
}