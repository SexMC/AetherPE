<?php

declare(strict_types=1);

namespace skyblock\sessions;

use pocketmine\permission\PermissionManager;
use pocketmine\Server;
use skyblock\communication\CommunicationLogicHandler;
use skyblock\communication\packets\types\player\PlayerUpdateDataPacket;
use skyblock\Main;
use skyblock\player\AetherPlayer;
use skyblock\player\ranks\BaseRank;
use skyblock\player\ranks\RankHandler;

trait SessionRankData {

	/**
	 * @return BaseRank[]
	 */
	public function getRanks(): array {
		$data = $this->getRedis()->lrange("player.{$this->username}.ranks", 0, -1);

		if($data === "" || $data === null){
			return [RankHandler::getInstance()->getRank("traveler")->setIsPerm(true)];
		}

		$ranks = [];
		foreach($data as $raw){
			$v = json_decode($raw, true);

			if(($r = RankHandler::getInstance()->getRank(strtolower($v[0])))){
				$rank = clone $r;

				//if $v[1] is 1 then it's a perm rank, if it's 0 then it's a seasonal rank
				$rank->setIsPerm($v[1] === 1);
				$ranks[] = $rank;
			}
		}

		if(empty($ranks)){
			return [RankHandler::getInstance()->getRank("traveler")->setIsPerm(true)];
		}

		return $ranks;
	}

	public function hasRank(BaseRank $rank): bool {
		$data = $this->getRedis()->lrange("player.{$this->username}.ranks", 0, -1);

		if($data === "" || $data === null){
			return false;
		}


		foreach($data as $raw){
			$v = json_decode($raw, true);

			if(strtolower($v[0]) === strtolower($rank->getName())){
				return true;
			}
		}

		return false;
	}


	public function addRank(BaseRank $rank, bool $permanent): void {
		$this->getRedis()->lpush("player.{$this->username}.ranks", json_encode([$rank->getName(), (int) $permanent]));

		$this->updatePerms();
	}

	public function removeRank(BaseRank $rank): bool {
		$removed = $this->getRedis()->lrem("player.{$this->username}.ranks", 3, json_encode([$rank->getName(), 0]));

		if($removed <= 0){
			$removed = $this->getRedis()->lrem("player.{$this->username}.ranks", 3, json_encode([$rank->getName(), 1]));
		}

		if($removed > 0) {
			$this->updatePerms();;
		}

		return $removed > 0;
	}

	public function getTopRank(bool $nonStaff = false): BaseRank {
		$ranks = $this->getRanks();

		$last = -1;
		$lastRank = RankHandler::getInstance()->getRank("traveler");
		foreach($ranks as $rank){
			if($nonStaff){
				if($rank->isStaff()) continue;
			}

			if($rank->getTier() > $last){
				$last = $rank->getTier();
				$lastRank = $rank;
			}
		}


		return $lastRank;
	}
	
	public function updatePerms(): void {
		$p = $this->getPlayer();
		if($p instanceof AetherPlayer){
			$attachment = $p->getAttachment() ?? $p->addAttachment(Main::getInstance());
			$attachment->clearPermissions();

			$top = $this->getTopRank();
            if (array_search('skyblock.permissions.admin', $top->getPermissions()) !== false) {
                foreach (PermissionManager::getInstance()->getPermissions() as $permission) {
                    $attachment->setPermission($permission, true);
                }
            }

            $permissions = [];
            foreach ($this->getRanks() as $rank) {
                foreach ($rank->getPermissions() as $permission) {
                    if (!isset($permissions[$permission])) {
                        $permissions[$permission] = true;
                    }
                }
            }

            $attachment->setPermissions($permissions);
			$p->setRank($top->getColour() . $top->getName());
			$p->setAttachment($attachment);
		} else{
			CommunicationLogicHandler::getInstance()->sendPacket(new PlayerUpdateDataPacket($this->username, PlayerUpdateDataPacket::UPDATE_PERMS));
		}
	}
}