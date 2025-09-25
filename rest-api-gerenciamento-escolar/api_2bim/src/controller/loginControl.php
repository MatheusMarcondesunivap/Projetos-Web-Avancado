<?php
require_once 'api_2bim/src/DAO/loginDAO.php';
require_once 'api_2bim/src/http/Response.php';
require_once 'api_2bim/src/utils/MeuTokenJWT.php';
require_once 'api_2bim/src/models/CadastroUsuario.php';

use Firebase\JWT\MeuTokenJWT;
class LoginControl{
   public function autenticar(stdClass $stdLogin): never
    {
    // Cria uma instância do DAO para acessar os dados no banco
        $loginDAO = new LoginDAO();
        $controle = new CadastroUsuario();
        $controle->setMatricula($stdLogin->controle->Matricula);
        $controle->setSenha($stdLogin->controle->Senha);
        // Obtém todos os cargos cadastrados
        $controleLogado = $loginDAO->verificarLogin($controle);
        if (empty($controleLogado)) {
            // Envia a resposta JSON com os dados encontrados
            (new Response(
            success: false,
            message: 'Usuário e senha inválidos',
            httpCode: 401
            ))->send();
        } else {
            $claims = new stdClass();
            $claims->name = $controleLogado->getNome();
            $claims->matricula = $controleLogado->getMatricula();
            $claims->cargo = $controleLogado->getCargo();

            $meuToken = new MeuTokenJWT();
            $token = $meuToken->gerarToken($claims);
            (new Response(
                success: true,
                message: 'Usuário e senha validados',
                data: [
                'token' => $token,
                'controle' => $controleLogado
                ],
                httpCode: 200
                ))->send();
        }
        exit();
    }
}
 
