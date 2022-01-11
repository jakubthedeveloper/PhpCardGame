<?php

namespace PhpCardGame\Game\Player;

use PhpCardGame\Game\Card;
use PhpCardGame\Game\Exception\CardDoesNotExist;

class Table
{
    public function __construct(
        /** @var Card[] */
        private array $cards = []
    ) {

    }

    /**
     * @throws CardDoesNotExist
     */
    public function getCard(int $index): Card
    {
        if (false === array_key_exists($index, $this->cards)) {
            throw new CardDoesNotExist(
                sprintf("Card with index %s does not exist within the cards on the player table.", $index)
            );
        }

        return $this->cards[$index];
    }

    public function hitBy(Card $card): void
    {
        foreach ($this->cards as $tableCard) {
            $card->hit($tableCard);
        }
    }

    public function putCard(Card $card): void
    {
        $this->cards[] = $card;
    }

    /**
     * @return Card[]
     */
    public function getCards(): array
    {
        return $this->cards;
    }
}
