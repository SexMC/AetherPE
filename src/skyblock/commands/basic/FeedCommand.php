<?php

declare(strict_types=1);

namespace skyblock\commands\basic;

use pocketmine\command\CommandSender;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use skyblock\commands\AetherCommand;

use skyblock\items\customenchants\CustomEnchantInstance;
use skyblock\items\customenchants\CustomEnchants;
use skyblock\items\pets\MysteryPetItem;
use skyblock\items\pets\types\WolfPet;
use skyblock\items\SkyblockItemFactory;
use skyblock\items\SkyblockItems;
use skyblock\menus\recipe\CraftingMenu;
use skyblock\menus\recipe\RecipeByClassMenu;
use skyblock\traits\AwaitStdTrait;
use skyblock\traits\PlayerCooldownTrait;
use skyblock\utils\Utils;

class FeedCommand extends AetherCommand {
	use PlayerCooldownTrait;
	use AwaitStdTrait;

	private $last = null;

	protected function prepare() : void{
		$this->setDescription("Heal yourself");
		$this->setPermission("skyblock.command.feed");
		$this->setCanBeUsedInCombat(true);
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof Player){

			$item = VanillaItems::POTATO();
			$item->setLore([
				'§r§6§lHead Hunter"',
				"§r§f§o==========================",
				'§r§f§7"May the gods have mercy on your soul."',
				"§r§f§7- Unknown archer",
				"§r§f",
				"§r§f§7Bow Lore:",
				"§r§fA powerful bow that sends a cursed head hurtling towards your enemies. ",
				"§r§fAThe Head Hunter is imbued with the strength of the gods, and its shots ",
				"§r§fAdeal devastating damage upon impact. However, the power of the curse ",
				"§r§fAmeans that the wielder of the bow will be consumed by madness ",
				"§r§fAif they continue to use it for too long. ",
				"§r§c§l==========================",
				"§r§c§lItem Ability: Head Curse",
				"§r§c§lFires an enchanted head that seeks out and ",
				"§r§c§ldestroys your enemies on impact. ",
				"§r§c§lCooldown: 5 seco
				nds",
				"§r§f§o==========================",
				"§r§r",
			]);

			Utils::addItem($sender, $item);

			//Utils::addItem($sender, (SkyblockItems::MYSTERY_PET_ITEM())->setPet(new WolfPet())->setType(MysteryPetItem::TYPE_SUPER_ENCHANTED));

			//(new CraftingMenu($sender))->send($sender);
			//(new RecipeByClassMenu($sender))->send($sender);

			/*Await::f2c(function() use($sender) {
				$floor = new DungeonFloorOne();
				yield $floor->start(new DungeonInstance($floor, [$sender], time(), $sender->getWorld()));
				var_dump("started");
			});*/

			/*Await::f2c(function() use ($sender){
				while(true){
					$data = yield $this->getStd()->awaitEvent(BlockBreakEvent::class, fn(BlockBreakEvent $event) => true, true, EventPriority::LOW, false);
					$data->cancel();
					$pos = $data->getBlock()->getPosition();

					$v = json_decode(file_get_contents(Main::getInstance()->getDataFolder() . "coords38.json"), true);
					if(isset($v["data"])){
						$b = $data->getBlock();
						if($b instanceof Ladder){
							$v["data"][] = [$pos->getX(), $pos->getY(), $pos->getZ(), $b->getFacing()];

						} else $v["data"][] = [$pos->getX(), $pos->getY(), $pos->getZ()];
					}

					file_put_contents(Main::getInstance()->getDataFolder() . "coords38.json", json_encode($v));
				}
			});*/

			//$item = $sender->getInventory()->getItemInHand()->getBlock();
			//$e = new HatEntity($sender->getLocation(), null, $sender, $item);
			//$e->spawnToAll();
			//$sender->sendMessage("spawned");

			/*$s = new Session($sender);
			foreach(NpcQuestHandler::getInstance()->getQuests() as $quest){
			$s->setBool($quest->getIdentifier(), false);
			}

			if($this->last !== null){
				//$this->last->flagForDespawn();
			}
			$item = $sender->getInventory()->getItemInHand()->getBlock();
			$e = new HatEntity($sender->getLocation(), null, $sender, $item);
			$e->spawnToAll();
			$sender->sendMessage("spawned");

			$this->last = $e;*/


			/*Await::f2c(function() use($sender){
				while(true){
					$world = $sender->getPosition()->getWorld();
					$total = 0;
					for($i = 1; $i <= 180; $i++){
						if($i % 5 === 0){
							$total += 0.05;
							yield $this->getStd()->sleep(1);
						}

						if($i % 2 === 0) continue;
						if($i % 3 === 0) continue;


						$x = 0.75 * cos($i);
						$z = 0.75 * sin($i);

						$vec = $sender->getPosition()->asVector3();
						$world->addParticle($vec->add($x, $total, $z), new DustParticle(new Color(0xf3, 0x8b, 0xaa)));
					}

					yield $this->getStd()->sleep(3);
				}


				//(x-5)^2+(y-6)^2=5

				$vec = $sender->getPosition()->asVector3();

				//x^2+25+y^2+36=5
				//x^2=-y
			});*/

			/*if($this->isOnCooldown($sender)){
				$sender->sendMessage(Main::PREFIX . "This command is on cooldown for " . ($this->getCooldown($sender) . " second(s)"));
				return;
			}

			$this->setCooldown($sender, 30);

			$sender->getHungerManager()->setSaturation(10);
			$sender->getHungerManager()->setFood($sender->getHungerManager()->getMaxFood());
			$sender->sendMessage(Main::PREFIX . "You've successfully fed yourself");*/
		}
	}
}