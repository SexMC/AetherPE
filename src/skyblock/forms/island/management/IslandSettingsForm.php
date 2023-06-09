<?php

namespace skyblock\forms\island\management;

use Closure;
use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Toggle;
use pocketmine\player\Player;
use skyblock\islands\Island;
use skyblock\islands\IslandInterface;

class IslandSettingsForm extends CustomForm {

	public function __construct(private Island $island)
	{
		if($this->island->isDisbanding()) return;
		parent::__construct("Island Settings", $this->getButtons(), Closure::fromCallable([$this, "handle"]));
	}

	public function handle(Player $player, CustomFormResponse $response): void {
		$this->island->setSetting(IslandInterface::SETTINGS_PICKUP_ITEM, $response->getBool("s" . IslandInterface::SETTINGS_PICKUP_ITEM));
		$this->island->setSetting(IslandInterface::SETTINGS_DROP_ITEM, $response->getBool("s" . IslandInterface::SETTINGS_DROP_ITEM));
		$this->island->setSetting(IslandInterface::SETTINGS_LOCKED, $response->getBool("s" . IslandInterface::SETTINGS_LOCKED));
		$this->island->setSetting(IslandInterface::SETTINGS_KILL_MOBS, $response->getBool("s" . IslandInterface::SETTINGS_KILL_MOBS));
		$this->island->setSetting(IslandInterface::SETTINGS_MEMBER_PVP, $response->getBool("s" . IslandInterface::SETTINGS_MEMBER_PVP));
		$this->island->setSetting(IslandInterface::SETTINGS_VIRTUAL_MINERS, $response->getBool("s" . IslandInterface::SETTINGS_VIRTUAL_MINERS));
		$player->sendMessage("§7Successfully updated your island settings");
		$player->sendForm(new IslandManageForm($player, $this->island));
	}

	public function getButtons(): array {
		$array = [];
		$island = $this->island;

		$array[] = new Toggle("s" . IslandInterface::SETTINGS_PICKUP_ITEM, "Visitors can pick up dropped items", $island->getSetting(IslandInterface::SETTINGS_PICKUP_ITEM));
		$array[] = new Toggle("s" . IslandInterface::SETTINGS_DROP_ITEM, "Visitors can drop items", $island->getSetting(IslandInterface::SETTINGS_DROP_ITEM));
		$array[] = new Toggle("s" . IslandInterface::SETTINGS_LOCKED, "Locked (Don't allow visitors)", $island->getSetting(IslandInterface::SETTINGS_LOCKED));
		$array[] = new Toggle("s" . IslandInterface::SETTINGS_KILL_MOBS, "Visitors can kill mobs", $island->getSetting(IslandInterface::SETTINGS_KILL_MOBS));
		$array[] = new Toggle("s" . IslandInterface::SETTINGS_MEMBER_PVP, "Members PvP (members can hit each other in pvp)", $island->getSetting(IslandInterface::SETTINGS_MEMBER_PVP));
		$array[] = new Toggle("s" . IslandInterface::SETTINGS_VIRTUAL_MINERS, "Virtual Miners (for low end devices)", $island->getSetting(IslandInterface::SETTINGS_VIRTUAL_MINERS));

		return $array;
	}
}