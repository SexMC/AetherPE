<?php

declare(strict_types=1);

namespace skyblock\items\masks;

interface IMaskHolder{
	public function getMask() : ?Mask;
	public function setMask(string $mask): self;
}