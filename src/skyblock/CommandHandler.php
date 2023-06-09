<?php

declare(strict_types=1);

namespace skyblock;

use skyblock\commands\arguments\EnhancementAttributeArgument;
use skyblock\commands\basic\AgeCommand;
use skyblock\commands\basic\AuctionHouseCommand;
use skyblock\commands\basic\AutoSellCommand;
use skyblock\commands\basic\BankCommand;
use skyblock\commands\basic\BlackAuctionHouseCommand;
use skyblock\commands\basic\BlocksCommand;
use skyblock\commands\basic\blueprints\BlockCommand;
use skyblock\commands\basic\blueprints\ListBlockCommand;
use skyblock\commands\basic\blueprints\MsgCommand;
use skyblock\commands\basic\blueprints\RecallCommand;
use skyblock\commands\basic\blueprints\UnblockCommand;
use skyblock\commands\basic\BountyCommand;
use skyblock\commands\basic\CeFixCommand;
use skyblock\commands\basic\CeInfoCommand;
use skyblock\commands\basic\ChunkBorders;
use skyblock\commands\basic\CoinflipCommand;
use skyblock\commands\basic\CollectCommand;
use skyblock\commands\basic\CollectionCommand;
use skyblock\commands\basic\CustomEnchantShopCommand;
use skyblock\commands\basic\DailyQuestsCommand;
use skyblock\commands\basic\EffectsCommand;
use skyblock\commands\basic\FeedCommand;
use skyblock\commands\basic\FixCommand;
use skyblock\commands\basic\FlyCommand;
use skyblock\commands\basic\FundCommand;
use skyblock\commands\basic\HealCommand;
use skyblock\commands\basic\HotspotCommand;
use skyblock\commands\basic\IdCommand;
use skyblock\commands\basic\IslandCommand;
use skyblock\commands\basic\ItemCommand;
use skyblock\commands\basic\ItemFlipCommand;
use skyblock\commands\basic\ItemModsCommand;
use skyblock\commands\basic\KitCommand;
use skyblock\commands\basic\MasksCommand;
use skyblock\commands\basic\NickCommand;
use skyblock\commands\basic\OnlinePlayersCommand;
use skyblock\commands\basic\PlayTimeCommand;
use skyblock\commands\basic\PrestigeCommand;
use skyblock\commands\basic\PreviewCommand;
use skyblock\commands\basic\QuestsCommand;
use skyblock\commands\basic\RealnameCommand;
use skyblock\commands\basic\RecipeCommand;
use skyblock\commands\basic\RemoveItemModCommand;
use skyblock\commands\basic\RemoveMaskCommand;
use skyblock\commands\basic\RestartCommand;
use skyblock\commands\basic\SeeBragCommand;
use skyblock\commands\basic\SeeItemCommand;
use skyblock\commands\basic\SellCommand;
use skyblock\commands\basic\ShopCommand;
use skyblock\commands\basic\SkillsCommand;
use skyblock\commands\basic\SlotBotCommand;
use skyblock\commands\basic\SpawnCommand;
use skyblock\commands\basic\StrongholdCommand;
use skyblock\commands\basic\TradeCommand;
use skyblock\commands\basic\VaccineCommand;
use skyblock\commands\basic\VanillaEnchantShopCommand;
use skyblock\commands\basic\WarpCommand;
use skyblock\commands\basic\WarpSpeedCommand;
use skyblock\commands\economy\EssenceCommand;
use skyblock\commands\economy\EssencePayCommand;
use skyblock\commands\economy\EssenceTopCommand;
use skyblock\commands\economy\EssenceWithdrawCommand;
use skyblock\commands\economy\FishpointsCommand;
use skyblock\commands\economy\FishpointsSetCommand;
use skyblock\commands\economy\GiveEssenceCommand;
use skyblock\commands\economy\GiveMoneyCommand;
use skyblock\commands\economy\GiveXpCommand;
use skyblock\commands\economy\MoneyCommand;
use skyblock\commands\economy\PayCommand;
use skyblock\commands\economy\SetEssenceCommand;
use skyblock\commands\economy\SetMoneyCommand;
use skyblock\commands\economy\SetXpCommand;
use skyblock\commands\economy\TakeEssenceCommand;
use skyblock\commands\economy\TakeMoneyCommand;
use skyblock\commands\economy\TakeXpCommand;
use skyblock\commands\economy\TinkererCommand;
use skyblock\commands\economy\TopMoneyCommand;
use skyblock\commands\economy\WithdrawCommand;
use skyblock\commands\economy\XpBottleCommand;
use skyblock\commands\staff\AccessoryCommand;
use skyblock\commands\staff\ArmorInvseeCommand;
use skyblock\commands\staff\BossCommand;
use skyblock\commands\staff\ClearCommand;
use skyblock\commands\staff\ClearlagCommand;
use skyblock\commands\staff\CombatZoneCommand;
use skyblock\commands\staff\ConsoleMsgCommand;
use skyblock\commands\staff\CrateCommand;
use skyblock\commands\staff\CustomBlockCommand;
use skyblock\commands\staff\DeathRestoreCommand;
use skyblock\commands\staff\EnderInvseeCommand;
use skyblock\commands\staff\EnhancementAttributeCommand;
use skyblock\commands\staff\FindCommand;
use skyblock\commands\staff\IntergalacticCoinCommand;
use skyblock\commands\staff\InvseeCommand;
use skyblock\commands\staff\ItemInfoCommand;
use skyblock\commands\staff\KothCommand;
use skyblock\commands\staff\LocationCommand;
use skyblock\commands\staff\MaskCommand;
use skyblock\commands\staff\CeEditCommand;
use skyblock\commands\staff\CustomEnchantCommand;
use skyblock\commands\staff\DamageRecordsCommand;
use skyblock\commands\staff\ItemModCommand;
use skyblock\commands\staff\LootboxCommand;
use skyblock\commands\staff\MinionCommand;
use skyblock\commands\staff\NBTCommand;
use skyblock\commands\staff\PathCommand;
use skyblock\commands\staff\PetCommand;
use skyblock\commands\staff\PotionCommand;
use skyblock\commands\staff\PvpZoneCommand;
use skyblock\commands\staff\RankCommand;
use skyblock\commands\staff\ReloadItemCommand;
use skyblock\commands\staff\ResetNpcQuestProgressCommand;
use skyblock\commands\staff\SayCommand;
use skyblock\commands\staff\SkinsCommand;
use skyblock\commands\staff\SpecialItemCommand;
use skyblock\commands\staff\SpecialSetCommand;
use skyblock\commands\staff\SpecialToolCommand;
use skyblock\commands\staff\SpeedCommand;
use skyblock\commands\staff\StaffModeCommand;
use skyblock\commands\staff\TeleportCommand;
use skyblock\commands\staff\TileCommand;
use skyblock\commands\staff\UnicodeCommand;
use skyblock\commands\staff\ZoneCommand;


class CommandHandler {

	public function __construct(private Main $plugin){
		$this->unregister("tell");
		$this->unregister("msg");
		$this->unregister("me");
		$this->unregister("say");
        $this->unregister("list");
        $this->unregister("clear");
        $this->unregister("ban");
        $this->unregister("pardon");
        $this->unregister("ban-ip");
        $this->unregister("kill");

		$this->plugin->getServer()->getCommandMap()->registerAll("skyblock", [
           // new ReclaimCommand("reclaim"),
            new ItemInfoCommand("iteminfo", ["ii"]),
            new TeleportCommand("teleport", ["tp"]),
            new ClearCommand("clear"),
			new IslandCommand("island", ["is", "isle"]),
			new SpecialItemCommand("specialitem", ["si"]),
			new NBTCommand("nbt"),
			new SpawnCommand("spawn", ["hub", "lobby"]),
			new CustomEnchantCommand("ce"),
            new StaffModeCommand("staffmode"),
			new ItemCommand("item"),
			new CollectCommand("collect"),
			new ItemModCommand("itemmod"),
			//new CustomEnchantShopCommand("ceshop", ["enchanter", "customenchantshop"]),
			//new SpecialSetCommand("specialset"),
			new IdCommand("id"),
			new SkinsCommand("skins", ["skin"]),
			new LootboxCommand("lootbox", ["lb"]),
			new FeedCommand("feed"),
			new HealCommand("heal"),
			new FixCommand("fix"),
			new VaccineCommand("vaccine"),
			new RemoveItemModCommand("removeitemmod"),
			new OnlinePlayersCommand("online", ["players", "onlineplayers", "list"]),
            new BlocksCommand("blocks", ["compact"]),

			new PlayTimeCommand("playtime", ["onlinetime"]),
			new SeeBragCommand("seebrag", ["cbrag"]),
			new SeeItemCommand("seeitem", ["citem"]),
			new TopMoneyCommand("topmoney", ["baltop"]),
			new EssenceTopCommand("essencetop", ["etop"]),
			new CoinflipCommand("coinflip", ["cf"]),
			new AuctionHouseCommand("ah", ["auctionhouse", "auction"]),

			new MoneyCommand("mymoney", ["money", "balance", "bal"]),
			new EssenceCommand("essence"),
			new SetMoneyCommand("setmoney"),
			new SetEssenceCommand("setessence"),
			new TakeEssenceCommand("takeessence"),
			new TakeMoneyCommand("takemoney"),
			new GiveMoneyCommand("givemoney"),
			new GiveEssenceCommand("giveessence"),
			new PayCommand("pay", ["moneypay", "paymoney"]),
			new EssencePayCommand("epay", ["essencepay"]),
			new SetXpCommand("setxp"),
			new TakeXpCommand("takexp"),
			new GiveXpCommand("givexp"),
			new TradeCommand("trade"),

			//new EssenceWithdrawCommand("ewithdraw", ["eessencewithdraw"]),
			new WithdrawCommand("withdraw"),
			new XpBottleCommand("xpbottle", ["xpb"]),

			new RankCommand("rank"),
			new TinkererCommand("tinkerer", ["alchemist"]),
			new KitCommand("kit"),
			new DamageRecordsCommand("damagerecords"),
			new CeEditCommand("ceedit"),

			new ChunkBorders("chunkborders"),
			new ShopCommand("shop"),
			new MaskCommand("mask"),
			new RemoveMaskCommand("removemask"),
			new ClearlagCommand("clearlag"),

			new SellCommand("sell"),
			new LocationCommand("location", ["find"]),

			new FlyCommand("fly"),
			new CrateCommand("crate"),
			new AgeCommand("age"),


			new MsgCommand("msg", ["tell", "w"]),
			new RecallCommand("recall", ["r", "reply"]),
			new UnblockCommand("unblock"),
			new BlockCommand("block"),


			new ConsoleMsgCommand("consolemsg", ["consoletell"]),

			new SpecialToolCommand("specialtool"),
			new DailyQuestsCommand("dailyquests", ["dailyquest", "dq"]),
			new StrongholdCommand("stronghold"),

			new PvpZoneCommand("pvpzone"),
			new WarpCommand("warp"),

			new TileCommand("tile"),


			new IntergalacticCoinCommand("intergalacticcoin"),
			new RestartCommand("restart"),
			new WarpSpeedCommand("warpspeed"),
			new CeInfoCommand("ceinfo", ["customenchants", "ces"]),
		//	new VanillaEnchantShopCommand("vanillaenchantshop"),
			new QuestsCommand("quests"),
			new SayCommand("say"),
			new AutoSellCommand("autosell"),

			new NickCommand("nick"),
			new RealnameCommand("realname"),
			new FundCommand("fund"),

			new PreviewCommand("preview"),


			new MasksCommand("masks"),
			new ItemModsCommand("itemmods"),

			new SlotBotCommand("slotbot"),
			new DeathRestoreCommand("deathrestore"),
			new CeFixCommand("cefix"),

			new InvseeCommand("cinv"),
			new EnderInvseeCommand("cendinv"),
			new ArmorInvseeCommand("carmorinv"),
			new BossCommand("boss"),

			new FindCommand("find"),

			new PetCommand("pet"),
			new CustomBlockCommand("customblock"),
			//new ReloadItemCommand("reloaditem"),
			new BountyCommand("bounty"),

			new ItemFlipCommand("itemflip", ["if"]),
			new PathCommand("pathfind"),
			new SpeedCommand("speed"),

			new SkillsCommand("skills"),
			new CombatZoneCommand("combatzone"),
			new ZoneCommand("zone"),
			new HotspotCommand("hotspot", ["hotspots"]),
			new PotionCommand("potion"),
			new EffectsCommand("effects"),
			new UnicodeCommand("unicode"),
			new RecipeCommand("recipe", ["recipes", "rc", "rcp"]),

			new MinionCommand("miner"),

			new CollectionCommand("collection"),

			//new AccessoryCommand("accessory"),
			new ResetNpcQuestProgressCommand("resetnpcquest"),

			new BankCommand("bank"),
		]);
	}

	public function unregister(string $cmd): bool {
		$a = $this->plugin->getServer()->getCommandMap()->getCommand($cmd);
		if($a !== null){
			return $this->plugin->getServer()->getCommandMap()->unregister($a);
		}

		return false;
	}
}