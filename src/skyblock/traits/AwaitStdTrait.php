<?php

declare(strict_types=1);

namespace skyblock\traits;

use JetBrains\PhpStorm\Pure;
use skyblock\Main;
use SOFe\AwaitStd\AwaitStd;

trait AwaitStdTrait {

	#[Pure] public function getStd(): AwaitStd {
		return Main::getInstance()->getStd();
	}
}