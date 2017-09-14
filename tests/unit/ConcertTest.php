<?php
use App\Concert;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Exceptions\NotEnoughTicketsException;
class ConcertTest extends TestCase
{
    use DatabaseMigrations;

    /** @test* */
    function can_get_formatted_date()
    {
        $concert = factory(Concert::class)->make([
            'date' => Carbon::parse(('2016-12-01 8:00pm'))
        ]);

        $this->assertEquals('December 1, 2016', $concert->formatted_date);
    }

    /** @test* */
    function can_get_formatted_start_time()
    {
        $concert = factory(Concert::class)->make([
            'date' => Carbon::parse(('2016-12-01 17:00:00'))
        ]);

        $this->assertEquals('5:00pm', $concert->formatted_start_time);

    }

    /** @test* */
    function can_get_ticket_price_in_dollars()
    {
        $concert = factory(Concert::class)->make([
            'ticket_price' => 6750,
        ]);
        self::assertEquals('67.50', $concert->ticket_price_in_dollars);
    }

    /** @test* */
    function concerts_with_published_at_date_are_published()
    {
        $publishedConcertA = factory(Concert::class)->states('published')->create();
        $publishedConcertB = factory(Concert::class)->states('published')->create();
        $unpublishedConcert = factory(Concert::class)->states('unpublished')->create();

        $publishedConcerts = Concert::published()->get();

        $this->assertTrue($publishedConcerts->contains($publishedConcertA));
        $this->assertTrue($publishedConcerts->contains($publishedConcertB));
        $this->assertFalse($publishedConcerts->contains($unpublishedConcert));
    }

    /** @test* */
    function can_order_concert_tickets()
    {
        // Arrange
        $concert = factory(Concert::class)->create();
        $concert->addTickets(3);
        //Act
        $order = $concert->orderTickets('jane@example.com', 3);
        //Assert
        $this->assertEquals('jane@example.com', $order->email);
        $this->assertEquals(3, $order->tickets()->count());

    }

    /** @test* */
    function can_add_tickets()
    {
        // Arrange
        $concert = factory(Concert::class)->create();
        //Act
        $concert->addTickets(50);
        //Assert
        $this->assertEquals(50, $concert->ticketsRemaining());

    }

    /** @test* */
    function tickets_remaining_does_not_include_tickets_associated_with_orders()
    {
        // Arrange
        $concert = factory(Concert::class)->create();
        $concert->addTickets(50);
        $concert->orderTickets('jane@example.com', 30);
        //Act

        //Assert
        $this->assertEquals(20, $concert->ticketsRemaining());
    }

    /** @test* */
    function trying_to_purchase_more_tickets_then_remain_throws_exception()
    {
        // Arrange
        $concert = factory(Concert::class)->create();
        $concert->addTickets(10);

        try{
            $concert->orderTickets('jane@example.com', 11);
        }catch (NotEnoughTicketsException $e){
            $order = $concert->orders()->where('email','jane@example.com')->first();
            $this->assertNull($order);
            $this->assertEquals(10, $concert->ticketsRemaining());
            return ;
        }
        //Act
        $this->fail('Order sucseeded but there is not enought tickets');
        //Assert

    }

    /** @test* */
    function cannot_order_tickets_that_have_already_been_purchased()
    {
        // Arrange
        $concert = factory(Concert::class)->create();
        $concert->addTickets(10);
        $concert->orderTickets('jane@example.com', 8);
        //Act
        try{
            $concert->orderTickets('john@example.com', 3);
        }catch (NotEnoughTicketsException $e){
            $johnsOrder = $concert->orders()->where('email','john@example.com')->first();
            $this->assertNull($johnsOrder);
            $this->assertEquals(2, $concert->ticketsRemaining());
            return ;
        }
        //Act
        $this->fail('Order sucseeded but there is not enought tickets');
    }
    /** @test */
    function can_reserve_available_tickets()
    {
        $concert = factory(Concert::class)->create()->addTickets(3);
        $this->assertEquals(3, $concert->ticketsRemaining());

        $reserveTickets = $concert->reserveTickets(2, 'john.unit@example.com');

       $this->assertCount(2, $reserveTickets);
//        $this->assertEquals('john.unit@example.com', $reservation->email());
        $this->assertEquals(1, $concert->ticketsRemaining());
    }

    /** @test* */
    function cannot_reserve_tickets_that_are_already_been_purchased()
    {
        // Arrange
        $concert = factory(Concert::class)->create()->addTickets(3);
        $concert->orderTickets('jane@example.com', 2);
        //Act
        try{
            $concert->reserveTickets(2,'jane@example.com');
        }catch (NotEnoughTicketsException $e){
            $this->assertEquals(1, $concert->ticketsRemaining());
            return;
        }
        //Assert
        $this->fail();
    }
    /** @test* */
    function cannot_reserve_tickets_that_are_already_been_reserved()
    {
        // Arrange
        $concert = factory(Concert::class)->create()->addTickets(3);
        $concert->reserveTickets(2);
        //Act
        try{
            $concert->reserveTickets(2);
        }catch (NotEnoughTicketsException $e){
            $this->assertEquals(1, $concert->ticketsRemaining());
            return;
        }
        //Assert
        $this->fail();
    }
}
