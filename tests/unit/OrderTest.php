<?php
use App\Concert;
use App\Order;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
class OrderTest extends TestCase
{
    use DatabaseMigrations;

    /** @test* */
    function creating_order_from_tickets_email_and_amount()
    {
        // Arrange
        $concert = factory(Concert::class)->create();
        $concert->addTickets(5);
        $this->assertEquals(5, $concert->ticketsRemaining());
        //Act
        $order = Order::forTickets($concert->findTickets(3),'john@example.com',3600);
        //Assert
        $this->assertEquals('john@example.com', $order->email);
        $this->assertEquals(3600, $order->amount);
        $this->assertEquals(3, $order->ticketQuantity());
        $this->assertEquals(2, $concert->ticketsRemaining());

    }

    /** @test* */
    function converting_to_an_array()
    {
        // Arrange
        $concert = factory(Concert::class)->create();
        $concert->addTickets(3);
        $order= $concert->orderTickets('jane@example.com',3);
        //Act
        $result = $order->toArray();

        $this->assertEquals([
            'email' => 'jane@example.com',
            'ticket_quantity' => 3,
            'amount' => 6000,
        ], $result);
        //Assert

    }


}
