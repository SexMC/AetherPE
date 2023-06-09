<?php

declare(strict_types=1);

namespace skyblock\listeners;

use skyblock\Main;

class ListenerManager {

	public function __construct( protected Main $plugin ){
		$this->plugin->getServer()->getPluginManager()->registerEvents(new EventListener($this->plugin), $plugin);
		$this->plugin->getServer()->getPluginManager()->registerEvents(new AetherPEListener(), $plugin);
		$this->plugin->getServer()->getPluginManager()->registerEvents(new CustomEnchantListener(), $plugin);
        $this->plugin->getServer()->getPluginManager()->registerEvents(new StaffModeListener(), $this->plugin);
	}
}