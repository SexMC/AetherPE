<?php

declare(strict_types=1);

namespace skyblock\items\masks;

trait MaskHolderTrait {

	public function setMask(string $mask): self {
		$this->getNamedTag()->setString("tag_mask_type", $mask);
		$this->resetLore();

		return $this;
	}

	public function getMask(): ?Mask {
		return MasksHandler::getInstance()->getMask(
			$this->getNamedTag()->getString("tag_mask_type", "unknown_mask_38")
		);
	}
}