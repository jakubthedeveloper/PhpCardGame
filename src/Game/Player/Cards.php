<?php

namespace PhpCardGame\Game\Player;

use PhpCardGame\Game\Card;
use PhpCardGame\Game\Exception\CardDoesNotExist;

class Cards
{
    public function __construct(
        /** @var Card[] */
        private array $cards = []
    ) {

    }

    /**
     * @throws CardDoesNotExist
     */
    public function get(int $index): Card
    {
        if (false === array_key_exists($index, $this->cards)) {
            throw new CardDoesNotExist(
                sprintf("Card with index %s does not exist within player cards.", $index)
            );
        }

        return $this->cards[$index];
    }

    /**
     * @throws CardDoesNotExist
     */
    public function pop(int $index): Card
    {
        $card = $this->get($index);

        unset($this->cards[$index]);
        $this->cards = array_values($this->cards);

        return $card;
    }


    /**
     * @return Card[]
     */
    public function getAll(): array
    {
        return $this->cards;
    }

    public function add(Card $card): void
    {
        $this->cards[] = $card;
    }
}
