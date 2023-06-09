<?php

declare(strict_types=1);

namespace skyblock\entity\misc\challenges;

use skyblock\traits\AetherSingletonTrait;

class ChallengeHandler {
	use AetherSingletonTrait;


	public function __construct(){
		self::setInstance(self::getInstance());
	}
}
