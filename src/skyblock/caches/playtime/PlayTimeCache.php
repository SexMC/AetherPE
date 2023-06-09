<?php

declare(strict_types=1);

namespace skyblock\caches\playtime;

use pocketmine\utils\SingletonTrait;
use skyblock\traits\InstanceTrait;
use skyblock\traits\StringIntCache;

//local playtime cache
class PlayTimeCache {
	use StringIntCache;
	use InstanceTrait;

	public function __construct(){
		self::$instance = $this;
	}

	public function get(string $key) : int{
		if(isset($this->cache[strtolower($key)])){
			return time() - $this->cache[strtolower($key)];
		}

		return 0;
	}
}