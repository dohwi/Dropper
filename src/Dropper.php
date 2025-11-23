<?php

declare(strict_types=1);

namespace Dropper;

use Dropper\Block\DropperBlock;
use Dropper\Listener\EventListener;
use Dropper\Tile\DropperTile;
use Dropper\Extra\ExtraDropperBlock;
use muqsit\invmenu\InvMenuHandler;
use muqsit\invmenu\type\util\InvMenuTypeBuilders;
use pocketmine\block\RuntimeBlockStateRegistry;
use pocketmine\block\tile\TileFactory;
use pocketmine\data\bedrock\block\BlockStateNames;
use pocketmine\data\bedrock\block\BlockTypeNames;
use pocketmine\data\bedrock\block\convert\BlockStateReader;
use pocketmine\data\bedrock\block\convert\BlockStateWriter;
use pocketmine\inventory\CreativeInventory;
use pocketmine\item\StringToItemParser;
use pocketmine\network\mcpe\protocol\types\inventory\WindowTypes;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\AsyncTask;
use pocketmine\world\format\io\GlobalBlockStateHandlers;

final class Dropper extends PluginBase
{
    public const INVMENU_TYPE_DROPPER = "invmenu:dropper";

    protected function onEnable(): void
    {
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);

        TileFactory::getInstance()->register(DropperTile::class, [BlockTypeNames::DROPPER]);

        self::registerBlock();

        $this->getServer()->getAsyncPool()->addWorkerStartHook(function (int $worker): void {
            $this->getServer()->getAsyncPool()->submitTaskToWorker(new class extends AsyncTask {
                public function onRun(): void
                {
                    Dropper::registerBlock();
                }
            }, $worker);
        });

        CreativeInventory::getInstance()->add(ExtraDropperBlock::DROPPER()->asItem());

        if(!InvMenuHandler::isRegistered()) {
            InvMenuHandler::register($this);
            InvMenuHandler::getTypeRegistry()->register(self::INVMENU_TYPE_DROPPER, InvMenuTypeBuilders::BLOCK_ACTOR_FIXED()
                ->setBlock(ExtraDropperBlock::DROPPER())
                ->setSize(9)
                ->setBlockActorId("Dropper")
                ->setNetworkWindowType(WindowTypes::DROPPER)
                ->build()
            );
        }
    }

    public static function registerBlock(): void
    {
        $block = ExtraDropperBlock::DROPPER();
        self::registerSimpleBlock($block);
    }

    private static function registerSimpleBlock(DropperBlock $block): void
    {
        RuntimeBlockStateRegistry::getInstance()->register($block);
        GlobalBlockStateHandlers::getDeserializer()->map(
            id: BlockTypeNames::DROPPER,
            c: fn(BlockStateReader $reader): DropperBlock => (clone $block)
                ->setFacing($reader->readFacingDirection())
                ->setPowered($reader->readBool(BlockStateNames::TRIGGERED_BIT))
        );
        GlobalBlockStateHandlers::getSerializer()->map(
            block: $block,
            serializer: fn(DropperBlock $block) => BlockStateWriter::create(BlockTypeNames::DROPPER)
                ->writeFacingDirection($block->getFacing())
                ->writeBool(BlockStateNames::TRIGGERED_BIT, $block->isPowered())
        );
        StringToItemParser::getInstance()->registerBlock(BlockTypeNames::DROPPER, fn() => clone $block);
    }
}