<?php

declare(strict_types=1);

namespace skyblock\forms\commands\coinflip;

use Closure;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\player\Player;
use skyblock\misc\coinflip\Coinflip;
use skyblock\misc\coinflip\CoinflipHandler;
use SOFe\AwaitGenerator\Await;

class CoinflipForm extends MenuForm {


	/**
	 * @param Player $player
	 * @param Coinflip[]  $coinflips
	 */
	public function __construct(Player $player, private array $coinflips)
	{
		foreach($this->coinflips as $k => $v){
			if(strtolower($v->getPlayer()) === strtolower($player->getName())){
				unset($this->coinflips[$k]);
			}
		}

		$buttons = array_map(fn(Coinflip $cf): MenuOption => $cf->getFormButton(), $this->coinflips);
		array_unshift($buttons, new MenuOption("§6§lRefresh\n§r§7Click to refresh matches."));

		parent::__construct("Coin flip Matches", "", $buttons, Closure::fromCallable([$this, "handle"]));
	}

	public function handle(Player $player, int $button): void {
		if(isset($this->coinflips[$button - 1])){
			$cf = $this->coinflips[$button - 1];
			if($cf->isUsed()){
				$player->sendMessage("§cThis coin flip already ended");
				return;
			}


			$player->sendForm(new CoinflipViewForm($cf));
			return;
		}

		Await::f2c(function() use($player){
			$player->sendForm(new CoinflipForm($player, yield CoinflipHandler::getInstance()->getAllCoinflips()));
		});
	}
}