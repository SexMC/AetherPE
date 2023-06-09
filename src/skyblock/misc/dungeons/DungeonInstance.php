<?php

declare(strict_types=1);

namespace skyblock\misc\dungeons;

use pocketmine\player\Player;
use pocketmine\world\World;

class DungeonInstance {

	/**
	 * @param DungeonFloor $floor
	 * @param Player[]        $participants
	 * @param int          $startTime
	 * @param World        $world
	 */
	public function __construct(private DungeonFloor $floor, private array $participants, private int $startTime, private World $world){

	}

	/**
	 * @return DungeonFloor
	 */
	public function getFloor() : DungeonFloor{
		return $this->floor;
	}

	/**
	 * @return array
	 */
	public function getParticipants() : array{
		return $this->participants;
	}

	/**
	 * @return int
	 */
	public function getStartTime() : int{
		return $this->startTime;
	}

	/**
	 * @return World
	 */
	public function getWorld() : World{
		return $this->world;
	}

	public function isParticipant(Player $player): bool {
		foreach($this->participants as $participant){
			if($participant->getName() === $player->getName()){
				return true;
			}
		}

		return false;
	}

	public function broadcastMessage(string|array $message): void {
		$msg = is_array($message) ? implode("\n", $message) : $message;

		foreach($this->participants as $participant){
			$participant->sendMessage($msg);
		}
	}

	public function title(string $message): void {
		$msg = is_array($message) ? implode("\n", $message) : $message;

		foreach($this->participants as $participant){
			$participant->sendTitle($msg);
		}
	}
}