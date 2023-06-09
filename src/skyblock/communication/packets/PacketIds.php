<?php

namespace skyblock\communication\packets;

interface PacketIds{
	const  UPDATE_STATUS = 0;
	const  CLOSE_CONNECTION = 1;

	const  REQUEST_ISLAND_LOCATION = 2;
	const  RESPONSE_ISLAND_LOCATION = 3;

	const  LOAD_ISLAND = 4;
	const  LOAD_ISLAND_RESPONSE = 5;

	const  OPEN_CONNECTION = 6;

	const  MESSAGE = 7;

	const PLAYER_LOCATION_REQUEST = 8;
	const PLAYER_LOCATION_RESPONSE = 9;

	const PLAYER_LIST_REQUEST = 10;
	const PLAYER_LIST_RESPONSE = 11;

	const EXECUTE_COMMANDS = 12;

	const ISLAND_SET_UNLOADING = 13;
	const PLAYER_TELEPORT_REQUEST = 14;
	const PLAYER_TELEPORT_RESPONSE = 15;
	const PLAYER_UPDATE_DATA = 16;
	const ISLAND_UPDATE_DATA = 17;

	const AUCTION_ADD_REQUEST = 18;
	const AUCTION_ADD_RESPONSE = 19;

	const AUCTION_GETALL_REQUEST = 20;
	const AUCTION_GETALL_RESPONSE = 21;

	const AUCTION_REMOVE_REQUEST = 22;
	const AUCTION_REMOVE_RESPONSE = 23;

	const COINFLIP_ADD_REQUEST = 24;
	const COINFLIP_ADD_RESPONSE = 25;

	const COINFLIP_START_REQUEST = 26;
	const COINFLIP_START_RESPONSE = 27;

	const COINFLIP_GETALL_REQUEST = 28;
	const COINFLIP_GETALL_RESPONSE = 29;

	const COINFLIP_REMOVE_REQUEST = 30;
	const COINFLIP_REMOVE_RESPONSE = 31;
	const COINFLIP_PROGRESS_UPDATE = 32;


	const ADD_BRAG = 33;
	const GET_BRAG = 34;
	const RESPONSE_BRAG = 35;

	const ADD_ITEM = 36;
	const GET_ITEM = 37;
	const RESPONSE_ITEM = 38;
}