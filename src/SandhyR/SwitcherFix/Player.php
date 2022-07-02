<?php

namespace SandhyR\SwitcherFix;

use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Armor;
use pocketmine\item\Totem;
use pocketmine\player\Player as PMPlayer;

class Player extends PMPlayer{

    public function applyDamageModifiers(EntityDamageEvent $source): void
    {
        if($this->lastDamageCause !== null && $this->attackTime > 0){
           $source->cancel();
        }
        if($source->canBeReducedByArmor()){
            //MCPE uses the same system as PC did pre-1.9
            $source->setModifier(-$source->getFinalDamage() * $this->getArmorPoints() * 0.04, EntityDamageEvent::MODIFIER_ARMOR);
        }

        $cause = $source->getCause();
        if(($resistance = $this->effectManager->get(VanillaEffects::RESISTANCE())) !== null && $cause !== EntityDamageEvent::CAUSE_VOID && $cause !== EntityDamageEvent::CAUSE_SUICIDE){
            $source->setModifier(-$source->getFinalDamage() * min(1, 0.2 * $resistance->getEffectLevel()), EntityDamageEvent::MODIFIER_RESISTANCE);
        }

        $totalEpf = 0;
        foreach($this->armorInventory->getContents() as $item){
            if($item instanceof Armor){
                $totalEpf += $item->getEnchantmentProtectionFactor($source);
            }
        }
        $source->setModifier(-$source->getFinalDamage() * min(ceil(min($totalEpf, 25) * (mt_rand(50, 100) / 100)), 20) * 0.04, EntityDamageEvent::MODIFIER_ARMOR_ENCHANTMENTS);

        $source->setModifier(-min($this->getAbsorption(), $source->getFinalDamage()), EntityDamageEvent::MODIFIER_ABSORPTION);
        if($cause !== EntityDamageEvent::CAUSE_SUICIDE && $cause !== EntityDamageEvent::CAUSE_VOID
            && ($this->inventory->getItemInHand() instanceof Totem || $this->offHandInventory->getItem(0) instanceof Totem)){

            $compensation = $this->getHealth() - $source->getFinalDamage() - 1;
            if($compensation < 0){
                $source->setModifier($compensation, EntityDamageEvent::MODIFIER_TOTEM);
            }
        }
    }
}
