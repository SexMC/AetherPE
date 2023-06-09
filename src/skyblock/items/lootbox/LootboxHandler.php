<?php

declare(strict_types=1);

namespace skyblock\items\lootbox;

use skyblock\items\lootbox\types\abandoned\CommonAbandonedLootbox;
use skyblock\items\lootbox\types\abandoned\RareAbandonedLootbox;
use skyblock\items\lootbox\types\armor\ArmorSetPieceGeneratorLootbox;
use skyblock\items\lootbox\types\ArmorOrArmourLootbox;
use skyblock\items\lootbox\types\AstronoutPackageLootbox;
use skyblock\items\lootbox\types\attribute\EnhancementAttributeBoosterGenerator;
use skyblock\items\lootbox\types\booster\MysteryFarmingXpBoosterLootbox;
use skyblock\items\lootbox\types\booster\MysteryIslandXpBoosterLootbox;
use skyblock\items\lootbox\types\booster\MysteryXpBoosterLootbox;
use skyblock\items\lootbox\types\BundleOfJoyLootbox;
use skyblock\items\lootbox\types\BundleOfStressLootbox;
use skyblock\items\lootbox\types\CosmonoutPapersLootbox;
use skyblock\items\lootbox\types\DarkAgeLootbox;
use skyblock\items\lootbox\types\destruction\HeroicDestructionEssenceLootbox;
use skyblock\items\lootbox\types\essence\EssenceLootboxT1;
use skyblock\items\lootbox\types\essence\EssenceLootboxT2;
use skyblock\items\lootbox\types\expander\GodlyMysteryEnchantmentExpanderLootbox;
use skyblock\items\lootbox\types\expander\MutatedEnchantmentOrbGeneratorLootbox;
use skyblock\items\lootbox\types\expander\MysteryAmuletExpanderLootbox;
use skyblock\items\lootbox\types\expander\MysteryBackpackExpanderLootbox;
use skyblock\items\lootbox\types\expander\MysteryBeltExpanderLootbox;
use skyblock\items\lootbox\types\expander\MysteryDoubleAmuletExpanderLootbox;
use skyblock\items\lootbox\types\expander\MysteryDoubleBackpackExpander;
use skyblock\items\lootbox\types\expander\MysteryDoubleBeltExpanderLootbox;
use skyblock\items\lootbox\types\expander\MysteryDoubleItemModExpander;
use skyblock\items\lootbox\types\expander\MysteryEnchantmentExpanderLootbox;
use skyblock\items\lootbox\types\expander\MysteryItemModExpanderLootbox;
use skyblock\items\lootbox\types\farming\Level100Lootbox;
use skyblock\items\lootbox\types\farming\Level10Lootbox;
use skyblock\items\lootbox\types\farming\Level20Lootbox;
use skyblock\items\lootbox\types\farming\Level30Lootbox;
use skyblock\items\lootbox\types\farming\Level40Lootbox;
use skyblock\items\lootbox\types\farming\Level50Lootbox;
use skyblock\items\lootbox\types\farming\Level60Lootbox;
use skyblock\items\lootbox\types\farming\Level85Lootbox;
use skyblock\items\lootbox\types\FishingLootbox;
use skyblock\items\lootbox\types\FishingLoottableLootbox;
use skyblock\items\lootbox\types\HeroicKothLootbox;
use skyblock\items\lootbox\types\IntergalacticAgentLootbox;
use skyblock\items\lootbox\types\IntergalacticChocolateLootbox;
use skyblock\items\lootbox\types\itemmod\BubbleItemModsLootbox;
use skyblock\items\lootbox\types\itemmod\ItemModBundleLootbox;
use skyblock\items\lootbox\types\itemmod\OverallMutatedItemModGeneratorLootbox;
use skyblock\items\lootbox\types\itemmod\SpaceItemModsLootbox;
use skyblock\items\lootbox\types\itemmod\TurkeyItemModGeneratorLootbox;
use skyblock\items\lootbox\types\itemmod\WardenItemModGeneratorLootbox;
use skyblock\items\lootbox\types\JealousYetLootbox;
use skyblock\items\lootbox\types\JosephsRemainsLootbox;
use skyblock\items\lootbox\types\KothLootbox;
use skyblock\items\lootbox\types\LuckyBlockLoottableLootbox;
use skyblock\items\lootbox\types\mask\MaskGeneratorLootbox;
use skyblock\items\lootbox\types\MemoryLaneLootbox;
use skyblock\items\lootbox\types\MemoryLaneLootboxGenerator;
use skyblock\items\lootbox\types\MysteryLootbox;
use skyblock\items\lootbox\types\MysteryRankGeneratorLootbox;
use skyblock\items\lootbox\types\NeptuneLoveLootbox;
use skyblock\items\lootbox\types\pets\CreatureGeneratorLootbox;
use skyblock\items\lootbox\types\pets\MysteryPetLootbox;
use skyblock\items\lootbox\types\quest\HeroicQuestTokenGeneratorLootbox;
use skyblock\items\lootbox\types\quest\QuestTokenGeneratorLootbox;
use skyblock\items\lootbox\types\quest\RerollOrbGeneratorLootbox;
use skyblock\items\lootbox\types\rank\AetherPlusLootbox;
use skyblock\items\lootbox\types\rank\AstronomicalLootbox;
use skyblock\items\lootbox\types\SellWandGeneratorLootbox;
use skyblock\items\lootbox\types\skit\EnchanterSkitLootbox;
use skyblock\items\lootbox\types\skit\MadScientistSkitLootbox;
use skyblock\items\lootbox\types\skit\MysterySkitLootbox;
use skyblock\items\lootbox\types\slots\SlotBotTicketGenerator;
use skyblock\items\lootbox\types\slots\SlotsLootbox;
use skyblock\items\lootbox\types\SpawnerLootbox;
use skyblock\items\lootbox\types\store\April2022AetherCrate;
use skyblock\items\lootbox\types\store\EarlySummer2022AetherCrate;
use skyblock\items\lootbox\types\store\EnhancementAttributeLootbox;
use skyblock\items\lootbox\types\store\June2022AetherCrate;
use skyblock\items\lootbox\types\store\May2022AetherCrate;
use skyblock\items\lootbox\types\attribute\MysteryHoteiAttributeGenerator;
use skyblock\items\lootbox\types\attribute\MysteryJurojinAttributeGenerator;
use skyblock\items\lootbox\types\store\NeptuneResetOneAetherCrate;
use skyblock\items\lootbox\types\store\PlanetNeptuneLootbox;
use skyblock\items\lootbox\types\TestAetherCrate;
use skyblock\items\lootbox\types\ThatHasToBeAGoodDeathLootbox;
use skyblock\items\lootbox\types\TotemCustomBlockGeneratorLootbox;
use skyblock\items\lootbox\types\UzisTreasureLootbox;
use skyblock\items\lootbox\types\VoteLootbox;
use skyblock\items\lootbox\types\WardensVaultLootbox;
use skyblock\traits\InstanceTrait;

class LootboxHandler {

	use InstanceTrait;

	/** @var Lootbox[] */
	private array $lootboxes = [];

	public function __construct(){
		self::$instance = $this;
	}

	public function registerLootbox(Lootbox $lootbox): void {
		$this->lootboxes[strtolower($lootbox::getName())] = $lootbox;
	}


	/**
	 * @return Lootbox[]
	 */
	public function getLootboxes() : array{
		return $this->lootboxes;
	}

	public function getLootbox(string $lb): ?Lootbox {
		return $this->lootboxes[strtolower($lb)] ?? null;
	}
}