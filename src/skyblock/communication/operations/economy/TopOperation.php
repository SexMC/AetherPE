<?php

declare(strict_types=1);

namespace skyblock\communication\operations\economy;

use Closure;
use skyblock\communication\http\HttpGetRequest;
use skyblock\communication\http\HttpPostRequest;
use skyblock\communication\operations\BaseOperation;

class TopOperation extends BaseOperation {

	const TYPE_ESSENCE = "essence";
	const TYPE_MONEY = "money";
	const TYPE_FARMING_LEVEL = "farminglevel";
	const TYPE_ISLAND_VALUE = "islandvalue";
	const TYPE_ISLAND_POWER = "islandpower";

	public function __construct(
		private string $type,
		private int $min,
		private int $max,
		Closure $closure
	){
		parent::__construct($closure);
	}

	public function execute() : array{
		$http = new HttpGetRequest("{$this->type}?min={$this->min}&max={$this->max}");
		$http->setBaseURL("http://135.148.150.31:9038/");

		return $http->execute();
	}
}