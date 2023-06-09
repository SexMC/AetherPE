<?php

declare(strict_types=1);

namespace skyblock\misc\auctionhouse;

use JsonSerializable;
use pocketmine\item\Item;
use skyblock\utils\TimeUtils;
use pocketmine\utils\TextFormat as C;

class AuctionHouseItem implements JsonSerializable{

	public function __construct(
		private string $owner,
		private Item|array $item,
		private int $price,
		private int $unix,
		private ?string $auctionID = null,
	){
		if($this->auctionID === null){
			$this->auctionID = uniqid();
		}
	}

	/**
	 * @return string
	 */
	public function getAuctionID() : string{
		return $this->auctionID;
	}

	/**
	 * @return string
	 */
	public function getOwner() : string{
		return $this->owner;
	}

	/**
	 * @return Item
	 */
	public function getItem() : Item{
		return $this->item;
	}

	/**
	 * @return int
	 */
	public function getPrice() : int{
		return $this->price;
	}

	/**
	 * @return int
	 */
	public function getUnix() : int{
		return $this->unix;
	}

	public function jsonSerialize(){
		return [
			"owner" => $this->owner,
			"item" => (is_array($this->item) ? $this->item : $this->item->jsonSerialize()),
			"price" => $this->price,
			"unix" => $this->unix,
			"auctionID" => $this->auctionID,
		];
	}

	public function getViewItem(): Item {
		$item = clone $this->item;
		$item->getNamedTag()->setString("auctionID", $this->auctionID);

		$lore = $this->item->getLore();
		$expires = TimeUtils::secondsToTime(AuctionHouseHandler::EXPIRE_TIME - (time() - $this->unix));

		$lore[] = "";
		$lore[] = C::RED . C::BOLD . str_repeat("-", 6) . C::RESET;
		$lore[] = C::GRAY . "Seller: " . C::RED . $this->owner;
		$lore[] = C::GRAY . "Price: " . C::RED . number_format($this->price);
		$lore[] = C::GRAY . "Expires: " . C::RED . "{$expires["d"]}d {$expires["h"]}h {$expires["m"]}m {$expires["s"]}s";
		$lore[] = C::RED . C::BOLD . str_repeat("-", 6) . C::RESET;

		return $item->setLore($lore);
	}
}