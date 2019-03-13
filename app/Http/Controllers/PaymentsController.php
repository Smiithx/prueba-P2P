<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Transaction;
use App\Services\GuzzleHttpRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PaymentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $payments = Payment::with("transactions")->orderBy("created_at", "desc")->get();
            return response()->json($payments);
        } else {
            return view("payments.index");
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("payments.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            "description" => "required",
            "currency" => "required",
            "amount" => "required|min:0",
        ]);

        $auth = $this->getAuth();
        $description = $request->description;
        $currency = $request->currency;
        $amount = $request->amount;

        // create payment
        $payment = new Payment();
        $now = Carbon::now(config('app.timezone'));
        $date = $now->format('Ymdhis');
        $rand = rand(1, 10000);
        $payment->reference = "{$date}{$rand}";
        $payment->ip = $request->ip();
        $payment->user_agent = $request->userAgent();
        $payment->expiry_at = $now->addMinutes(5);
        $payment->description = $description;
        $payment->currency = $currency;
        $payment->amount = $amount;

        // create payment request
        $payment_request = new \stdClass();
        $payment_request->reference = $payment->reference;
        $payment_request->description = $payment->description;
        $amount_request = new \stdClass();
        $amount_request->currency = $payment->currency;
        $amount_request->total = $payment->amount;
        $payment_request->amount = $amount_request;

        // preparing data
        $data = new \stdClass();
        $data->auth = $auth;
        $data->payment = $payment_request;
        $data->expiration = $now->format("c");
        $data->ipAddress = $payment->ip;
        $data->userAgent = $payment->user_agent;
        $data->returnUrl = url("/payments/response/$payment->reference");

        // send data
        $client = new GuzzleHttpRequest();

        $result = $client->post('api/session', $data);

        if ($result->success) {

            $response = $result->response;

            if ($response->status->status === "OK") {
                // store payment
                $payment->process_url = $response->processUrl;
                $payment->request_id = $response->requestId;
                $payment->save();

                return response()->json([
                    "success" => true,
                    "message" => "El pago ha sido creado con éxito!",
                    "process_url" => $payment->process_url
                ]);
            } else {
                return response()->json([
                    "success" => false,
                    "message" => $response->status->message,
                ]);
            }

        } else {

            return response()->json([
                "success" => false,
                "message" => $result->response
            ]);
        }
    }

    public function response($reference)
    {

        $auth = $this->getAuth();

        $payment = Payment::with("transactions")->where("reference", $reference)->firstOrFail();

        // send data
        $client = new GuzzleHttpRequest();

        $data = new \stdClass();
        $data->auth = $auth;

        $result = $client->post("api/session/$payment->request_id", $data);

        if ($result->success) {

            $response = $result->response;
            $transactions_db = $payment->transactions->keyBy("receipt");

            // update payment
            $solved_in = Carbon::parse($response->status->date);
            $payment->status = $response->status->status;
            $payment->solved_in = $solved_in;
            $payment->save();
            //dd($response);

            // store transactions
            $transactions = $response->payment;

            if ($transactions) {
                foreach ($transactions as $transaction) {
                    if (!$transactions_db->has($transaction->receipt)) {
                        $solved_in = Carbon::parse($transaction->status->date);
                        $transaction_db = new Transaction();
                        $transaction_db->receipt = $transaction->receipt;
                        $transaction_db->status = $transaction->status->status;
                        $transaction_db->amount = $transaction->amount->from->total;
                        $transaction_db->currency = $transaction->amount->from->currency;
                        if(property_exists($transaction,"discount")){
                            $transaction_db->discount = $transaction->discount->amount;
                        }else{
                            $transaction_db->discount = 0;
                        }
                        $transaction_db->bank = $transaction->issuerName;
                        $transaction_db->solved_in = $solved_in;
                        $transaction_db->payment_id = $payment->id;
                        $transaction_db->authorization = $transaction->authorization;
                        $transaction_db->save();
                    }
                }
            }

            switch ($response->status->status) {
                case "APPROVED":
                    flash($response->status->message . " <b>$reference</b>")->success();
                    break;
                case "REJECTED":
                    flash($response->status->message . " <b>$reference</b>")->error();
                    break;
                case "PENDING":
                    flash($response->status->message . " <b>$reference</b>")->warning();
                    break;
                default:
                    return response()->json([
                        "success" => false,
                        "message" => $response->status->message,
                        "response" => $response
                    ]);
            }

            return redirect("/payments");

        } else {

            flash($result->response->getMessage())->error();
            return redirect("/payments");
        }

    }
}
