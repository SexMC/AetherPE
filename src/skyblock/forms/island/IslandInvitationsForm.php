<?php

declare(strict_types=1);

namespace skyblock\forms\island;

use Closure;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\player\Player;
use skyblock\islands\Island;
use skyblock\islands\IslandInterface;
use skyblock\islands\upgrades\types\IslandMemberUpgrade;
use skyblock\Main;
use skyblock\sessions\Session;

class IslandInvitationsForm extends MenuForm {

	private array $invitations = [];

	public function __construct(private Session $session){
		$options = [];
		foreach($this->session->getInvitations() as $invitation){
			$options[] = new MenuOption($invitation[0]);
			$this->invitations[] = $invitation;
		}

		parent::__construct("Island Invitations", "Your invitations:", $options, Closure::fromCallable([$this, "handle"]));
	}

	public function handle(Player $player,  int $index) : void{
		$clicked = $this->invitations[$index];

		$island = new Island($clicked[0]);
		if($island->exists()){
			$am = IslandInterface::MEMBER_LIMIT;
			if(($lvl = $island->getIslandUpgrade(IslandMemberUpgrade::getIdentifier())) > 0){
				$am += $lvl;
			}

			if(count($island->getMembers()) >= $am){
				$player->sendMessage("§7You already have §c$am §7members");
				return;
			}

			if(count($island->getMembers()) >= $am){
				$player->sendMessage(Main::PREFIX . "§7island already has §c" . $am . " §7members");
				return;
			}

			$this->session->setIslandName($island->getName());
			$island->addMember($player->getName());
			$island->announce(Main::PREFIX . "§c{$player->getName()}§7 has joined the island!");
		}
	}
}