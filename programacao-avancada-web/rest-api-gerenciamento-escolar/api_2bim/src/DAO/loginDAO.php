<?php
require_once 'api_2bim/src/db/Database.php';
require_once 'api_2bim/src/models/CadastroUsuario.php';
class LoginDAO{
    public function verificarLogin(CadastroUsuario $controle): CadastroUsuario| null
    {
        $query = ' SELECT matricula, nome, cargo, senha FROM controle
                    WHERE
                    matricula = :matricula 
                    ORDER BY matricula ASC ';
        // Prepara a instrução SQL, protegendo contra SQL Injection
        $statement = Database::getConnection()->prepare(query: $query);
        $statement->bindValue(
            param: ':matricula',
            value: $controle->getMatricula(),
            type: PDO::PARAM_STR
        );
        // Busca a única linha esperada da consulta como um objeto genérico (stdClass)
        $statement->execute();

        $linha = $statement->fetch(mode: PDO::FETCH_OBJ);
        if (!$linha) {
            return null; // Retorna array vazio caso não encontre nenhum funcionário com esse idcontrole
        }
        if (!password_verify($controle->getSenha(), $linha->senha)) {
            return null;
        }
        // Preenche os dados básicos do funcionário no objeto controle
        $controle
                ->setMatricula($linha->matricula) // ID do funcionário
                ->setNome($linha->nome) // Nome do funcionário
                ->setCargo($linha->cargo)
                ->setSenhaHash($linha->senha);

        return $controle;
}
}