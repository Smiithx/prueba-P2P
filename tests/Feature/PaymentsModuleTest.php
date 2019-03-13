<?php

namespace Tests\Feature;

use App\Models\Payment;
use Tests\TestCase;

class PaymentsModuleTest extends TestCase
{
    /**
     * Test it loads the payments list page
     *
     * @test
     */
    public function itLoadsThePaymentsListPage()
    {
        $response = $this->get('/payments');

        $response->assertStatus(200)->assertSee("Pagos");
    }

    /**
     * Test it load the create payments page
     *
     * @test
     */
    public function itLoadTheCreatePaymentsPage()
    {
        $response = $this->get('/payments/create');

        $response->assertStatus(200)->assertSee("Datos de compra");
    }

    /**
     * Test store payments
     *
     * @return void
     */
    public function testStorePayment()
    {
        $response = $this->withHeaders([
            'X-CSRF-TOKEN' => csrf_token(),
        ])->json('POST', '/payments', [
            'description' => 'Payment test',
            'currency' => 'COP',
            'amount' => 10000,
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                "success" => true,
                "message" => "El pago ha sido creado con éxito!",
            ]);
    }

    /**
     * Test response payment P2P
     *
     * @return void
     */
    public function testResponsePaymentP2P()
    {
        $payment = Payment::whereNull("status")->first();

        if ($payment) {
            $response = $this->get("/payments/response/$payment->reference");
            $response->assertRedirect("/payments");
            $this->assertDatabaseHas('payments', [
                'id' => $payment->id,
                "status" => "PENDING"
            ]);
        } else {
            $this->assertTrue(true);
        }
    }

}
