<?php

declare(strict_types=1);

namespace skyblock\communication;

class CommunicationData {
	
	public static array $onlinePlayers = [];
	public static array $warping = [];
	public static array $teleporting = [];
	
	public static function isOnline(string $username): bool {
		return in_array(strtolower($username), self::$onlinePlayers);
	}

	public static function updateOnlinePlayers(array $data): void {
		self::$onlinePlayers = $data;
	}

	public static function getWarpingData(string $player): ?string {
		return self::$warping[strtolower($player)] ?? null;
	}

	public static function setWarpingData(string $player, ?string $island): void {
		self::$warping[strtolower($player)] = $island;
	}

	public static function getOnlinePlayers() : array{
		return self::$onlinePlayers;
	}

	public static function setTeleportingData(string $player, array $data): void {
		self::$teleporting[strtolower($player)] = $data;
	}

	public static function getTeleportingData(string $player): ?array {
		return self::$teleporting[strtolower($player)] ?? null;
	}

}