<?php

declare(strict_types=1);

namespace skyblock\forms\island\management\permissions;

use Closure;
use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Toggle;
use pocketmine\player\Player;
use skyblock\islands\Island;
use skyblock\islands\IslandInterface;
use skyblock\Main;


class IslandEditPermissionsForm extends CustomForm {

	/** @var Island */
	private $island;
	/** @var string */
	private $member;

	public function __construct(Island $island, string $member) {
		if($island->isDisbanding()) return;


		parent::__construct($member, array_map(function (string $perm) use($island, $member) {
			return new Toggle("id-$perm", ucwords(str_replace("_", " ", $perm)), $island->hasPermission($member, $perm));
		}, IslandInterface::ALL_PERMISSIONS), Closure::fromCallable([$this, "handle"]));

		$this->island = $island;
		$this->member = $member;
	}

	public function handle(Player $player, CustomFormResponse $response): void {
		foreach(IslandInterface::ALL_PERMISSIONS as $k => $v){
			$this->island->setPermission($this->member, $v, $response->getBool("id-$v"));
		}

		$player->sendForm(new IslandPermissionsForm($player, $this->island));
		$player->sendMessage(Main::PREFIX . "§7Successfully edited §c{$this->member}§7's permissions");
	}
}