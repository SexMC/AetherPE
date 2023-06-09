<?php

declare(strict_types=1);

namespace skyblock\commands\staff;

use CortexPE\Commando\args\TextArgument;
use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\lang\KnownTranslationFactory;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use skyblock\commands\AetherCommand;
use skyblock\commands\staff\sub\RankAddSubCommand;
use skyblock\commands\staff\sub\RankListSubCommand;
use skyblock\commands\staff\sub\RankRemoveSubCommand;
use skyblock\utils\Utils;

class SayCommand extends AetherCommand {
	protected function prepare() : void{
		$this->setPermission("skyblock.command.say");
		$this->registerArgument(0, new TextArgument("message"));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		Utils::announce(TextFormat::LIGHT_PURPLE . "[SAY] " . $args["message"]);
	}
}