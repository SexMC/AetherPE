<?php

declare(strict_types=1);

namespace skyblock\sessions;

trait SessionCollectionData {

	public function getCollectionLevel(string $collection): int {
		return (int) ($this->getRedis()->get("player.{$this->username}.collection.level.$collection") ?? 0);
	}

	public function getCollectionCount(string $collection): int {
		return (int) ($this->getRedis()->get("player.{$this->username}.collection.count.$collection") ?? 0);
	}

	public function setCollectionLevel(string $collection, int $lvl): void {
		$this->getRedis()->set("player.{$this->username}.collection.level.$collection", $lvl);
	}

	public function setCollectionCount(string $collection, int $count): void {
		$this->getRedis()->set("player.{$this->username}.collection.count.$collection", $count);
	}

	public function increaseCollectionCount(string $collection, int $count): void {
		$this->getRedis()->incrby("player.{$this->username}.collection.count.$collection", $count);
	}

	public function decreaseCollectionCount(string $collection, int $count): void {
		$this->getRedis()->decrby("player.{$this->username}.collection.count.$collection", $count);
	}


	public function getAllUnlockedRecipesIdentifiers(): array {
		return $this->getRedis()->smembers("player.{$this->username}.collection.list");
	}

	public function unlockRecipe(string $id): void {
		$this->getRedis()->sadd("player.{$this->username}.collection.list", $id);
	}

	public function lockRecipe(string $id): void {
		$this->getRedis()->srem("player.{$this->username}.collection.list", $id);
	}



	public function getAllUnlockedTradeIdentifiers(): array {
		return $this->getRedis()->smembers("player.{$this->username}.trade.list");
	}

	public function unlockTrade(string $id): void {
		$this->getRedis()->sadd("player.{$this->username}.trade.list", $id);
	}

	public function lockTrade(string $id): void {
		$this->getRedis()->srem("player.{$this->username}.trade.list", $id);
	}

}