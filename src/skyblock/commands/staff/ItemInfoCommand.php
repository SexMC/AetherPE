<?php

declare(strict_types=1);

namespace skyblock\commands\staff;

use pocketmine\command\CommandSender;
use pocketmine\item\Durable;
use pocketmine\item\Food;
use pocketmine\item\ItemIds;
use pocketmine\item\Tool;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use skyblock\commands\AetherCommand;
use skyblock\Main;

class ItemInfoCommand extends AetherCommand {
    protected function prepare() : void{
        $this->setPermission("skyblock.command.iteminfo");

    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
        if(!$sender instanceof Player) {
            $sender->sendMessage("Please use this command in-game");
            return;
        }

        $item = $sender->getInventory()->getItemInHand();
        if ($item->getId() === ItemIds::AIR) {
            $sender->sendMessage(Main::PREFIX . "Please hold something in your hand");
            return;
        }

        $message = [
            Main::PREFIX . "{$item->getName()} information:",
            "VanillaName: {$item->getVanillaName()}",
            "ItemId: {$item->getId()}",
            "ItemVariant: {$item->getMeta()}",
            "Count: {$item->getCount()}",
            "MaxStackSize: {$item->getMaxStackSize()}",
        ];

        if ($item->getAttackPoints() > 0) {
            $message[] = "AttackPoints: {$item->getAttackPoints()}";
        }

        if ($item->getDefensePoints() > 0) {
            $message[] = "DefensePoints: {$item->getDefensePoints()}";
        }

        if ($item->getFuelTime() > 0) {
            $message[] = "FuelTime: {$item->getFuelTime()}s";
        }

        if ($item instanceof Durable) {
            $message[] = "Damage: {$item->getDamage()}";
            $message[] = "MaxDurability: {$item->getMaxDurability()}";
        }

        if ($item instanceof Food) {
            $message[] = "Food: {$item->getFoodRestore()}";
        }

        if ($item instanceof Tool) {
            $message[] = "MiningEfficiency: {$item->getMiningEfficiency(true)}";
        }

        $sender->sendMessage(implode(TextFormat::EOL, $message));
    }
}