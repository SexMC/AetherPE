<?php

declare(strict_types=1);

namespace skyblock\entity;

class LivingEntityType {
    public function __construct(
        protected string $networkId,
        protected string $name,
        protected float  $maxHealth,
        protected float  $height,
        protected float  $width,
        protected array  $drops = [],
    ) {

    }

    public function getNetworkId(): string {
        return $this->networkId;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getMaxHealth(): float {
        return $this->maxHealth;
    }

    public function getHeight(): float {
        return $this->height;
    }

    public function getWidth(): float {
        return $this->width;
    }

    public function getDrops(): array {
        return $this->drops;
    }
}