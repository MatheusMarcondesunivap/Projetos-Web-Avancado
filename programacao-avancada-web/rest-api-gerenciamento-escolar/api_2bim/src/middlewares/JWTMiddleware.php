<?php
use Firebase\JWT\MeuTokenJWT;

require_once 'api_2bim/src/utils/MeuTokenJWT.php';
class JWTMiddleware{
    function getAuthorizationHeader()
    {
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            // Para Nginx ou servidores que passam como HTTP_*
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Faz o header ser insensível a maiúsculas/minúsculas
            $requestHeaders = array_change_key_case($requestHeaders, CASE_LOWER);
            if (isset($requestHeaders['authorization'])) {
                $headers = trim($requestHeaders['authorization']);
            }
        }
        return $headers;
    }


    public function isValidToken(): stdClass
    {
        $token = $this->getAuthorizationHeader();
        //verifica se existe algum token enviado
        if (!isset($token)) {
            (new Response(
                success: false,
                message: 'Token Inválido',
                error: [
                    'codigoError' => 'validation_error',
                    'message' => 'Não foi fornecido um token',
                ],
                httpCode: 401
            ))->send();
            exit();
        }
        $Jwt = new MeuTokenJWT();
        //verifica se o token é valido
        if ($Jwt->validateToken(stringToken: $token) == true) {
            return $Jwt->getPayload(); //retorna o payload publico e privado
        } else {
            (new Response(
                success: false,
                message: 'Token Inválido',
                error: [
                    'codigoError' => 'validation_error',
                    'message' => 'O token fornecido não é válido',
                ],
                httpCode: 401
            ))->send();
            exit();
        }
    }
}