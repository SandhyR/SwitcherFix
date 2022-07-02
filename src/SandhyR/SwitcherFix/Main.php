<?php

namespace SandhyR\SwitcherFix;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCreationEvent;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener{

    public function onEnable(): void
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onPlayerCreation(PlayerCreationEvent $event){
        $event->setPlayerClass(Player::class);
    }
}