<?php

declare(strict_types=1);

namespace skyblock\caches\skin;

use pocketmine\entity\Skin;
use skyblock\Main;
use skyblock\traits\InstanceTrait;
use skyblock\utils\EntityUtils;
use skyblock\utils\Utils;

class SkinCache {
	use InstanceTrait;

	/** @var Skin[] */
	private array $cache = [];

	public static $geo;

	public function __construct(){
		self::$instance = $this;

		foreach(json_decode(file_get_contents(Main::getInstance()->getDataFolder() . "skins/masks/geometry.json"), true) as $k => $v){
			$this->loadFromBase64EncodedFile($k, "skins/masks/geometry.json", "skins/masks/skins.json", $k);
		}

		$this->loadSkin(
			"Stacked Books",
			"skins/blocks/Stacked_Books.geo.json",
			"skins/blocks/Stacked_Books.png",
			"Stacked_Books"
		);

		$this->loadSkin(
			"Vending Machine",
			"skins/blocks/Vending_Machine.geo.json",
			"skins/blocks/Vending_Machine.png",
			"Vending_Machine"
		);

		$this->loadSkin("patrick", "skins/sea/patrick.json", "skins/sea/patrick.png", "patrick");
		$this->loadSkin("seal", "skins/sea/seal.json", "skins/sea/seal.png", "seal");
		$this->loadSkin("gible", "skins/sea/gible.json", "skins/sea/gible.png", "gible");

		$this->loadSkin(
			"Joseph",
			"skins/blocks/Vending_Machine.geo.json",
			"skins/blocks/Vending_Machine.png",
			"Vending_Machine"
		);

		$this->loadSkinAsMask("Astronaut", "skins/masks/Astronout.png",);
		$this->loadSkinAsMask("Vladimir", "skins/masks/Vladimir.png",);
		$this->loadSkinAsMask("Bubble", "skins/masks/Bubble.png",);
		$this->loadSkinAsMask("Blaze", "skins/masks/Blaze.png",);
		$this->loadSkinAsMask("Banana", "skins/masks/Banana.png",);
		$this->loadSkinAsMask("Zed", "skins/masks/Zed.png",);
		$this->loadSkinAsMask("Yasuo", "skins/masks/Yasou.png",);
		$this->loadSkinAsMask("Jax", "skins/masks/Jax.png",);
		$this->loadSkinAsMask("Tahm", "skins/masks/Tahm.png",);
		$this->loadSkinAsMask("Clyde", "skins/masks/Clyde.png",);

	}
	
	public function loadSkin(string $name, string $geometryPath, string $skinPath, string $geometryName): void {
		$this->cache[strtolower($name)] = EntityUtils::getSkin($geometryPath, $skinPath, $geometryName);
		Main::debug("Loaded skin $name (skin: $skinPath, geometry: $geometryPath, geometry name: $geometryName)");
	}

	public function loadSkinAsMask(string $name, string $skinPath): void {
		$this->cache[strtolower($name)] =
			new Skin("Standard_Custom", EntityUtils::getSkinAsRaw($skinPath), "", "geometry.custom.mask_wearer", SkinCache::$geo);

		Main::debug("Loaded skin $name as mask (skin: $skinPath)");
	}

	public function loadFromBase64EncodedFile(string $name, string $geometryPath, string $skinPath, string $key): void {
		$geoData = base64_decode(json_decode(file_get_contents(Main::getInstance()->getDataFolder() . $geometryPath), true)[$key]);
		$skinData = base64_decode(json_decode(file_get_contents(Main::getInstance()->getDataFolder() . $skinPath), true)[$key]);
		$this->cache[strtolower($key)] = new Skin("Standard_Custom", $skinData, "", "geometry.custom.mask_wearer", $geoData);
		self::$geo = $geoData;
		Main::debug("Loaded skin $name");
	}

	/**
	 * @return Skin[]
	 */
	public function getCache() : array{
		return $this->cache;
	}

	public function getSkin(string $name): ?Skin {
		return $this->cache[strtolower($name)] ?? null;
	}

}
