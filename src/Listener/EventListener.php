<?php

declare(strict_types=1);

namespace Dropper\Listener;

use Dropper\Block\DropperBlock;
use pocketmine\block\Button;
use pocketmine\event\block\BlockUpdateEvent;
use pocketmine\event\Listener;
use pocketmine\math\Facing;

final class EventListener implements Listener
{
    public function onBlock(BlockUpdateEvent $event): void
    {
        $block = $event->getBlock();
        if ($block instanceof Button) {
            if ($block->isPressed()) {
                $target = $block->getSide(Facing::opposite($block->getFacing()));
                if ($target instanceof DropperBlock) {
                    $target->onButtonPressed();
                }
            }
        }
    }
}