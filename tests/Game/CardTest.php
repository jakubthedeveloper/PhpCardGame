<?php

namespace PhpCardGame\Game;

use PHPUnit\Framework\TestCase;

/**
 * @covers \PhpCardGame\Game\Card
 * @covers \PhpCardGame\Game\Card\Color
 * @covers \PhpCardGame\Game\Card\Power
 */
class CardTest extends TestCase
{
    public function testCardHasRequiredAttributes(): void
    {
        $card = new Card(
            color: Card\Color::Blue,
            points: 7,
        );

        $this->assertEquals(Card\Color::Blue, $card->color);
        $this->assertEquals(7, $card->points());
    }

    public function testCardHasPowers(): void
    {
        $card = new Card(
            color: Card\Color::Blue,
            points: 10,
            powers: [
                new Card\Power(
                    colorToHit: Card\Color::Red,
                    hitStrength: 5
                ),
                new Card\Power(
                    colorToHit: Card\Color::Green,
                    hitStrength: 2
                )
            ]
        );

        $this->assertEquals(
            Card\Color::Red,
            $card->powers[0]->colorToHit
        );

        $this->assertEquals(
            5,
            $card->powers[0]->hitStrength
        );

        $this->assertEquals(
            Card\Color::Green,
            $card->powers[1]->colorToHit
        );

        $this->assertEquals(
            2,
            $card->powers[1]->hitStrength
        );
    }

    public function testCardHitsAnotherCard(): void
    {
        $power = new Card\Power(
            colorToHit: Card\Color::Yellow,
            hitStrength: 3
        );

        $cardThatHits = new Card(
            color: Card\Color::Red,
            points: 5,
            powers: [
                $power
            ]
        );

        $cartThatIsBeingHit = new Card(
            color: Card\Color::Yellow,
            points: 10
        );

        $this->assertEquals(10, $cartThatIsBeingHit->points());

        $cardThatHits->hit($cartThatIsBeingHit);
        $this->assertEquals(7, $cartThatIsBeingHit->points());

        $cardThatHits->hit($cartThatIsBeingHit);
        $this->assertEquals(4, $cartThatIsBeingHit->points());

        $cardThatHits->hit($cartThatIsBeingHit);
        $this->assertEquals(1, $cartThatIsBeingHit->points());

        $cardThatHits->hit($cartThatIsBeingHit);
        $this->assertEquals(0, $cartThatIsBeingHit->points());

        $cardThatHits->hit($cartThatIsBeingHit);
        $this->assertEquals(0, $cartThatIsBeingHit->points());
    }

    public function testCardDoesNotHitIfPowersDoesNotAllow(): void
    {
        $power = new Card\Power(
            colorToHit: Card\Color::Blue,
            hitStrength: 3
        );

        $cardThatHits = new Card(
            color: Card\Color::Red,
            points: 5,
            powers: [
                $power
            ]
        );

        $cartThatIsBeingHit = new Card(
            color: Card\Color::Yellow,
            points: 10
        );

        $this->assertEquals(10, $cartThatIsBeingHit->points());

        $cardThatHits->hit($cartThatIsBeingHit);
        $this->assertEquals(10, $cartThatIsBeingHit->points());
    }
}
