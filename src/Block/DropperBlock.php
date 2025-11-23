<?php

declare(strict_types=1);

namespace Dropper\Block;

use Dropper\Tile\DropperTile;
use pocketmine\block\Block;
use pocketmine\block\Opaque;
use pocketmine\block\utils\AnyFacingTrait;
use pocketmine\block\utils\PoweredByRedstone;
use pocketmine\block\utils\PoweredByRedstoneTrait;
use pocketmine\item\Item;
use pocketmine\math\Facing;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\BlockTransaction;

class DropperBlock extends Opaque implements PoweredByRedstone
{
    use AnyFacingTrait;
    use PoweredByRedstoneTrait;

    public function onButtonPressed(): void
    {
        $tile = $this->position->getWorld()->getTile($this->position);
        if ($tile instanceof DropperTile) {
            $inventory = $tile->getInventory();
            if ($contents = $inventory->getContents()) {
                /** @var Item[] $items */
                $items = array_filter($contents, fn(Item $item) => !$item->isNull());
                $key = array_rand($items);
                $this->position->getWorld()->dropItem(
                    $this->position->getSide($this->facing),
                    $items[$key]->pop(),
                    Vector3::zero()->getSide($this->facing)->multiply(0.5)
                );
                $inventory->setItem($key, $items[$key]);
            }
        }
    }

    public function place(BlockTransaction $tx, Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, ?Player $player = null): bool
    {
        if ($player !== null) {
            if (abs($player->getPosition()->x - $this->position->x) < 2 && abs($player->getPosition()->z - $this->position->z) < 2) {
                $y = $player->getEyePos()->y;

                if ($y - $this->position->y > 2) {
                    $this->facing = Facing::UP;
                } elseif ($this->position->y - $y > 0) {
                    $this->facing = Facing::DOWN;
                } else {
                    $this->facing = Facing::opposite($player->getHorizontalFacing());
                }
            } else {
                $this->facing = Facing::opposite($player->getHorizontalFacing());
            }
        }
        return parent::place($tx, $item, $blockReplace, $blockClicked, $face, $clickVector, $player);
    }

    public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null, array &$returnedItems = []): bool
    {
        if ($player !== null) {
            /**
             * @var DropperTile $tile
             */
            $tile = $this->position->getWorld()->getTile($this->position);
            if ($tile instanceof DropperTile) {
                $tile->getMenu()->send($player);
            }
            return true;
        }
        return false;
    }
}