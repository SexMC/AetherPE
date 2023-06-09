<?php

declare(strict_types=1);

namespace skyblock\forms\commands\bounty;

use dktapps\pmforms\MenuOption;
use skyblock\forms\AetherMenuForm;
use skyblock\misc\bounty\BountyData;

class BountyInfoForm extends AetherMenuForm {

	public function __construct(private BountyData $data){
		parent::__construct("Bounty Information", $this->getText(), [new MenuOption("§cClose")]);
	}

	public function getText(): string {
		$arr = [
			"§7Username: §c" . $this->data->username,
			"§r",

			"§7Current Bounty: §c$" . number_format($this->data->currentBounty),
			"§r",

			"§7Total All time bounty: §c$" . number_format($this->data->lifeTimeBounty),
			"§r",

			"§7Max bounty at once: §c$" . number_format($this->data->maxBounty),
			"§r",
			"§7Total earned from bounties: §c$" . number_format($this->data->earned),
		];

		return implode("\n", $arr);
	}
}