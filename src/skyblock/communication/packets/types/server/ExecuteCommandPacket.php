<?php

declare(strict_types=1);

namespace skyblock\communication\packets\types\server;

use Closure;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\lang\Language;
use pocketmine\Server;
use skyblock\communication\packets\BasePacket;
use skyblock\communication\packets\PacketIds;

class ExecuteCommandPacket extends BasePacket{

	public function __construct(public array $commands, Closure $closure = null){
		parent::__construct($closure);
	}

	public function handle(array $data) : void{
		$sender = new ConsoleCommandSender(Server::getInstance(), new Language(Language::FALLBACK_LANGUAGE));
		foreach($this->commands as $command){
			Server::getInstance()->dispatchCommand($sender, $command);
		}
	}

	public static function create(array $data) : static{
		return new self($data["commands"]);
	}

	public function jsonSerialize(){
		$r = parent::jsonSerialize();
		$r["commands"] = $this->commands;

		return $r;
	}


	public function getType() : int{
		return PacketIds::EXECUTE_COMMANDS;
	}
}