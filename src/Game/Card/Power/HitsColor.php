<?php

namespace Game\Card\Power;

use Game\Card;

class HitsColor implements Card\Power
{
    public function __construct(
        public readonly Card\Color $colorToHit,
        public readonly int $hitStrength
    ) {

    }

    public function applyTo(Card $card): void
    {
        if ($card->color === $this->colorToHit)
        {
            $card->takeHit(
                $this->hitStrength
            );
        }
    }
}
