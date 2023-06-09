<?php

declare(strict_types=1);

namespace skyblock\items\misc\storagesack;

use pocketmine\item\VanillaItems;

class HusbandryStorageSack extends StorageSack {

	public function buildStorageList() : array{
		return [
			VanillaItems::FEATHER(),
			VanillaItems::LEATHER(),
			VanillaItems::RAW_MUTTON(),
			VanillaItems::RAW_PORKCHOP(),
			VanillaItems::RAW_CHICKEN(),
			VanillaItems::RAW_BEEF(),
			VanillaItems::RAW_RABBIT(),
			VanillaItems::RABBIT_FOOT(),
			VanillaItems::RABBIT_HIDE(),
		];
	}

	public function getTypeName() : string{
		return "Husbandry";
	}
}