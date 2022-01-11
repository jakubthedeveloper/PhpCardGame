<?php

namespace PhpCardGame\Game;

use PhpCardGame\Game\Card\Power;

class Card
{
    public function __construct(
        public readonly Card\Color $color,
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
        /** @var Card\Power $power */
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
