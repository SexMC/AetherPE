<?php

declare(strict_types=1);

namespace skyblock\items\customenchants;

use pocketmine\inventory\CreativeInventory;
use skyblock\items\customenchants\types\Angler;
use skyblock\items\customenchants\types\Autosmelt;
use skyblock\items\customenchants\types\BaneOfArthropods;
use skyblock\items\customenchants\types\BlastProtection;
use skyblock\items\customenchants\types\Caster;
use skyblock\items\customenchants\types\Cleave;
use skyblock\items\customenchants\types\Critical;
use skyblock\items\customenchants\types\Cubism;
use skyblock\items\customenchants\types\Efficiency;
use skyblock\items\customenchants\types\EnderSlayer;
use skyblock\items\customenchants\types\Execute;
use skyblock\items\customenchants\types\Experience;
use skyblock\items\customenchants\types\FirstStrike;
use skyblock\items\customenchants\types\Growth;
use skyblock\items\customenchants\types\Harvesting;
use skyblock\items\customenchants\types\Knockback;
use skyblock\items\customenchants\types\Lure;
use skyblock\items\customenchants\types\Magnet;
use skyblock\items\customenchants\types\Power;
use skyblock\items\customenchants\types\Protection;
use skyblock\items\customenchants\types\Punch;
use skyblock\items\customenchants\types\Rainbow;
use skyblock\items\customenchants\types\Respiration;
use skyblock\items\customenchants\types\Scavenger;
use skyblock\items\customenchants\types\Scotopia;
use skyblock\items\customenchants\types\Sharpness;
use skyblock\items\customenchants\types\SilkTouch;
use skyblock\items\customenchants\types\Smite;
use skyblock\items\customenchants\types\Thunderlord;
use skyblock\items\SkyblockItems;
use skyblock\traits\AetherHandlerTrait;


class CustomEnchantFactory{
	use AetherHandlerTrait;

	private array $list = [];

	public function onEnable() : void{
		$arr = [
			new Scotopia(),
			new Harvesting(),
			new Cubism(),
			new Autosmelt(),
			new Execute(),
			new Critical(),
			new Scavenger(),
			new Rainbow(),
			new EnderSlayer(),
			new Power(),
			new Smite(),
			new SilkTouch(),
			new BaneOfArthropods(),
			new Lure(),
			new Protection(),
			new BlastProtection(),
			new Thunderlord(),
			new Efficiency(),
			new Experience(),
			new Knockback(),
			new Punch(),
			new Growth(),
			new Respiration(),
			new Magnet(),
			new Cleave(),
			new Caster(),
			new Angler(),
			new Sharpness(),
			new FirstStrike(),
		];

		foreach($arr as $v){
			$this->register($v);
		}
	}


	public function get(string $name) : ?BaseCustomEnchant{
		return $this->list[str_replace(" ", "_", strtolower($name))] ?? null;
	}

	public function register(BaseCustomEnchant $item) : void{
		$this->list[str_replace(" ", "_", strtolower($item->getIdentifier()->getId()))] = $item;

		foreach(range(1, $item->getMaxLevel()) as $lvl){
			CreativeInventory::getInstance()->add((SkyblockItems::ENCHANTMENT_BOOK())->addCustomEnchant(new CustomEnchantInstance($item, $lvl)));
		}
	}

	public function getList() : array{
		return $this->list;
	}
}