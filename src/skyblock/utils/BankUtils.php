<?php

declare(strict_types=1);

namespace skyblock\utils;

use InvalidArgumentException;
use skyblock\Main;
use skyblock\player\AetherPlayer;
use skyblock\sessions\Session;

class BankUtils {

	const INTEREST_TIME = 31 * 3600;

	const DEPOSIT = 0;
	const WITHDRAW = 1;

	public static function maxLimit(int $lvl): int {
		return match($lvl) {
			1 => 50000000,
			2 => 100000000,
			3 => 250000000,
			default => throw new InvalidArgumentException("Invalid bank level: $lvl"),
		};
	}

	public static function withdraw(AetherPlayer $executor, int $amount, Session $bank, Session $withdrawer): void {
		if($bank->getBank() < $amount){
			$executor->sendMessage(Main::PREFIX . "You cannot withdraw more money than there is!");
			return;
		}

		$bank->setBank($bank->getBank() - $amount);
		$withdrawer->increasePurse($amount);

		$executor->getCurrentProfile()->announce(Main::PREFIX . "§b{$executor->getName()} §7withdrew §6" . number_format($amount) . " coins");

		$bank->addTransactionHistory([self::WITHDRAW, $amount, time(), $executor->getName()]);
	}

	public static function deposit(AetherPlayer $executor, int $amount, Session $from, Session $to): void {
		if($from->getPurse() < $amount){
			$executor->sendMessage(Main::PREFIX . "You cannot deposit more money than you have!");
			return;
		}

		$from->decreasePurse($amount);
		$to->setBank($to->getBank() + $amount);

		$executor->getCurrentProfile()->announce(Main::PREFIX . "§b{$executor->getName()} §7deposited §6" . number_format($amount) . " coins");

		$to->addTransactionHistory([self::DEPOSIT, $amount, time(), $executor->getName()]);
	}

	public static function checkForInterest(Session $session): ?int {
		if(time() - $session->getLastInterestUnix() >= self::INTEREST_TIME){
			$amount = self::getInterestAmount($session);

			$session->setLastInterestUnix(time());
			$session->setLastInterest($amount);
			$session->setBank($session->getBank() + $amount);
			$session->addTransactionHistory([self::DEPOSIT, $amount, time(), "Interest Rate"]);

			return $amount;
		}

		return null;
	}

	public static function getInterestAmount(Session $session): int {
		return min((int) floor($session->getBank() * self::getInterestPercent($session)), 300000);
	}

	public static function getInterestPercent(Session $session): float{
		$coins = $session->getBank();

		if($coins <= 10000000){
			return 0.02;
		}

		if($coins <= 20000000){
			return 0.01;
		}

		if($coins <= 30000000){
			return 0.005;
		}

		if($coins <= 50000000){
			return 0.002;
		}

		if($coins <= 160000000){
			return 0.001;
		}

		return 0.0001;
	}
}