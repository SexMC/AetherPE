<?php

declare(strict_types=1);

namespace skyblock\utils;

use pocketmine\entity\Entity;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\math\AxisAlignedBB;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;
use pocketmine\world\Position;
use pocketmine\world\World;
use skyblock\entity\EntityHandler;
use skyblock\entity\object\LightningEntity;
use skyblock\entity\object\TextEntity;
use skyblock\Main;
use skyblock\sessions\Session;

class EntityUtils {

	public static function addEntity(Location $position, string $identifier, int $count): Entity {
		$entity = EntityHandler::getInstance()->get($identifier, Location::fromObject($position, $position->getWorld(), 0, 0));
		$entity->spawnToAll();

		return $entity;
	}

	public static function getSkin(string $geometryPath, string $skinPath, string $geometryName): Skin {
		$path = Main::getInstance()->getDataFolder() . $skinPath;

		if(!file_exists($path)){
			Main::getInstance()->saveResource($skinPath);
			Main::debug("Saves resource");
		}


		$img = imagecreatefrompng($path);
		$bytes = '';
		$l = (int)getimagesize($path)[1];
		for ($y = 0; $y < $l; $y++) {
			for ($x = 0; $x < $l; $x++) {
				$rgba = imagecolorat($img, $x, $y);
				$a = ((~((int)($rgba >> 24))) << 1) & 0xff;
				$r = ($rgba >> 16) & 0xff;
				$g = ($rgba >> 8) & 0xff;
				$b = $rgba & 0xff;
				$bytes .= chr($r) . chr($g) . chr($b) . chr($a);
			}
		}
		imagedestroy($img);
		$geopath = Main::getInstance()->getDataFolder() . $geometryPath;

		if(!file_exists($geopath)){
			Main::getInstance()->saveResource($geopath);
		}

		$geometry = file_get_contents($geopath);
		return new Skin("Standard_Custom", $bytes, "", "geometry.$geometryName", $geometry);
	}

	public static function getSkinAsRaw(string $skinPath): string {
		$path = Main::getInstance()->getDataFolder() . $skinPath;

		if(!file_exists($skinPath)){
			Main::getInstance()->saveResource($skinPath);
			Main::debug("Saves resource");
		}

		$img = imagecreatefrompng($path);
		$bytes = '';
		$l = (int)getimagesize($path)[1];
		for ($y = 0; $y < $l; $y++) {
			for ($x = 0; $x < $l; $x++) {
				$rgba = imagecolorat($img, $x, $y);
				$a = ((~((int)($rgba >> 24))) << 1) & 0xff;
				$r = ($rgba >> 16) & 0xff;
				$g = ($rgba >> 8) & 0xff;
				$b = $rgba & 0xff;
				$bytes .= chr($r) . chr($g) . chr($b) . chr($a);
			}
		}
		imagedestroy($img);

		return $bytes;
	}

	public static function spawnLightning(Location $location, float $volume = 1, float $pitch = 0.3): void {
		$lightning = new LightningEntity($location);
		$lightning->spawnToAll();

		self::playSound("ambient.weather.lightning.impact", $location, $volume, $pitch);
	}

	public static function spawnTextEntity(Location $location, string $text, int $despawnAfterInSeconds = 5, array $viewers = []): TextEntity {
		$e = new TextEntity($location);
		$e->setDespawnAfter($despawnAfterInSeconds * 20);
		$e->setText($text);

		if(empty($viewers)){
			$e->spawnToAll();
		} else {

			foreach($viewers as $viewer){
				$e->spawnTo($viewer);
			}
		}

		return $e;
	}

	public static function playSound(string $soundName, Location $location, float $volume = 1, float $pitch = 1){
		$location->getWorld()->broadcastPacketToViewers($location, self::getSoundPacket($soundName, $location, $volume, $pitch));
	}

	public static function getSoundPacket(string $soundName, Location $location, float $volume = 1, float $pitch = 1): PlaySoundPacket {
		$sound = new PlaySoundPacket();
		$sound->x = $location->getX();
		$sound->y = $location->getY();
		$sound->z = $location->getZ();
		$sound->volume = $volume;
		$sound->pitch = $pitch;
		$sound->soundName = $soundName;

		return $sound;
	}

    public static function getNearbyEntitiesFromPosition(Position $pos, int $dist, ?Entity $entity = null, bool $staff = false) : array {
        if (!$pos->isValid()) {
            return [];
        }

        $bb = new AxisAlignedBB($pos->x - $dist, $pos->y - $dist, $pos->z - $dist, $pos->x + $dist, $pos->y + $dist, $pos->z + $dist);
        return self::getNearbyEntitiesFromBB($pos->getWorld(), $bb, $entity, $staff);
    }

    public static function getNearbyEntitiesFromBB(World $world, AxisAlignedBB $bb, ?Entity $entity = null, bool $staff = false) : array {
        $entities = [];
        foreach ($world->getNearbyEntities($bb, $entity) as $entity) {
            if (!$staff && $entity instanceof Player) {
                $session = new Session($entity);
                if ($session->isStaffMode()) {
                    continue;
                }
            }

            $entities[] = $entity;
        }

        return $entities;
    }

	public static function getEntityNameFromID(string $entityID): string {
		$names = [
			EntityIds::ZOGLIN => "Zoglin",
			EntityIds::PLAYER => "Player",
			EntityIds::BAT => "Bat",
			EntityIds::BLAZE => "Blaze",
			EntityIds::CAVE_SPIDER => "Cave Spider",
			EntityIds::CHICKEN => "Chicken",
			EntityIds::COW => "Cow",
			EntityIds::CREEPER => "Creeper",
			EntityIds::DOLPHIN => "Dolphin",
			EntityIds::DONKEY => "Donkey",
			EntityIds::ELDER_GUARDIAN => "Elder Guardian",
			EntityIds::ENDERMAN => "Enderman",
			EntityIds::ENDERMITE => "Endermite",
			EntityIds::GHAST => "Ghast",
			EntityIds::GUARDIAN => "Guardian",
			EntityIds::HORSE => "Horse",
			EntityIds::HUSK => "Husk",
			EntityIds::IRON_GOLEM => "Iron Golem",
			EntityIds::LLAMA => "Llama",
			EntityIds::MAGMA_CUBE => "Magma Cube",
			EntityIds::MOOSHROOM => "Mooshroom",
			EntityIds::MULE => "Mule",
			EntityIds::OCELOT => "Ocelot",
			EntityIds::PANDA => "Panda",
			EntityIds::PARROT => "Parrot",
			EntityIds::PHANTOM => "Phantom",
			EntityIds::PIG => "Pig",
			EntityIds::POLAR_BEAR => "Polar Bear",
			EntityIds::RABBIT => "Rabbit",
			EntityIds::SHEEP => "Sheep",
			EntityIds::SHULKER => "Shulker",
			EntityIds::SILVERFISH => "Silverfish",
			EntityIds::SKELETON => "Skeleton",
			EntityIds::SKELETON_HORSE => "Skeleton Horse",
			EntityIds::SLIME => "Slime",
			EntityIds::SNOW_GOLEM => "Snow Golem",
			EntityIds::SPIDER => "Spider",
			EntityIds::SQUID => "Squid",
			EntityIds::STRAY => "Stray",
			EntityIds::VEX => "Vex",
			EntityIds::VILLAGER => "Villager",
			EntityIds::VINDICATOR => "Vindicator",
			EntityIds::WITCH => "Witch",
			EntityIds::WITHER_SKELETON => "Wither Skeleton",
			EntityIds::WITHER => "Wither",
			EntityIds::WOLF => "Wolf",
			EntityIds::ZOMBIE => "Zombie",
			EntityIds::ZOMBIE_HORSE => "Zombie Horse",
			EntityIds::ZOMBIE_PIGMAN => "Zombie Pigman",
			EntityIds::ZOMBIE_VILLAGER => "Zombie Villager",
			EntityIds::ENDER_DRAGON => "Ender Dragon",
			EntityIds::FOX => "Fox",
			EntityIds::BEE => "Bee",
			EntityIds::RAVAGER => "Ravager",
			EntityIds::PIGLIN => "Piglin",
			EntityIds::STRIDER => "Strider",
			EntityIds::HOGLIN => "Hoglin",
			EntityIds::EVOCATION_ILLAGER => "Evoker",
			EntityIds::TURTLE => "Turtle",
			"minecraft:warden" => "Warden",
			"minecraft:piglin_brute" => "Piglin Brute",
		];

		return $names[$entityID] ?? "Monster (Unknown)";
	}

	public static function getEntityIdFromName(string $name): string {
		$all = [
			"minecraft:warden" => "Warden",
			EntityIds::PLAYER => "Player",
			EntityIds::ZOGLIN => "Zoglin",
			EntityIds::BAT => "Bat",
			EntityIds::BLAZE => "Blaze",
			EntityIds::CAVE_SPIDER => "Cave Spider",
			EntityIds::CHICKEN => "Chicken",
			EntityIds::COW => "Cow",
			EntityIds::CREEPER => "Creeper",
			EntityIds::DOLPHIN => "Dolphin",
			EntityIds::DONKEY => "Donkey",
			EntityIds::ELDER_GUARDIAN => "Elder Guardian",
			EntityIds::ENDERMAN => "Enderman",
			EntityIds::ENDERMITE => "Endermite",
			EntityIds::GHAST => "Ghast",
			EntityIds::GUARDIAN => "Guardian",
			EntityIds::HORSE => "Horse",
			EntityIds::HUSK => "Husk",
			EntityIds::IRON_GOLEM => "Iron Golem",
			EntityIds::LLAMA => "Llama",
			EntityIds::MAGMA_CUBE => "Magma Cube",
			EntityIds::MOOSHROOM => "Mooshroom",
			EntityIds::MULE => "Mule",
			EntityIds::OCELOT => "Ocelot",
			EntityIds::PANDA => "Panda",
			EntityIds::PARROT => "Parrot",
			EntityIds::PHANTOM => "Phantom",
			EntityIds::PIG => "Pig",
			EntityIds::POLAR_BEAR => "Polar Bear",
			EntityIds::RABBIT => "Rabbit",
			EntityIds::SHEEP => "Sheep",
			EntityIds::SHULKER => "Shulker",
			EntityIds::SILVERFISH => "Silverfish",
			EntityIds::SKELETON => "Skeleton",
			EntityIds::SKELETON_HORSE => "Skeleton Horse",
			EntityIds::SLIME => "Slime",
			EntityIds::SNOW_GOLEM => "Snow Golem",
			EntityIds::SPIDER => "Spider",
			EntityIds::SQUID => "Squid",
			EntityIds::STRAY => "Stray",
			EntityIds::VEX => "Vex",
			EntityIds::VILLAGER => "Villager",
			EntityIds::VINDICATOR => "Vindicator",
			EntityIds::WITCH => "Witch",
			EntityIds::WITHER_SKELETON => "Wither Skeleton",
			EntityIds::WITHER => "Wither",
			EntityIds::WOLF => "Wolf",
			EntityIds::ZOMBIE => "Zombie",
			EntityIds::ZOMBIE_HORSE => "Zombie Horse",
			EntityIds::ZOMBIE_PIGMAN => "Zombie Pigman",
			EntityIds::ZOMBIE_VILLAGER => "Zombie Villager",
			EntityIds::ENDER_DRAGON => "Ender Dragon",
			EntityIds::FOX => "Fox",
			EntityIds::BEE => "Bee",
			EntityIds::RAVAGER => "Ravager",
			EntityIds::PIGLIN => "Piglin",
			"minecraft:piglin_brute" => "Piglin Brute",
			EntityIds::STRIDER => "Strider",
			EntityIds::HOGLIN => "Hoglin",
			EntityIds::EVOCATION_ILLAGER => "Evoker",
			EntityIds::TURTLE => "Turtle",
		];

		return array_keys($all)[array_search($name, array_values($all))];
	}
}