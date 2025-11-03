<?php

declare(strict_types=1);

namespace Dropper\Extra;

use Dropper\Block\DropperBlock;
use Dropper\Tile\DropperTile;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockTypeIds;
use pocketmine\block\BlockTypeInfo;
use pocketmine\item\ToolTier;
use pocketmine\utils\CloningRegistryTrait;

/**
 * @generate-registry-docblock
 * @method static DropperBlock DROPPER()
 */
final class ExtraDropperBlock
{
    use CloningRegistryTrait;

    protected static function register(int $id): void
    {
        $block = new DropperBlock(
            new BlockIdentifier($id, DropperTile::class),
            "Dropper",
            new BlockTypeInfo(
                BlockBreakInfo::pickaxe(3.0, ToolTier::WOOD, 24.0)
            )
        );
        self::_registryRegister("dropper", $block);
    }

    public static function getAll(): array
    {
        return self::_registryGetAll();
    }

    protected static function setup(): void
    {
        self::register(BlockTypeIds::newId());
    }
}