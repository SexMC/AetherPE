<?php

declare(strict_types=1);

namespace skyblock\entity;

use pocketmine\block\BlockFactory;
use pocketmine\data\bedrock\PotionTypeIdMap;
use pocketmine\data\bedrock\PotionTypeIds;
use pocketmine\data\SavedDataLoadingException;
use pocketmine\entity\Entity;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\Location;
use pocketmine\entity\object\FallingBlock;
use pocketmine\item\Item;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\utils\Utils;
use pocketmine\world\World;
use skyblock\entity\boss\IslandBossEntity;
use skyblock\entity\boss\WitheredBlazeBoss;
use skyblock\entity\minion\farmer\FarmerMinion;
use skyblock\entity\minion\fishing\FishingMinion;
use skyblock\entity\minion\foraging\ForagingMinion;
use skyblock\entity\minion\miner\MinerMinion;
use skyblock\entity\minion\slayer\SlayerMinion;
use skyblock\entity\object\AetherItemEntity;
use skyblock\entity\object\CarpenterFallingBlock;
use skyblock\entity\object\LightningEntity;
use skyblock\entity\projectile\FishingRodEntity;
use skyblock\entity\projectile\SplashPotion;
use skyblock\traits\AwaitStdTrait;
use skyblock\traits\InstanceTrait;

class EntityHandler {

    use InstanceTrait;
    use AwaitStdTrait;

    private array $clearlagEntities = [
    ];

    private array $entities = [
        MinerMinion::class,
        FarmerMinion::class,
        SlayerMinion::class,
        ForagingMinion::class,
        FishingMinion::class,
    ];

    private array $map;

    public function __construct() {
        self::$instance = $this;

        $this->register(AetherItemEntity::class, ['Item', 'minecraft:item'], function (World $world, CompoundTag $nbt): AetherItemEntity {
            $itemTag = $nbt->getCompoundTag("Item");
            if ($itemTag === null) {
                throw new SavedDataLoadingException("Expected \"Item\" NBT tag not found");
            }

            $item = Item::nbtDeserialize($itemTag);
            if ($item->isNull()) {
                throw new SavedDataLoadingException("Item is invalid");
            }
            return new AetherItemEntity(EntityDataHelper::parseLocation($nbt, $world), $item, $nbt);
        });

        $this->register(CarpenterFallingBlock::class, ["CarpenterFallingBlock"], function (World $world, CompoundTag $nbt): CarpenterFallingBlock {
            return new CarpenterFallingBlock(EntityDataHelper::parseLocation($nbt, $world), FallingBlock::parseBlockNBT(BlockFactory::getInstance(), $nbt), $nbt);
        });

        $this->register(FishingRodEntity::class, ["FishingRodEntity"]);

        $this->register(IslandBossEntity::class, ["IslandBossEntity"], function (World $world, CompoundTag $nbt): IslandBossEntity {
            return new IslandBossEntity($nbt->getString("networkID38"), EntityDataHelper::parseLocation($nbt, $world), $nbt);
        });

        $this->register(LightningEntity::class, ["Lightning"]);

        $this->register(SplashPotion::class, ['ThrownPotion', 'minecraft:potion', 'thrownpotion'], function (World $world, CompoundTag $nbt): SplashPotion {
            $potionType = PotionTypeIdMap::getInstance()->fromId($nbt->getShort("PotionId", PotionTypeIds::WATER));
            if ($potionType === null) {
                throw new SavedDataLoadingException("No such potion type");
            }
            return new SplashPotion(EntityDataHelper::parseLocation($nbt, $world), null, $potionType, $nbt);
        });

        $this->register(WitheredBlazeBoss::class, ["WitheredBlazeBoss"]);

        foreach ($this->clearlagEntities as $entity) {
            $this->register($entity);
            $this->clearlagEntities[$entity::getNetworkTypeId()] = $entity;
        }

        foreach ($this->entities as $entity) {
            $this->register($entity);
        }
    }

    /**
     * @param class-string<Entity> $className
     */
    public function register(string $className, array $saveNames = [], ?callable $creationFunc = null): void {
        if ($creationFunc === null) {
            Utils::testValidInstance($className, Entity::class);
            $creationFunc = function (World $world, CompoundTag $nbt) use ($className): Entity {
                return new $className(EntityDataHelper::parseLocation($nbt, $world), $nbt);
            };
        }

        EntityFactory::getInstance()->register($className, $creationFunc, empty($saveNames) ? ["aetherpe:" . $className::getNetworkTypeId()] : $saveNames);
    }

    public function get(string $identifier, Location $location, CompoundTag $nbt = null): ?Entity {
        if (isset($this->clearlagEntities[$identifier])) {
            return new $this->clearlagEntities[$identifier]($location, $nbt);
        }

        return null;
    }
}