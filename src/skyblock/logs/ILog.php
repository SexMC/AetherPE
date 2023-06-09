<?php

declare(strict_types=1);

namespace skyblock\logs;


interface ILog {

	const TYPE_CHAT = 1;
	const TYPE_COMMAND = 2;
	const TYPE_COINFLIP = 3;
	const TYPE_MONEY_NOTE_CLAIM = 4;
	const TYPE_MONEY_WITHDRAW = 5;
	const TYPE_RANK_VOUCHERS = 6;
	const TYPE_DEATH = 7;
	const TYPE_GALAXY_KEY = 8;
	const TYPE_SPAWNER = 9;
	const TYPE_BANK = 10;
	const TYPE_SELL = 11;
	const TYPE_SLOTS = 12;
	const TYPE_DUPE = 13;
}