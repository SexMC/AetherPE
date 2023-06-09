<?php

declare(strict_types=1);

namespace skyblock\misc\collection;

use skyblock\misc\collection\combat\CaveSpiderCollection;
use skyblock\misc\collection\combat\ChickenCollection;
use skyblock\misc\collection\combat\CreeperCollection;
use skyblock\misc\collection\combat\EndermanCollection;
use skyblock\misc\collection\combat\PigCollection;
use skyblock\misc\collection\combat\SheepCollection;
use skyblock\misc\collection\combat\SkeletonCollection;
use skyblock\misc\collection\combat\SlimeCollection;
use skyblock\misc\collection\combat\SpiderCollection;
use skyblock\misc\collection\combat\ZombieCollection;
use skyblock\misc\collection\farming\CarrotCollection;
use skyblock\misc\collection\farming\MelonCollection;
use skyblock\misc\collection\farming\PotatoCollection;
use skyblock\misc\collection\farming\PumpkinCollection;
use skyblock\misc\collection\farming\SugarcaneCollection;
use skyblock\misc\collection\farming\WheatCollection;
use skyblock\misc\collection\fishing\ClayCollection;
use skyblock\misc\collection\fishing\ClownFishCollection;
use skyblock\misc\collection\fishing\InkSacCollection;
use skyblock\misc\collection\fishing\LilyPadCollection;
use skyblock\misc\collection\fishing\PufferfishCollection;
use skyblock\misc\collection\fishing\RawFishCollection;
use skyblock\misc\collection\foraging\AcaciaCollection;
use skyblock\misc\collection\foraging\BirchCollection;
use skyblock\misc\collection\foraging\DarkOakCollection;
use skyblock\misc\collection\foraging\JungleCollection;
use skyblock\misc\collection\foraging\OakCollection;
use skyblock\misc\collection\foraging\SpruceCollection;
use skyblock\misc\collection\mining\CoalCollection;
use skyblock\misc\collection\mining\CobblestoneCollection;
use skyblock\misc\collection\mining\DiamondCollection;
use skyblock\misc\collection\mining\EmeraldCollection;
use skyblock\misc\collection\mining\GoldCollection;
use skyblock\misc\collection\mining\GravelCollection;
use skyblock\misc\collection\mining\IronCollection;
use skyblock\misc\collection\mining\LapisCollection;
use skyblock\misc\collection\mining\ObsidianCollection;
use skyblock\misc\collection\mining\RedstoneCollection;
use skyblock\traits\AetherHandlerTrait;

class CollectionHandler {
	use AetherHandlerTrait;

	/** @var array<string, Collection[]>  */
	private array $collections = [];

	public array $collectionByItems = [];

	public function onEnable() : void{
		$this->registerCollection("farming", new CarrotCollection());
		$this->registerCollection("farming", new MelonCollection());
		$this->registerCollection("farming", new PotatoCollection());
		$this->registerCollection("farming", new PumpkinCollection());
		$this->registerCollection("farming", new SugarcaneCollection());
		$this->registerCollection("farming", new WheatCollection());


		$this->registerCollection("mining", new CoalCollection());
		$this->registerCollection("mining", new CobblestoneCollection());
		$this->registerCollection("mining", new DiamondCollection());
		$this->registerCollection("mining", new GoldCollection());
		$this->registerCollection("mining", new IronCollection());
		$this->registerCollection("mining", new RedstoneCollection());
		$this->registerCollection("mining", new LapisCollection());
		$this->registerCollection("mining", new GravelCollection());
		$this->registerCollection("mining", new EmeraldCollection());
		$this->registerCollection("mining", new ObsidianCollection());
		//TODO: emerald collection

		$this->registerCollection("combat", new ZombieCollection());
		$this->registerCollection("combat", new SpiderCollection());
		$this->registerCollection("combat", new CaveSpiderCollection());
		$this->registerCollection("combat", new SlimeCollection());
		$this->registerCollection("combat", new SkeletonCollection());
		$this->registerCollection("combat", new SheepCollection());
		$this->registerCollection("combat", new PigCollection());
		$this->registerCollection("combat", new EndermanCollection());
		$this->registerCollection("combat", new CreeperCollection());
		$this->registerCollection("combat", new ChickenCollection());

		$this->registerCollection("foraging", new OakCollection());
		$this->registerCollection("foraging", new AcaciaCollection());
		$this->registerCollection("foraging", new BirchCollection());
		$this->registerCollection("foraging", new DarkOakCollection());
		$this->registerCollection("foraging", new JungleCollection());
		$this->registerCollection("foraging", new SpruceCollection());

		$this->registerCollection("fishing", new ClayCollection());
		$this->registerCollection("fishing", new ClownFishCollection());
		$this->registerCollection("fishing", new PufferfishCollection());
		$this->registerCollection("fishing", new InkSacCollection());
		$this->registerCollection("fishing", new RawFishCollection());
		$this->registerCollection("fishing", new LilyPadCollection());
	}

	public function registerCollection(string $type, Collection $collection): void {
		if(!isset($this->collections[$type])){
			$this->collections[$type] = [];
		}

		$this->collections[$type][] = $collection;

		$this->collectionByItems[$collection->getItem()->getId() . $collection->getItem()->getMeta()] = $collection;
	}

	public function getCollectionType(string $type): array {
		return $this->collections[$type] ?? [];
	}

	public function getCategoryByCollection(Collection $collection): string {
		foreach($this->collections as $k => $v){
			foreach($v as $c){
				if($c->getName() === $collection->getName()){
					return $k;
				}
			}
		}

		return "";
	}

	public function getAllCollections(): array {
		return $this->collections;
	}
}