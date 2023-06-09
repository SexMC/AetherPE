<?php

declare(strict_types=1);

namespace skyblock\items\customenchants;

interface ICustomEnchantable{

	public function getCustomEnchants() : array;

	public function addCustomEnchant(CustomEnchantInstance $instance) : self;

	public function removeCustomEnchant(BaseCustomEnchant $instance) : void;

	public function hasCustomEnchant(string $id) : bool;
}