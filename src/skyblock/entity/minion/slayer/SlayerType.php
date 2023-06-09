<?php

declare(strict_types=1);

namespace skyblock\entity\minion\slayer;



class SlayerType {

	public function __construct(private string $name, private string $entityId){ }

	public function getName() : string{
		return $this->name;
	}

	public function getEntityId() : string{
		return $this->entityId;
	}
}