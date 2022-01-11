<?php

namespace PhpCardGame\Game;

use PhpCardGame\Game\Exception\CardDoesNotExist;
use PhpCardGame\Game\Player\Table;
use PHPUnit\Framework\TestCase;

/**
 * @covers \PhpCardGame\Game\Player\Table
 * @covers \PhpCardGame\Game\Card
 * @covers \PhpCardGame\Game\Card\Power
 * @covers \PhpCardGame\Game\Card\Color
 */
class TableTest extends TestCase
{
    public function testPlayerTableHasCards(): void
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

        $playerTable = new Table(
            cards: [
                $blueCard,
                $redCard,
                $yellowCard,
                $greenCard
            ]
        );

        $this->assertEquals($blueCard, $playerTable->getCard(0));
        $this->assertEquals($redCard, $playerTable->getCard(1));
        $this->assertEquals($yellowCard, $playerTable->getCard(2));
        $this->assertEquals($greenCard, $playerTable->getCard(3));
    }

    public function testHitByCard(): void
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

        $greenCardOne = new Card(
            color: Card\Color::Green,
            points: 3,
        );

        $greenCardTwo = new Card(
            color: Card\Color::Green,
            points: 9,
        );

        $playerTable = new Table(
            cards: [
                $blueCard,
                $redCard,
                $yellowCard,
                $greenCardOne,
                $greenCardTwo
            ]
        );

        $cardThatHits = new Card(
            color: Card\Color::Red,
            points: 8,
            powers: [
                new Card\Power(
                    colorToHit: Card\Color::Yellow,
                    hitStrength: 1
                ),
                new Card\Power(
                    colorToHit: Card\Color::Green,
                    hitStrength: 2
                ),
            ]
        );

        // Check points before the hit
        $this->assertEquals(7, $blueCard->points());
        $this->assertEquals(4, $redCard->points());
        $this->assertEquals(2, $yellowCard->points());
        $this->assertEquals(3, $greenCardOne->points());
        $this->assertEquals(9, $greenCardTwo->points());

        // Hit
        $playerTable->hitBy($cardThatHits);

        // Check points after the hit
        $this->assertEquals(7, $blueCard->points());
        $this->assertEquals(4, $redCard->points());
        $this->assertEquals(1, $yellowCard->points());
        $this->assertEquals(1, $greenCardOne->points());
        $this->assertEquals(7, $greenCardTwo->points());
    }

    public function testGetCard(): void
    {
        $blueCard = new Card(
            color: Card\Color::Blue,
            points: 7,
        );

        $playerTable = new Table(
            cards: [
                $blueCard
            ]
        );

        $this->assertEquals($blueCard, $playerTable->getCard(0));
    }

    public function testGetCardThrowsExceptionWhenCardDoesNotExist(): void
    {
        $blueCard = new Card(
            color: Card\Color::Blue,
            points: 7,
        );

        $playerTable = new Table(
            cards: [
                $blueCard
            ]
        );

        $this->expectException(CardDoesNotExist::class);
        $this->expectExceptionMessage("Card with index 1 does not exist within the cards on the player table.");

        $playerTable->getCard(1);
    }
}
