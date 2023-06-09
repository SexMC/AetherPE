<?php

declare(strict_types=1);

namespace skyblock\forms\island\unlockables;

use Closure;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;
use RedisClient\Pipeline\PipelineInterface;
use skyblock\Database;
use skyblock\islands\Island;
use skyblock\items\special\types\SpawnerItem;
use skyblock\utils\EntityUtils;

class SpawnerUnlockablesForm extends MenuForm {

	const SPAWNERS = [
		EntityIds::CHICKEN,
		EntityIds::COW,
		EntityIds::ZOMBIE,
		EntityIds::SKELETON,
		EntityIds::BLAZE,
		EntityIds::SLIME,
		EntityIds::IRON_GOLEM,
		EntityIds::ZOMBIE_PIGMAN,
		EntityIds::MAGMA_CUBE,
		EntityIds::GUARDIAN,
		EntityIds::TURTLE,
		SpawnerItem::PIGLIN_BRUTE,
		EntityIds::ZOGLIN,
		EntityIds::RAVAGER,
		SpawnerItem::WARDEN,
	];

	public function __construct(private Island $island){
		parent::__construct("Spawner Unlockables", "", $this->getButtons(), Closure::fromCallable([$this, "handle"]));
	}

	public function handle(Player $player, int $btn): void {
		if($btn === 0){
			$player->sendForm(new IslandUnlockablesForm());
			return;
		}

		$spawner = EntityUtils::getEntityIdFromName(explode("\n", $this->getOption($btn)->getText())[0]);
		$player->sendForm(new SpawnerUnlockablesViewForm($spawner, $this->island));
	}

	public function getButtons(): array {
		$result = Database::getInstance()->getRedis()->pipeline(function(PipelineInterface $pipeline) {
			foreach(self::SPAWNERS as $spawner){
				$pipeline->get("island.{$this->island->getName()}.spawner.$spawner");
			}
		});


		$arr = [new MenuOption("<- Back")];
		foreach($result as $k => $v){
			if(isset(self::SPAWNERS[$k])){
				$name = EntityUtils::getEntityNameFromID(self::SPAWNERS[$k]);

				if((bool) $v === true){
					$arr[] = new MenuOption("$name" . "\n" . "§aUnlocked");
				} else $arr[] = new MenuOption("$name" . "\n" . "§cLocked");
			}
		}


		return $arr;
	}
}