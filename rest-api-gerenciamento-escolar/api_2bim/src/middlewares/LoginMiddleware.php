<?php
require_once 'api_2bim/src/http/Response.php';
require_once 'api_2bim/src/DAO/LoginDAO.php';

    class LoginMiddleware 
    {
        public function stringJsonToStdClass($requestBody): stdClass{
            $stdLogin = json_decode(json: $requestBody);
            if (json_last_error() !== JSON_ERROR_NONE){
                (new Response(
                    success: false,
                    message: "Login inválido",
                    error:[
                        "code" => 'validation_error',
                        "message" => 'Json inválido.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            } else if (!isset($stdLogin->controle->Matricula)){
                (new Response(
                    success: false,
                    message: "Usuário inválido",
                    error:[
                        "code" => 'validation_error',
                        "message" => 'Não foi enviada a matrícula do usuário.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            else if (!isset($stdLogin->controle->Senha)){
                (new Response(
                    success: false,
                    message: "Usuário inválido",
                    error:[
                        "code" => 'validation_error',
                        "message" => 'Não foi enviada a senha do usuário.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            return $stdLogin;
        }

        public function isValidMatricula($matricula): self
        {
            if (!isset($matricula)){
                (new Response(
                    success: false,
                    message: "Matricula inválido",
                    error: [
                        "code" => 'validation_error',
                        "message" => 'A matricula do usuário deve ser informado.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }else if (!is_numeric($matricula) || $matricula <= 0) {
                (new Response(
                    success: false,
                    message: "Matricula inválido",
                    error: [
                        "code" => 'validation_error',
                        "message" => 'A matricula do usuário deve ser um número maior que zero.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }else {
                return $this;
            }
        }
        public function isValidSenha($senha): self
        {
            if (!isset($senha)){
                (new Response(
                    success: false,
                    message: "Senha inválida",
                    error: [
                        "code" => 'validation_error',
                        "message" => 'A senha do usuário deve ser informada.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            } else if (strlen($senha) < 6) {
                (new Response(
                    success: false,
                    message: "Senha inválida",
                    error: [
                        "code" => 'validation_error',
                        "message" => 'A senha do usuário deve conter no mínimo 6 caracteres.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            } else{
                return $this;
            }
        }
    }
?>