<?php

declare(strict_types=1);

namespace skyblock\forms\commands\coinflip;

use Closure;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\player\Player;
use skyblock\communication\CommunicationLogicHandler;
use skyblock\communication\packets\types\mechanics\coinflip\CoinflipStartOperation;
use skyblock\communication\packets\types\mechanics\coinflip\CoinflipStartRequestPacket;
use skyblock\communication\packets\types\mechanics\coinflipAddOperation;
use skyblock\Main;
use skyblock\misc\coinflip\Coinflip;
use skyblock\misc\coinflip\CoinflipHandler;
use skyblock\sessions\Session;
use skyblock\traits\AwaitStdTrait;
use SOFe\AwaitGenerator\Await;

class CoinflipSelectColorForm extends MenuForm {
	use AwaitStdTrait;

	const colors = [
		"e" => "yellow",
		"a" => "green",
		"d" => "purple",
		"b" => "light blue",
		"6" => "orange",
		"8" => "black",
		"7"=> "gray",
		"c" => "red"
	];

    private array $cache = [];


    public function __construct(private ?Coinflip $cf = null, private ?int $amount = null)
	{
		parent::__construct("Pick a coin flip color", "", $this->getButtons(), Closure::fromCallable([$this, "handle"]));
	}

    public function handle(Player $player, int $button): void {
		if($this->amount !== null){
			$session = new Session($player);
			if($session->getPurse() >= $this->amount){
				$session->decreasePurse($this->amount);
				Await::f2c(function() use($session, $player, $button){
					$cf = new Coinflip($player->getName(),(string) $this->cache[$button], $this->amount);
					$data = yield CoinflipHandler::getInstance()->addCoinflip($cf);
					if($data === true){
						$player->sendMessage(Main::PREFIX . "§7Coin flip queued, waiting for an opponent");
					} else $player->sendMessage(Main::PREFIX . "§7Failed to queue coin flip, gave the money back.");

					/*$data = yield Await::race([
						"response" => CoinflipHandler::getInstance()->addCoinflip($cf),
						"sleep" => $this->getStd()->sleep(20 * 5)
					]);

					if($data[0] === "response"){
						if($data[1] === true){
							$player->sendMessage(Main::PREFIX . "§7Coin flip queued, waiting for an opponent");
							return;
						}
					}

					$session->increaseMoney($this->amount);
					$player->sendMessage(Main::PREFIX . "§7Failed to queue coin flip, gave the money back.");*/
				});
			} else $player->sendMessage("§cYou cannot coin flip more money than you have!");

			return;
		}

		if(isset($this->cache[$button])){
			$color = "" . $this->cache[$button];

			if($this->cf !== null){
				$session = new Session($player);

				if($this->cf->getAmount() > $session->getPurse()){
					$player->sendMessage(Main::PREFIX . "You cannot coinflip more money than you have");
					return;
				}

				$session->decreasePurse($this->cf->getAmount());
				Await::f2c(function() use($player, $color, $session){
					CommunicationLogicHandler::getInstance()->sendPacket(new CoinflipStartRequestPacket($this->cf->getPlayer(), $player->getName(), $color, yield Await::RESOLVE));
					$data = yield Await::ONCE;

					if($data === false){
						$player->sendMessage(Main::PREFIX . "Coinflip already used");
						$session->increasePurse($this->cf->getAmount());
					} else $player->sendMessage(Main::PREFIX . "Starting coinflip");
				});
			}
		}
	}

    public function getButtons(): array {
		$buttons = [];

		foreach (self::colors as $k => $v){
			if($this->cf !== null) {
				if ((string) $k === (string) $this->cf->getColor()) continue;
			}
			$this->cache[] = $k;
			$buttons[] = new MenuOption("§l§" . $k . strtoupper($v) . "\n§r§7Click to pick " . ucwords($v));
		}

		return $buttons;
	}
}