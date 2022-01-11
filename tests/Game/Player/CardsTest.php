<?php

namespace PhpCardGame\Game;

use PhpCardGame\Game;
use PHPUnit\Framework\TestCase;

/**
 * @covers \PhpCardGame\Game\Player\Cards
 * @covers \PhpCardGame\Game\Card
 * @covers \PhpCardGame\Game\Card\Color
 */
class CardsTest extends TestCase
{
    public function testPlayerCardsAreSet(): void
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

        $playerCards = new Game\Player\Cards(
            cards: [
                $blueCard,
                $redCard,
                $yellowCard,
                $greenCard
            ]
        );

        $this->assertEquals($blueCard, $playerCards->get(0));
        $this->assertEquals($redCard, $playerCards->get(1));
        $this->assertEquals($yellowCard, $playerCards->get(2));
        $this->assertEquals($greenCard, $playerCards->get(3));
    }

    public function testCardDoesNotExist(): void
    {
        $blueCard = new Card(
            color: Card\Color::Blue,
            points: 7,
        );

        $redCard = new Card(
            color: Card\Color::Red,
            points: 4,
        );

        $playerCards = new Game\Player\Cards(
            cards: [
                $blueCard,
                $redCard
            ]
        );

        $this->expectException(Game\Exception\CardDoesNotExist::class);
        $this->expectExceptionMessage("Card with index 3 does not exist within player cards.");

        $playerCards->get(3);
    }
}
