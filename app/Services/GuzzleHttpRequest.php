<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Support\Facades\Log;

class GuzzleHttpRequest
{
    public $client;
    public $response;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => config('services.p2p.baseUrl'),
        ]);
        $this->response = null;
    }

    public function get($url,$data = [])
    {
        try {
            $response = $this->client->request('GET', $url,$data);
            $this->response = $response;
            return json_decode($response->getBody()->getContents());
        } catch (ClientException $err) {
            $response = new \stdClass();
            $response->code = $err->getCode();
            $response->error = true;
            $response->mensaje = "";
            if($response->code === 404){
                $response->mensaje = "Recurso no encontrado.";
            }
            return $response;
        }
    }

    public function post($url, $form_params,$headers = [])
    {
        try {
            $response = $this->client->request('POST', $url, [
                'headers' => $headers,
                'form_params' => $form_params
            ]);

            $result = new \stdClass();
            $result->success = true;
            $result->response = json_decode($response->getBody()->getContents());

            $this->response = $result;

            return $result;
        } catch (ServerException $err) {
            preg_match("/\{(.*)\}/",$err->getMessage(),$message);
            $error = [
                "code" => $err->getCode(),
                "message" => $err->getMessage(),
                "request" => $err->getRequest(),
                "response" => $err->getResponse(),
                "trace" => $err->getTraceAsString(),
            ];
            Log::error(json_encode($error));

            $result = new \stdClass();
            $result->success = false;
            $result->response = $err;

            $this->response = $result;

            return $result;
        }
    }

    public function getResponse()
    {
        return $this->response;
    }

}
