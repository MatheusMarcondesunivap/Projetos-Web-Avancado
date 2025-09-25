<?php
require_once 'api_2bim/src/DAO/loginDAO.php';
require_once 'api_2bim/src/http/Response.php';

class CadastroMiddleware{
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
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($stdLogin->controle->Nome)){
                (new Response(
                    success: false,
                    message: "Usuário inválido",
                    error:[
                        "code" => 'validation_error',
                        "message" => 'Não foi enviado o nome do usuário.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            else if (!isset($stdLogin->controle->Matricula)){
                (new Response(
                    success: false,
                    message: "Usuário inválido",
                    error:[
                        "code" => 'validation_error',
                        "message" => 'Não foi enviado a matrícula do usuário.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            else if (!isset($stdLogin->controle->Cargo)){
                (new Response(
                    success: false,
                    message: "Usuário inválido",
                    error:[
                        "code" => 'validation_error',
                        "message" => 'Não foi enviado o cargo do usuário.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            
            else if (!isset($stdLogin->controle->dataNascimento)){
                (new Response(
                    success: false,
                    message: "Usuário inválido",
                    error:[
                        "code" => 'validation_error',
                        "message" => 'Não foi enviado a data de nascimento de um usuário.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            } 
        } else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            $temCamposAtualizaveis = isset($stdLogin->controle->Nome) ||
                            isset($stdLogin->controle->Cargo) ||
                            isset($stdLogin->controle->Senha) ||
                            isset($stdLogin->controle->dataNascimento);

            if (!$temCamposAtualizaveis) {
                (new Response(
                    success: false,
                    message: "Requisição inválida",
                    error: [
                        "code" => 'validation_error',
                        "message" => 'Nenhum campo de atualização foi enviado (Nome, Email, Senha_hash ou Ativo).'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            } 
        }

        return $stdLogin;
    }

    public function isValidID($matricula): self
    {
        if(!isset($matricula))
        {
            (new Response(
            success: false,
            message: "A matrícula do usuário não foi informada.",
            error:[
                "code" => 'usuario_validation_error',
                "message" => 'A matrícula do usuário deve ser informada para a operação.'
            ],
            httpCode: 400
            ))->send();
            exit();
        }else if(!is_numeric($matricula) || ((int)$matricula) <= 0)
        {
            (new Response(
                success: false,
                message: "Matrícula inválida.",
                error:[
                    "code" => 'usuario_validation_error',
                    "message" => 'A matrícula do usuário deve ser um número positivo.'
                ],
                httpCode: 400
            ))->send();
            exit();
        }else{
            return $this; 
        } 
    }

    public function isValidNome($nome): self
    {
        if (!isset($nome)){
            (new Response(
                success: false,
                message: "Nome inválido",
                error: [
                    "code" => 'validation_error',
                    "message" => 'O nome do usuário deve ser informado.'
                ],
                httpCode: 400
            ))->send();
            exit();
        } else if (strlen($nome) < 3) {
            (new Response(
                success: false,
                message: "Nome inválido",
                error: [
                    "code" => 'validation_error',
                    "message" => 'O nome do usuário deve ter no mínimo 3 letras.'
                ],
                httpCode: 400
            ))->send();
            exit();
        }else{
            return $this;
        }
    }
    public function isValidCargo($cargo): self
    {
        if (!isset($cargo)){
            (new Response(
                success: false,
                message: "Cargo inválido",
                error: [
                    "code" => 'validation_error',
                    "message" => 'O cargo do usuário deve ser informado.'
                ],
                httpCode: 400
            ))->send();
            exit();
        }else if (strlen($cargo) < 5) {
            (new Response(
                success: false,
                message: "Cargo inválido",
                error: [
                    "code" => 'validation_error',
                    "message" => 'O cargo do usuário deve ter no mínimo 5 letras.'
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