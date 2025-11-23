<?php

declare(strict_types=1);

namespace Dropper\Tile;

use Dropper\Dropper;
use muqsit\invmenu\InvMenu;
use pocketmine\block\tile\Container;
use pocketmine\block\tile\ContainerTrait;
use pocketmine\block\tile\Nameable;
use pocketmine\block\tile\NameableTrait;
use pocketmine\block\tile\Spawnable;
use pocketmine\inventory\Inventory;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\world\World;

class DropperTile extends Spawnable implements Container, Nameable
{
    use ContainerTrait;
    use NameableTrait;

    private InvMenu $menu;

    public function __construct(World $world, Vector3 $pos)
    {
        parent::__construct($world, $pos);
        $this->menu = InvMenu::create(Dropper::INVMENU_TYPE_DROPPER);
    }

    public function readSaveData(CompoundTag $nbt): void
    {
        $this->loadItems($nbt);
        $this->loadName($nbt);
    }

    protected function writeSaveData(CompoundTag $nbt): void
    {
        $this->saveItems($nbt);
        $this->saveName($nbt);
    }

    public function getDefaultName(): string
    {
        return "Dropper";
    }

    public function getRealInventory(): Inventory
    {
        return $this->menu->getInventory();
    }

    public function getInventory(): Inventory
    {
        return $this->menu->getInventory();
    }

    public function getMenu(): InvMenu
    {
        return $this->menu;
    }
}