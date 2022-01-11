<?php

namespace PhpCardGame\Game;

use JetBrains\PhpStorm\Pure;
use PhpCardGame\Game\Player\Cards;
use PhpCardGame\Game\Player\Table;

class Player
{
    public function __construct(
        private string $name,
        private Cards $cards,
        private Table $table
    ) {

    }

    public function putCardOnTable(Card $card): void
    {
        $this->table->putCard($card);
    }

    /**
     * @throws Exception\CardDoesNotExist
     */
    public function popCardFromHand(int $cardIndex): Card
    {
        return $this->cards->pop($cardIndex);
    }

    public function hitTableBy(Card $card): void
    {
        $this->table->hitBy($card);
    }

    /**
     * @return Card[]
     */
    #[Pure] public function getCardsOnTable(): array
    {
        return $this->table->getCards();
    }


    public function getCardOnHand(int $cardIndex): Card
    {
        return $this->cards->get($cardIndex);
    }

    /**
     * @return Card[]
     */
    #[Pure] public function getCardsOnHand(): array
    {
        return $this->cards->getAll();
    }

    #[Pure] public function points(): int
    {
        $points = 0;

        foreach ($this->table->getCards() as $card) {
            $points += $card->points();
        }

        return $points;
    }

    /**
     * We need this getter instead of read-only property, because in phpunit we cannot mock readonly properties.
     */
    public function name(): string
    {
        return $this->name;
    }
}
