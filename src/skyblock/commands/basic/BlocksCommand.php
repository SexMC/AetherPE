<?php

declare(strict_types=1);

namespace skyblock\commands\basic;

use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use skyblock\commands\AetherCommand;
use skyblock\Main;
use skyblock\traits\PlayerCooldownTrait;

class BlocksCommand extends AetherCommand {
    use PlayerCooldownTrait;

    public const ingots = [
        ItemIds::COAL => ItemIds::COAL_BLOCK,
        ItemIds::IRON_INGOT => ItemIds::IRON_BLOCK,
        ItemIds::GOLD_INGOT => ItemIds::GOLD_BLOCK,
        ItemIds::EMERALD => ItemIds::EMERALD_BLOCK,
        ItemIds::DIAMOND => ItemIds::DIAMOND_BLOCK,
        ItemIds::REDSTONE => ItemIds::REDSTONE_BLOCK
    ];

    protected function prepare() : void{
        $this->setDescription("Turn all the ingots in your inventory into blocks");
        $this->setPermission("skyblock.command.blocks");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
        if($sender instanceof Player){
            if($this->isOnCooldown($sender)){
                $sender->sendMessage(Main::PREFIX . "This command is on cooldown for " . ($this->getCooldown($sender) . " second(s)"));
                return;
            }

            $this->setCooldown($sender, 3);

            $ingots = [];
            foreach ($sender->getInventory()->getContents() as $item) {
                if (in_array($item->getId(), self::ingots)) {
                    if (!isset($ingots[$item->getId()])) {
                        $ingots[$item->getId()] = 0;
                    }

                    $ingots[$item->getId()] += $item->getCount();
                }
            }

            /** @var ItemFactory $itemFactory */
            $itemFactory = ItemFactory::getInstance();
            foreach ($ingots as $id => $count) {
                if (!isset(self::ingots[$id])) {
                    continue;
                }

                while ($count > 9) {
                    if (!$sender->getInventory()->canAddItem($itemFactory->get(self::ingots[$id]))) {
                        $sender->sendMessage(Main::PREFIX . "Not enough space in your inventory to finish converting ingots to blocks");
                        return;
                    }

                    $count -= 9;
                    $sender->getInventory()->removeItem($itemFactory->get($id, 0, 9));
                    $sender->getInventory()->addItem($itemFactory->get(self::ingots[$id]));
                }
            }

            $sender->sendMessage(Main::PREFIX . "Converted all your ingots to blocks");
        }
    }
}