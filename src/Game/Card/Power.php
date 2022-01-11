<?php

namespace Game\Card;

use Game\Card;

interface Power
{
    public function applyTo(Card $card): void;
}
