<?php

declare(strict_types=1);

namespace skyblock\misc\auctionhouse;

use pocketmine\item\Item;
use pocketmine\utils\SingletonTrait;
use skyblock\communication\CommunicationLogicHandler;
use skyblock\communication\operations\mechanics\auctionhouse\AuctionHouseAddOperation;
use skyblock\communication\operations\mechanics\auctionhouse\AuctionHouseGetOperation;
use skyblock\communication\packets\types\mechanics\auctionhouse\AuctionAddRequestPacket;
use skyblock\communication\packets\types\mechanics\auctionhouse\AuctionAddResponsePacket;
use skyblock\communication\packets\types\mechanics\auctionhouse\AuctionGetAllRequest;
use skyblock\communication\packets\types\mechanics\auctionhouse\AuctionRemoveRequestPacket;
use skyblock\Main;
use SOFe\AwaitGenerator\Await;

class AuctionHouseHandler  {
	use SingletonTrait;

	public const EXPIRE_TIME = 86400;

	public function getAllAuctions() {
		$data = yield $this->getAllAuctionsRaw();

		$all = [];
		foreach($data as $k => $v){
			if(is_string($v["item"])){
				$v["item"] = json_decode($v["item"], true);
			}
			$all[$k] = new AuctionHouseItem($v["owner"], Item::jsonDeserialize($v["item"]), $v["price"], $v["unix"], $v["auctionID"]);
		}

		return $all;
	}

	public function getAllAuctionsRaw() {
		CommunicationLogicHandler::getInstance()->sendPacket(new AuctionGetAllRequest(yield Await::RESOLVE));

		return yield Await::ONCE;
	}

	public function removeAuction(string $auctionID) {
		CommunicationLogicHandler::getInstance()->sendPacket(new AuctionRemoveRequestPacket($auctionID, yield Await::RESOLVE));

		return yield Await::ONCE;
	}

	public function addItem(AuctionHouseItem $item) {
		CommunicationLogicHandler::getInstance()->sendPacket(new AuctionAddRequestPacket($item, yield Await::RESOLVE));

		return yield Await::ONCE;
	}

	public function getAuctionCountByPlayer(string $username) {
		$data = yield $this->getAllAuctions();

		$count = 0;
		/** @var AuctionHouseItem $v */
		foreach($data as $v){
			if(strtolower($v->getOwner()) === strtolower($username)){
				$count++;
			}
		}

		return $count;
	}
}