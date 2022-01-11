<?php

namespace PhpCardGame\Game\Card;

use PhpCardGame\Game\Card;

class Power
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
