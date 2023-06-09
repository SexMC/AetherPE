<?php

declare(strict_types=1);

namespace skyblock\items\itemmods;

use pocketmine\event\Event;
use pocketmine\event\Listener;
use pocketmine\player\Player;
use skyblock\events\CustomEntityDamageByEntityEvent;
use skyblock\items\customenchants\CustomEnchantInstance;
use skyblock\items\ItemEditor;
use skyblock\items\itemmods\types\AetherPeHatItemMod;
use skyblock\items\itemmods\types\BrokenHeartItemMod;
use skyblock\items\itemmods\types\BubbleAxeItemMod;
use skyblock\items\itemmods\types\BubbleBladeItemMod;
use skyblock\items\itemmods\types\BubbleHeadItemMod;
use skyblock\items\itemmods\types\BubbleRodItemMod;
use skyblock\items\itemmods\types\CrystalAmuletItemMod;
use skyblock\items\itemmods\types\LuckyJackhammerItemMod;
use skyblock\items\itemmods\types\MutatedBubbleAxeItemMod;
use skyblock\items\itemmods\types\MutatedBubbleRodItemMod;
use skyblock\items\itemmods\types\MutatedCrystalAmuletItemMod;
use skyblock\items\itemmods\types\MutatedSpaceBeltItemMod;
use skyblock\items\itemmods\types\SpaceBeltItemMod;
use skyblock\items\itemmods\types\SpaceDrillItemMod;
use skyblock\items\itemmods\types\SpaceGaloshesItemMod;
use skyblock\items\itemmods\types\SpacePassItemMod;
use skyblock\items\itemmods\types\SpaceRucksackItemMod;
use skyblock\items\itemmods\types\SpaceVisorItemMod;
use skyblock\items\itemmods\types\TurkeyClawsItemMod;
use skyblock\items\itemmods\types\TurkeyFishnetItemMod;
use skyblock\items\itemmods\types\TurkeyHatItemMod;
use skyblock\items\itemmods\types\WardensCapeItemMod;
use skyblock\items\itemmods\types\WardensHammerItemMod;
use skyblock\items\itemmods\types\WardensHeadWearItemMod;
use skyblock\items\itemmods\types\WardensPickaxeItemMod;
use skyblock\Main;
use skyblock\traits\InstanceTrait;

class ItemModHandler implements Listener {
	use InstanceTrait;

	private array $itemMods = [];

	private Main $plugin;

	public function __construct(Main $plugin)
	{
		self::$instance = $this;
		$this->plugin = $plugin;


	}

	public function getItemMod(string $id): ?ItemMod {
		return $this->itemMods[strtolower($id)] ?? null;
	}

	/**
	 * @return ItemMod[]
	 */
	public function getAllItemMods(): array
	{
		return $this->itemMods;
	}

	/**
	 * @throws \ReflectionException
	 */
	public function registerItemMod(ItemMod $itemSkin): void {
		$this->itemMods[strtolower($itemSkin->getUniqueID())] = $itemSkin;

		if(empty($itemSkin->getDesiredEvents()) === false) {
			foreach($itemSkin->getDesiredEvents() as $desiredEvent){
				$this->plugin->getServer()->getPluginManager()->registerEvent(
					$desiredEvent,
					\Closure::fromCallable([$itemSkin, "tryCall"]),
					$itemSkin->getPriority(),
					$this->plugin,
				);
			}
		}
	}

	public static function setupItemModReaction(CustomEntityDamageByEntityEvent $event): void {
		$entity = $event->getEntity();
		$damager = $event->getDamager();

		if($entity instanceof Player){
			$event->getEntityCustomEnchantsReactionManager()->set(self::getActivatingItemMods($entity, $event));
		}

		if($damager instanceof Player){
			$event->getDamagerCustomEnchantsReactionManager()->set(self::getActivatingItemMods($damager, $event));
		}
	}

	private static function getActivatingItemMods(Player $player, Event $event): array {
		$class = $event::class;

		/** @var \skyblock\items\customenchants\CustomEnchantInstance[] $v */
		$v = [];
		foreach(array_merge($player->getArmorInventory()->getContents(), [$player->getInventory()->getItemInHand()]) as $item){
			foreach(ItemEditor::getItemMods($item) as $skin){
				$s = self::getInstance()->getItemMod($skin);
				if($s === null) continue;
				if(in_array($class, $s->getDesiredEvents())){
					if(($a = $s->tryCall($event)) !== null && $a !== []){
						$v[] = [$s, $a];
					}
				}
			}
		}

		return $v;
	}
}