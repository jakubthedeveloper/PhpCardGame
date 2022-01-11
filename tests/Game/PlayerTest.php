<?php

namespace PhpCardGame\Game;

use PhpCardGame\Game\Player\Table;
use PHPUnit\Framework\TestCase;

/**
 * @covers \PhpCardGame\Game\Player
 * @covers \PhpCardGame\Game\Player\Cards
 * @covers \PhpCardGame\Game\Player\Table
 * @covers \PhpCardGame\Game\Card
 */
class PlayerTest extends TestCase
{
    public function testPlayerHasCards(): void
    {
        $blueCard = new Card(
            color: Card\Color::Blue,
            points: 7,
        );

        $redCard = new Card(
            color: Card\Color::Red,
            points: 4,
        );

        $yellowCard = new Card(
            color: Card\Color::Yellow,
            points: 2,
        );

        $greenCard = new Card(
            color: Card\Color::Green,
            points: 3,
        );

        $playerCards = new Player\Cards(
            cards: [
                $blueCard,
                $redCard,
                $yellowCard,
                $greenCard
            ]
        );

        $player = new Player(
            "John Doe",
            cards: $playerCards,
            table: new Table([])
        );

        $this->assertEquals($blueCard, $player->getCardOnHand(0));
        $this->assertEquals($redCard, $player->getCardOnHand(1));
        $this->assertEquals($yellowCard, $player->getCardOnHand(2));
        $this->assertEquals($greenCard, $player->getCardOnHand(3));
    }
}
