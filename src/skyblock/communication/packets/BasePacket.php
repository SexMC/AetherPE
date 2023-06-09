<?php

declare(strict_types=1);

namespace skyblock\communication\packets;

use Closure;
use JsonSerializable;
use skyblock\communication\operations\ClosureStorage;

abstract class BasePacket implements JsonSerializable{

	public string $callbackID = "";

	public function __construct(Closure $closure = null){
		if($closure instanceof Closure){
			ClosureStorage::addClosure($this->callbackID = uniqid(spl_object_hash($closure)), $closure);
		}
	}


	public abstract function getType() : int;

	public function handle(array $data) : void{ }

	public static function create(array $data) : static{
		return new static();
	}

	public function jsonSerialize(){
		return [
			"type" => $this->getType(),
			"callbackID" => $this->callbackID
		];
	}
}