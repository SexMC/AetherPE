<?php

declare(strict_types=1);

namespace skyblock\communication\packets;

use skyblock\communication\datatypes\ServerInformation;
use skyblock\communication\operations\mechanics\BragAddOperation;
use skyblock\communication\packets\types\CloseConnectionPacket;
use skyblock\communication\packets\types\island\IslandLoadRequestPacket;
use skyblock\communication\packets\types\island\IslandLoadResponsePacket;
use skyblock\communication\packets\types\island\IslandLocationRequestPacket;
use skyblock\communication\packets\types\island\IslandLocationResponsePacket;
use skyblock\communication\packets\types\island\IslandSetUnloadingPacket;
use skyblock\communication\packets\types\island\IslandUpdateDataPacket;
use skyblock\communication\packets\types\mechanics\auctionhouse\AuctionAddRequestPacket;
use skyblock\communication\packets\types\mechanics\auctionhouse\AuctionAddResponsePacket;
use skyblock\communication\packets\types\mechanics\auctionhouse\AuctionGetAllRequest;
use skyblock\communication\packets\types\mechanics\auctionhouse\AuctionGetAllResponse;
use skyblock\communication\packets\types\mechanics\auctionhouse\AuctionRemoveRequestPacket;
use skyblock\communication\packets\types\mechanics\auctionhouse\AuctionRemoveResponsePacket;
use skyblock\communication\packets\types\mechanics\brag\AddBragPacket;
use skyblock\communication\packets\types\mechanics\item\AddItemPacket;
use skyblock\communication\packets\types\mechanics\brag\BragResponsePacket;
use skyblock\communication\packets\types\mechanics\brag\GetBragPacket;
use skyblock\communication\packets\types\mechanics\item\GetItemPacket;
use skyblock\communication\packets\types\mechanics\coinflip\CoinflipAddRequestPacket;
use skyblock\communication\packets\types\mechanics\coinflip\CoinflipAddResponsePacket;
use skyblock\communication\packets\types\mechanics\coinflip\CoinflipGetRequestPacket;
use skyblock\communication\packets\types\mechanics\coinflip\CoinflipGetResponsePacket;
use skyblock\communication\packets\types\mechanics\coinflip\CoinflipProgressUpdatePacket;
use skyblock\communication\packets\types\mechanics\coinflip\CoinflipRemoveRequestPacket;
use skyblock\communication\packets\types\mechanics\coinflip\CoinflipRemoveResponsePacket;
use skyblock\communication\packets\types\mechanics\coinflip\CoinflipStartRequestPacket;
use skyblock\communication\packets\types\mechanics\coinflip\CoinflipStartResponsePacket;
use skyblock\communication\packets\types\mechanics\item\ItemResponsePacket;
use skyblock\communication\packets\types\OpenConnectionPacket;
use skyblock\communication\packets\types\player\FindPlayerLocationRequestPacket;
use skyblock\communication\packets\types\player\FindPlayerLocationResponsePacket;
use skyblock\communication\packets\types\player\PlayerTeleportRequestPacket;
use skyblock\communication\packets\types\player\PlayerTeleportResponsePacket;
use skyblock\communication\packets\types\player\PlayerUpdateDataPacket;
use skyblock\communication\packets\types\server\ExecuteCommandPacket;
use skyblock\communication\packets\types\server\PlayerListRequestPacket;
use skyblock\communication\packets\types\server\PlayerListResponsePacket;
use skyblock\communication\packets\types\server\ServerMessagePacket;

class PacketRegistry{

	public static function getPacketByType(int $type) : ?string{
		return match ($type) {
			PacketIds::UPDATE_STATUS => ServerInformation::class,
			PacketIds::OPEN_CONNECTION => OpenConnectionPacket::class,
			PacketIds::CLOSE_CONNECTION => CloseConnectionPacket::class,
			PacketIds::REQUEST_ISLAND_LOCATION => IslandLocationRequestPacket::class,
			PacketIds::RESPONSE_ISLAND_LOCATION => IslandLocationResponsePacket::class,
			PacketIds::LOAD_ISLAND_RESPONSE => IslandLoadResponsePacket::class,
			PacketIds::LOAD_ISLAND => IslandLoadRequestPacket::class,
			PacketIds::MESSAGE => ServerMessagePacket::class,

			PacketIds::PLAYER_LOCATION_RESPONSE => FindPlayerLocationResponsePacket::class,
			PacketIds::PLAYER_LOCATION_REQUEST => FindPlayerLocationRequestPacket::class,

			PacketIds::PLAYER_LIST_RESPONSE => PlayerListResponsePacket::class,
			PacketIds::PLAYER_LIST_REQUEST => PlayerListRequestPacket::class,
			PacketIds::EXECUTE_COMMANDS => ExecuteCommandPacket::class,
			PacketIds::ISLAND_SET_UNLOADING => IslandSetUnloadingPacket::class,
			PacketIds::PLAYER_TELEPORT_REQUEST => PlayerTeleportRequestPacket::class,
			PacketIds::PLAYER_TELEPORT_RESPONSE => PlayerTeleportResponsePacket::class,
			PacketIds::PLAYER_UPDATE_DATA => PlayerUpdateDataPacket::class,

			PacketIds::AUCTION_ADD_REQUEST => AuctionAddRequestPacket::class,
			PacketIds::AUCTION_ADD_RESPONSE => AuctionAddResponsePacket::class,
			PacketIds::AUCTION_REMOVE_RESPONSE => AuctionRemoveResponsePacket::class,
			PacketIds::AUCTION_REMOVE_REQUEST => AuctionRemoveRequestPacket::class,
			PacketIds::AUCTION_GETALL_RESPONSE => AuctionGetAllResponse::class,
			PacketIds::AUCTION_GETALL_REQUEST => AuctionGetAllRequest::class,

			PacketIds::COINFLIP_GETALL_REQUEST => CoinflipGetRequestPacket::class,
			PacketIds::COINFLIP_GETALL_RESPONSE => CoinflipGetResponsePacket::class,
			PacketIds::COINFLIP_START_REQUEST => CoinflipStartRequestPacket::class,
			PacketIds::COINFLIP_START_RESPONSE => CoinflipStartResponsePacket::class,
			PacketIds::COINFLIP_REMOVE_REQUEST => CoinflipRemoveRequestPacket::class,
			PacketIds::COINFLIP_REMOVE_RESPONSE => CoinflipRemoveResponsePacket::class,
			PacketIds::COINFLIP_ADD_REQUEST => CoinflipAddRequestPacket::class,
			PacketIds::COINFLIP_ADD_RESPONSE => CoinflipAddResponsePacket::class,
			PacketIds::COINFLIP_PROGRESS_UPDATE => CoinflipProgressUpdatePacket::class,
			PacketIds::ISLAND_UPDATE_DATA => IslandUpdateDataPacket::class,

			PacketIds::ADD_BRAG => AddBragPacket::class,
			PacketIds::GET_BRAG => GetBragPacket::class,
			PacketIds::RESPONSE_BRAG => BragResponsePacket::class,

			PacketIds::ADD_ITEM => AddItemPacket::class,
			PacketIds::GET_ITEM => GetItemPacket::class,
			PacketIds::RESPONSE_ITEM => ItemResponsePacket::class,


			default => null
		};
	}
}