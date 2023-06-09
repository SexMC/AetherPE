<?php

declare(strict_types=1);

namespace skyblock\items\customenchants;

final class CustomEnchantInstance {

	public function __construct(private BaseCustomEnchant $customEnchant, private int $level){ }

    public function getCustomEnchant() : BaseCustomEnchant{
        return $this->customEnchant;
    }

	public function getLevel() : int{
		return $this->level;
	}
}