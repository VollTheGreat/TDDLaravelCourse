<?php

namespace App\Http\Controllers;

use App\Billing\PaymentFailedException;
use App\Concert;
use App\Exceptions\NotEnoughTicketsException;
use App\Order;
use App\Reservation;
use Illuminate\Http\Request;
use App\Billing\PaymentGateway;

class ConcertOrdersController extends Controller
{
    /**
     * @var
     */
    private $paymentGateway;

    function __construct(PaymentGateway $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }
    public function store($concertId)
    {
        $concert = Concert::published()->findorFail($concertId);
        $this->validate(
            request(), [
            'email'           => 'required|email',
            'ticket_quantity' => [
                'required',
                'integer',
                'min:1',
            ],
            'payment_token'   => 'required',
        ]);
        try {
            //find
            $tickets = $concert->reserveTickets(request('ticket_quantity'));
            $reservation = new Reservation($tickets);
            //charge
            $this->paymentGateway->charge($reservation->totalCost(), request('payment_token'));
            //create order
            $order = Order::forTickets($tickets, request('email'),$reservation->totalCost());

            return response()->json($order, 201);
        } catch (PaymentFailedException $e) {
            $reservation->cancel();
            return response()->json([], 422);
        } catch (NotEnoughTicketsException $e) {
            return response()->json([], 422);
        }
    }
}
