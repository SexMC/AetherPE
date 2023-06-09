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

class CoinflipViewForm extends MenuForm {

	public function __construct(private Coinflip $cf)
	{
		$this->cf = $cf;
		parent::__construct("Coin Flip - " . $cf->getPlayer(), $this->getText(), [
			new MenuOption("§2§lAccept wager\n§7Click to accept the wager"),
			new MenuOption("§6§lBack\n§7Back to coin flip matches")
		], Closure::fromCallable([$this, "handle"]));
	}

	public function handle(Player $player, int $button): void {
		if($button === 1){
			Await::f2c(function() use($player){
				$data = yield CoinflipHandler::getInstance()->getAllCoinflips();
				$player->sendForm(new CoinflipForm($player, $data));
			});
			return;
		}
		if($this->cf->isUsed()){
			$player->sendMessage("§cThis coin flip already ended");
			return;
		}

		$player->sendForm(new CoinflipSelectColorForm($this->cf));
	}

	public function getText(): string {
		return "§b§lWager: \n   §7" . number_format($this->cf->getAmount()) . "\n   §7-5%% Tax ($"  . (100 / $this->cf->getAmount() * 5) . ")\n\n§b§lColor chosen:\n    §l§" . $this->cf->getColor() . strtoupper(CoinflipSelectColorForm::colors[$this->cf->getColor()]);
	}
}