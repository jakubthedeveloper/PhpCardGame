<?php

namespace Game;

use Game\Card\Power;
use Game\Card\Color;

class Card
{
    public function __construct(
        public readonly string $name,
        public readonly Color $color,
        private int $points,
        /** @var Power[] */
        public readonly array $powers = [],
    ) {

    }

    public function points(): int
    {
        return $this->points;
    }

    public function hit(Card $cartThatIsBeingHit): void
    {
        /** @var Power $power */
        foreach ($this->powers as $power)
        {
            $power->applyTo($cartThatIsBeingHit);
        }
    }

    public function takeHit(int $strength): void
    {
        if ($strength > $this->points) {
            $this->points = 0;
            return;
        }

        $this->points -= $strength;
    }
}
