<?php

declare(strict_types=1);

namespace skyblock\utils;

interface Queries {

	const KITS_TABLE_CREATE = "kits.init";
	const KITS_GET = "kits.get";
	const KITS_UPDATE = "kits.update";


	const WORLDS_TABLE_CREATE = "worlds.init";
	const WORLDS_GET = "worlds.get";
	const WORLDS_UPDATE = "worlds.update";
	const WORLDS_DELETE = "worlds.delete";


	const SAFEZONES_TABLE_CREATE = "safezones.init";
	const SAFEZONES_GET_ALL = "safezones.getAll";
	const SAFEZONES_GET = "safezones.get";
	const SAFEZONES_UPDATE = "safezones.update";
	const SAFEZONES_DELETE = "safezones.delete";

	const WARP_TABLE_CREATE = "warps.init";
	const WARP_GET_ALL = "warps.getAll";
	const WARP_GET = "warps.get";
	const WARP_UPDATE = "warps.update";
	const WARP_DELETE = "warps.delete";

	const SELL_LOG = "logs.sell";
	const DEATH_RESTORE_LOG = "logs.death_restore";



	const BOUNTY_TABLE_CREATE = "bounty.init";
	const BOUNTY_SELECT = "bounty.select";
	const BOUNTY_UPDATE = "bounty.update";
	const BOUNTY_CURRENT = "bounty.current";

	const BOUNTY_HISTORY_TABLE_CREATE = "bounty.history.init";
	const BOUNTY_HISTORY_UPDATE = "bounty.history.update";

}