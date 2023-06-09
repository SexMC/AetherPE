<?php

declare(strict_types=1);

namespace skyblock\islands;

trait IslandBankData {



	public function getBankMoney(): int {
		return (int) ($this->getRedis()->get("island.{$this->name}.bank.money") ?? 0);
	}

	public function setBankMoney(int $amount): void {
		$this->getRedis()->set("island.{$this->name}.bank.money", $amount);
	}

	public function increaseBankMoney(int $amount): void {
		$this->getRedis()->incrby("island.{$this->name}.bank.money", $amount);
	}

	public function decreaseBankMoney(int $amount): void {
		$this->getRedis()->decrby("island.{$this->name}.bank.money", $amount);
	}


	public function getBankEssence(): int {
		return (int) ($this->getRedis()->get("island.{$this->name}.bank.essence") ?? 0);
	}

	public function setBankEssence(int $amount): void {
		$this->getRedis()->set("island.{$this->name}.bank.essence", $amount);
	}

	public function increaseBankEssence(int $amount): void {
		$this->getRedis()->incrby("island.{$this->name}.bank.essence", $amount);
	}

	public function decreaseBankEssence(int $amount): void {
		$this->getRedis()->decrby("island.{$this->name}.bank.essence", $amount);
	}





	public function getQuestTokens(): int {
		return (int) ($this->getRedis()->get("island.{$this->name}.bank.quest") ?? 0);
	}

	public function setQuestTokens(int $amount): void {
		$this->getRedis()->set("island.{$this->name}.bank.quest", $amount);
	}

	public function increaseQuestTokens(int $amount): void {
		$this->getRedis()->incrby("island.{$this->name}.bank.quest", $amount);
	}

	public function decreaseQuestTokens(int $amount): void {
		$this->getRedis()->decrby("island.{$this->name}.bank.quest", $amount);
	}





	public function getHeroicQuestTokens(): int {
		return (int) ($this->getRedis()->get("island.{$this->name}.bank.hquest") ?? 0);
	}

	public function setHeroicQuestTokens(int $amount): void {
		$this->getRedis()->set("island.{$this->name}.bank.hquest", $amount);
	}

	public function increaseHeroicQuestTokens(int $amount): void {
		$this->getRedis()->incrby("island.{$this->name}.bank.hquest", $amount);
	}

	public function decreaseHeroicQuestTokens(int $amount): void {
		$this->getRedis()->decrby("island.{$this->name}.bank.hquest", $amount);
	}
}