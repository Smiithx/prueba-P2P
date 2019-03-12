<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function getAuth(){
        $auth = new \stdClass();
        $seed = date('c');
        $nonce = $this->getNonce();
        $secretKey= config("services.p2p.auth.secretKey");

        $auth->login = config("services.p2p.auth.identifier");
        $auth->seed = $seed;
        $auth->nonce = base64_encode($nonce);
        $auth->tranKey = base64_encode(sha1($nonce.$seed.$secretKey,true));

        return $auth;
    }

    /**
     * Valor aleatorio para cada solicitud codificado en Base64.
     *
     * @return string The encoded data, as a string.
     */
    protected function getNonce(){
        if (function_exists('random_bytes')) {
            $nonce = bin2hex(random_bytes(16));
        } elseif (function_exists('openssl_random_pseudo_bytes')) {
            $nonce = bin2hex(openssl_random_pseudo_bytes(16));
        } else {
            $nonce = mt_rand();
        }

        return $nonce;
    }
}
