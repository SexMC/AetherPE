<?php

declare(strict_types=1);

namespace skyblock\sessions;

trait SessionRawData {
	public function getBool(string $id): bool {
		return (bool) ($this->getRedis()->get("player.{$this->username}.raw.bool.$id") ?? false);
	}

	public function setBool(string $id, bool $value): void {
		$this->getRedis()->set("player.{$this->username}.raw.bool.$id", $value);
	}
}