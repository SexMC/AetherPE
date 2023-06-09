<?php

declare(strict_types=1);

namespace skyblock\communication\operations;

use Closure;
use skyblock\utils\Utils;

abstract class BaseOperation {

	protected ?string $identifier = null;

	protected array $data = [];


	public function __construct(Closure $closure = null){
		if($closure instanceof Closure){
			ClosureStorage::addClosure($this->identifier = uniqid(spl_object_hash($closure)), $closure);
			$this->data["callback"] = ["callbackID" => $this->identifier, "requestedFrom" => Utils::getServerName()];
		}

		$this->data["sentFrom"] = Utils::getServerName();
	}

	/**
	 * @return string|null
	 */
	public function getIdentifier() : ?string{
		return $this->identifier;
	}

	abstract public function execute(): array;

}