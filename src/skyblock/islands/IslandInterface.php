<?php

declare(strict_types=1);

namespace skyblock\islands;

interface IslandInterface {

	const MEMBER_LIMIT = 12;

	const INVITATION_EXPIRE_TIME = 2 * 60;
	const ISLAND_MAX_ALLY_COUNT = 0;

	const PERMISSION_EDIT_SETTINGS = "edit_settings";
	const PERMISSION_INVITE_PLAYERS = "invite_players";
	const PERMISSION_KICK_MEMBERS = "kick_members";
	const PERMISSION_SET_ISLAND_HOME = "set_island_home";
	const PERMISSION_SET_ISLAND_WARP = "set_island_warp";
	const PERMISSION_EDIT_MEMBER_PERMISSIONS = "edit_member_permissions";

	const PERMISSION_OPEN_CONTAINERS = "open_containers";
	const PERMISSION_KILL_MOBS = "kill_mobs";
	const PERMISSION_BREAK_BLOCKS = "break_blocks";
	const PERMISSION_PLACE_BLOCKS = "place_blocks";
	const PERMISSION_BREAK_SPAWNERS = "break_spawners";
	const PERMISSION_PLACE_SPAWNERS = "place_spawners";

	const MANAGE_PERMISSIONS = [
		self::PERMISSION_EDIT_MEMBER_PERMISSIONS,
		self::PERMISSION_EDIT_SETTINGS,
		self::PERMISSION_KICK_MEMBERS,
		self::PERMISSION_INVITE_PLAYERS,
		self::PERMISSION_SET_ISLAND_HOME,
		self::PERMISSION_SET_ISLAND_WARP
	];

	const ALL_PERMISSIONS = [
		self::PERMISSION_OPEN_CONTAINERS,
		self::PERMISSION_KILL_MOBS,
		self::PERMISSION_PLACE_BLOCKS,
		self::PERMISSION_BREAK_BLOCKS,
		self::PERMISSION_BREAK_SPAWNERS,
		self::PERMISSION_PLACE_SPAWNERS,

		self::PERMISSION_EDIT_MEMBER_PERMISSIONS,
		self::PERMISSION_EDIT_SETTINGS,
		self::PERMISSION_KICK_MEMBERS,
		self::PERMISSION_INVITE_PLAYERS,
		self::PERMISSION_SET_ISLAND_HOME,
		self::PERMISSION_SET_ISLAND_WARP,
	];

	const DEFAULT_PERMISSIONS = [
		self::PERMISSION_OPEN_CONTAINERS => true,
		self::PERMISSION_KILL_MOBS => true,
		self::PERMISSION_PLACE_BLOCKS => true,
		self::PERMISSION_BREAK_BLOCKS => true,
		self::PERMISSION_BREAK_SPAWNERS => false,
		self::PERMISSION_PLACE_SPAWNERS => false,

		self::PERMISSION_EDIT_MEMBER_PERMISSIONS => false,
		self::PERMISSION_EDIT_SETTINGS => false,
		self::PERMISSION_KICK_MEMBERS => false,
		self::PERMISSION_INVITE_PLAYERS => false,
		self::PERMISSION_SET_ISLAND_HOME => false,
		self::PERMISSION_SET_ISLAND_WARP => false,
	];


	const SETTINGS_PICKUP_ITEM = "pickup";
	const SETTINGS_DROP_ITEM = "drop";
	const SETTINGS_MEMBER_PVP = "member_pvp";
	const SETTINGS_LOCKED = "locked";
	const SETTINGS_KILL_MOBS = "kill_mobs";
	const SETTINGS_VIRTUAL_MINERS = "virtual_miners";

	const SETTINGS_ARRAY = [
		self::SETTINGS_PICKUP_ITEM,
		self::SETTINGS_DROP_ITEM,
		self::SETTINGS_KILL_MOBS,
		self::SETTINGS_LOCKED,
		self::SETTINGS_MEMBER_PVP,
		self::SETTINGS_VIRTUAL_MINERS
	];

	const DEFAULT_SETTINGS = [
		self::SETTINGS_PICKUP_ITEM => true,
		self::SETTINGS_DROP_ITEM => true,
		self::SETTINGS_KILL_MOBS => true,
		self::SETTINGS_LOCKED => false,
		self::SETTINGS_MEMBER_PVP => true,
		self::SETTINGS_VIRTUAL_MINERS => false,
	];


	const LIMIT_HOPPER = "hopper";
	const LIMIT_SPAWNER = "spawner";
	const LIMIT_MINION = "minion";



	const UPDATE_BOUNDING_BOX = "update_bb";
}