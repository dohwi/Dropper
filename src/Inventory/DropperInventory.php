<?php

declare(strict_types=1);

namespace Dropper\Inventory;

use pocketmine\block\inventory\BlockInventory;
use pocketmine\block\inventory\BlockInventoryTrait;
use pocketmine\inventory\SimpleInventory;
use pocketmine\world\Position;

final class DropperInventory extends SimpleInventory implements BlockInventory
{
    use BlockInventoryTrait;

    public function __construct(Position $holder)
    {
        $this->holder = $holder;
        parent::__construct(9);
    }
}