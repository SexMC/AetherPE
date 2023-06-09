<?php

declare(strict_types=1);

namespace skyblock\items\tools;

use pocketmine\Server;

use skyblock\items\tools\types\pve\DragonMadeBlade;
use skyblock\items\tools\types\pve\DragonMadePickaxe;
use skyblock\items\tools\types\pve\FlintShovel;
use skyblock\items\tools\types\pve\RodOfChampions;
use skyblock\items\tools\types\pve\CleaverSword;
use skyblock\items\tools\types\pve\DreadlordSword;
use skyblock\items\tools\types\pve\ExplosiveBow;
use skyblock\items\tools\types\pve\FarmersRod;
use skyblock\items\tools\types\pve\GolemSword;
use skyblock\items\tools\types\pve\InkWand;
use skyblock\items\tools\types\pve\JungleAxe;
use skyblock\items\tools\types\pve\PigmanSword;
use skyblock\items\tools\types\pve\SavannaBow;
use skyblock\items\tools\types\pve\SpiderSword;
use skyblock\items\tools\types\pve\TreecapacitorAxe;
use skyblock\items\tools\types\pve\YetiSword;
use skyblock\items\tools\types\pve\ZombiePickaxe;
use skyblock\items\tools\types\pve\ZombieSword;
use skyblock\Main;
use skyblock\traits\AetherSingletonTrait;

class SpecialWeaponHandler {
	use AetherSingletonTrait;

	private array $list = [];

	public function __construct(){
		if(self::$instance !== null) return;
		self::setInstance($this);

		//$this->register(new DreadlordSword());
		//$this->register(new YetiSword());
		//$this->register(new CleaverSword());
		//$this->register(new PigmanSword());
		//$this->register(new ZombiePickaxe());
		//$this->register(new ZombieSword());
		//$this->register(new SpiderSword());
		//$this->register(new JungleAxe());
		//$this->register(new TreecapacitorAxe());
		//$this->register(new GolemSword());
		//$this->register(new ExplosiveBow());
		//$this->register(new SavannaBow());
		//$this->register(new FarmersRod());
		//$this->register(new InkWand());
		//$this->register(new RodOfChampions());
		$this->register(new DragonMadePickaxe());
		$this->register(new DragonMadeBlade());
		//$this->register(new FlintShovel());


		//accessory

	}

	public function getWeapon(string $id): ?SpecialWeapon {
		return $this->list[strtolower($id)] ?? null;
	}

	public function register(SpecialWeapon $w): void {
		$this->list[strtolower($w::getName())] = $w;

		if(empty($w->getDesiredEvents()) === false) {
			foreach($w->getDesiredEvents() as $desiredEvent){
				Server::getInstance()->getPluginManager()->registerEvent(
					$desiredEvent,
					\Closure::fromCallable([$w, "tryCall"]),
					$w->getPriority(),
					Main::getInstance(),
				);
			}
		}
	}

	/**
	 * @return array
	 */
	public function getList() : array{
		return $this->list;
	}

}