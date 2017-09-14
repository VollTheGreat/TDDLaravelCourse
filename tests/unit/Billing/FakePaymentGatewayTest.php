<?php
use App\Billing\FakePaymentGateway;
use App\Billing\PaymentFailedException;
use App\Billing\PaymentGateway;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FakePaymentGatewayTest extends TestCase
{
    /** @test* */
    function charges_with_a_valid_payment_are_succsesfull()
    {
        // Arrange
        $paymentGateway = new FakePaymentGateway();
        $this->app->instance(PaymentGateway::class, $paymentGateway);
        //Act
        $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());
        //Assert
        $this->assertEquals(2500, $paymentGateway->totalCharges());
    }
    /** @test* */
    function charges_with_invalid_payment_token_fail()
    {
        try {
            $paymentGateway = new FakePaymentGateway();
            $this->app->instance(PaymentGateway::class, $paymentGateway);
            $paymentGateway->charge(2500, 'invalide-token');
        } catch (PaymentFailedException $e) {
            return;
        };
        $this->fail();
    }

    /** @test* */
    function running_a_hook_before_first_charge()
    {
        // Arrange
        $paymentGateway = new FakePaymentGateway();
        $timesCallBackRan = 0;
        $paymentGateway->beforeFirstCharge (function ($paymentGateway) use (&$timesCallBackRan){
             $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());
            $timesCallBackRan++;
            $this->assertEquals(2500, $paymentGateway->totalCharges());
        });
        $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());
        $this->assertEquals(1,$timesCallBackRan);
        $this->assertEquals(5000, $paymentGateway->totalCharges());
        //Act

        //Assert

    }
}