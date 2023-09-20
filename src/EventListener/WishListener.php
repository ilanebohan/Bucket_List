<?php
namespace App\EventListener;

use App\Entity\Wish;

class WishListener
{

    public function preCreate(Wish $wish): void
    {
        $wish->setDateCreated(new \DateTime());
        $wish->setDateUpdated(new \DateTime());
    }
    public function preUpdate(Wish $wish): void
    {
        $wish->setDateUpdated(new \DateTime());
    }

}