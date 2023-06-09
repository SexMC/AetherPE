<?php

declare(strict_types=1);

namespace skyblock\misc\npcquests;

use pocketmine\block\Beacon;
use pocketmine\block\Crops;
use pocketmine\event\EventPriority;
use pocketmine\event\player\PlayerToggleSneakEvent;
use pocketmine\player\Player;
use pocketmine\world\particle\EnchantParticle;
use skyblock\Main;
use skyblock\player\AetherPlayer;
use skyblock\sessions\Session;
use skyblock\traits\AwaitStdTrait;
use skyblock\utils\EntityUtils;
use skyblock\utils\Utils;
use slapper\events\SlapperCreationEvent;
use SOFe\AwaitGenerator\Await;

abstract class NpcQuest implements INpcQuest{
	use AwaitStdTrait;

	public function finished(Session $session): bool {
		return $session->getNpcStarterQuestProgress() > $this->getOrder();
	}

	public function getCompletionMessage(): array {
		return [
			"§r",
			"§r§l§6» QUEST COMPLETED «",
            "§r§e [{$this->getName()}]",
            "§r",
		];
	}

	public function startConversation(AetherPlayer $player): void {
		Await::f2c(function() use($player) {
			NpcQuestHandler::getInstance()->setCooldown($player, 30);
			$messages = $this->getCompletionMessage();
			$player->sendMessage($messages);
			EntityUtils::playSound("random.anvil_use", $player->getLocation());

			foreach($this->getConversationMessages() as $message) {
				if($message === ""){
					$messages[] = $message;
					continue;
				}

				$message = str_replace(["{username}"], [$player->getName()], $message);
				$this->clearChat($player);

				$messages[] = $message;
				$player->sendMessage($messages);
				$player->sendMessage(array(
                    "§r",
                    "§r§l§a» CROUCH TO CONTINUE «",
                ));
				EntityUtils::playSound((mt_rand(1, 2) === 1 ? "mob.villager.yes" : "mob.villager.no"), $player->getLocation());
				yield $this->getStd()->awaitEvent(PlayerToggleSneakEvent::class, fn(PlayerToggleSneakEvent $e) => $e->isSneaking() && $e->getPlayer()->getId() === $player->getId(), true, EventPriority::LOW, true);
			}


			if(!$player->isOnline()) return;

			$this->onComplete($player);

			$session = $player->getCurrentProfilePlayerSession();
			$session->setNpcStarterQuestProgress($this->getOrder() + 1);

			NpcQuestHandler::getInstance()->removeCooldown($player);

			$all = NpcQuestHandler::getInstance()->getQuests();
			$currentIndex = array_search($this, $all);

			if(!isset($all[$this->getOrder() + 1])){
				$session->setNpcStarterQuestProgress(-1);


				yield $this->getStd()->sleep(20);
				$this->clearChat($player);

				//TODO: finished all playthrough quests

				$player->sendMessage([
					Main::PREFIX . "Congratulations, you have completed all walkthrough quests.",
					"§r§7You are now free in the AetherPE universe, be sure to check out your island profile!",
				]);

				return;
			}

			EntityUtils::playSound("random.levelup", $player->getLocation());
			$messages = array_merge($messages, $all[$currentIndex+1]->getUnlockMessage());
			$this->clearChat($player);
			$player->sendMessage($messages);
		});
	}

	public function clearChat(Player $player): void {
		$player->sendMessage(str_repeat("\n", 25));
	}
}