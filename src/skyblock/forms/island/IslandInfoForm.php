<?php

declare(strict_types=1);

namespace skyblock\forms\island;

use Closure;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use skyblock\communication\CommunicationData;
use skyblock\islands\Island;
use skyblock\islands\IslandInterface;
use skyblock\islands\upgrades\types\IslandHoppersUpgrade;
use skyblock\islands\upgrades\types\IslandMemberUpgrade;
use skyblock\islands\upgrades\types\IslandSizeUpgrade;
use skyblock\islands\upgrades\types\IslandSpawnersUpgrade;
use skyblock\sessions\Session;
use skyblock\utils\Utils;

class IslandInfoForm extends MenuForm {

	public function __construct(private Island $island){
		if($this->island->isDisbanding()) return;

		//TODO: get data with redis pipelines for optimizing

		$options = [new MenuOption("View other islands"), new MenuOption("§c<- Back")];
		//TODO: warp button and check if island is locked or not if($this->island->getWarp() === null && $this->island->getSetting())

		parent::__construct($island->getName() . "'s info", $this->getText(), $options, Closure::fromCallable([$this, "handle"]));
	}

	public function handle(Player $player, int $button): void {
		switch(strtolower(TextFormat::clean($this->getOption($button)->getText()))) {
			case "<- back":
				$player->sendForm(new IslandForm(new Session($player)));
				break;
			case "view other islands":
				$player->sendForm(new ViewOtherIslandsForm());
				break;
		}
	}

	public function getText(): string {
		$info = [];
		$leader = $this->island->getLeader();
		$members = $this->island->getMembers();

		$info[] = "§7Island Name:§c " . $this->island->getCaseSensitiveName();
		$info[] = "§7Owner: §c" . (Utils::isOnline($this->island->getLeader()) ? "§a$leader" : "§c$leader");
		$str = count($members) . "§7/" . (IslandInterface::MEMBER_LIMIT + ($this->island->getIslandUpgrade(IslandMemberUpgrade::getIdentifier())));
		if(count($members) === 0){
			$info[] = "§cNone";
		} else $info[] = "§7Members (§c{$str}§7): §c" . implode(", ", array_map(fn(string $member): string => (Utils::isOnline($member) ? "§a$member, " : "§c$member, "), $members));

		$info[] = "§7Island Value: §c" . number_format($this->island->getValue());
		$info[] = "§7Island Power: §c" . number_format($this->island->getPower());
		$info[] = "§r";
		$info[] = "§7Bank Money: §c" . number_format($this->island->getBankMoney());
		$info[] = "§7Bank Essence: §c" . number_format($this->island->getBankEssence());
		$info[] = "§7Regular Quest Tokens: §c" . number_format($this->island->getQuestTokens());
		$info[] = "§7Heroic Quest Tokens: §c" . number_format($this->island->getHeroicQuestTokens());
		$info[] = "§r";
		$size = (20 + ($this->island->getIslandUpgrade(IslandSizeUpgrade::getIdentifier()) * 20));
		$info[] = "§7Island Size: §c{$size}x{$size}";
		$info[] = "§7Hopper Limit: §c" . ($this->island->getLimit(IslandInterface::LIMIT_HOPPER)) . "§7/§c" . (20 + ($this->island->getIslandUpgrade(IslandHoppersUpgrade::getIdentifier()) * 20));
		$info[] = "§7Spawner Limit: §c" . ($this->island->getLimit(IslandInterface::LIMIT_SPAWNER)) . "§7/§c" . (25 + ($this->island->getIslandUpgrade(IslandSpawnersUpgrade::getIdentifier()) * 25));
		$info[] = "§7Minion Limit: §c" . ($this->island->getLimit(IslandInterface::LIMIT_MINION)) . "§7/§c" . "35";

		return implode("\n", $info);
	}
}