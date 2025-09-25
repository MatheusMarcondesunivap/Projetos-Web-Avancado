<?php
require_once 'api_2bim/src/DAO/CadastroDAO.php';
require_once 'api_2bim/src/DAO/alunosDAO.php';
require_once 'api_2bim/src/http/Response.php';
require_once 'api_2bim/src/models/CadastroUsuario.php';

    class CadastroControl{
        public function index(): never{
            $cadastroDAO = new CadastroDAO();
            $resposta = $cadastroDAO->readAll();
            foreach ($resposta as &$item){
                if (password_verify(str_replace("-", "", $item["dataNascimento"]), $item["senha"])) {
                    $item["senha"] = "default_password";
                } else {
                    $item["senha"] = "senha_atualizada"; 
                }
            }
            (new Response(
                success: true,
                message: 'Cadastros selecionados com sucesso.',
                data: ['cadastros' => $resposta],
                httpCode: 200
            ))->send();

            exit();
        }
        public function show(int $matricula): never
        {
            $cadastroDAO = new CadastroDAO();
            $resposta = $cadastroDAO->readById($matricula);
            if (password_verify(str_replace("-", "", $resposta["dataNascimento"]), $resposta["senha"])) {
                $resposta["senha"] = "default_password";
            } else {
                $resposta["senha"] = "senha_atualizada";
            }
            if ($resposta === null) {
                (new Response(
                    success: false,
                    message: 'Cadastro não encontrado.',
                    httpCode: 404
                ))->send();
            } else {
                (new Response(
                    success: true,
                    message: 'Cadastro selecionado com sucesso.',
                    data: ['cadastros' => $resposta],
                    httpCode: 200
                ))->send();
            }
            
            exit();
        }

        public function edit(stdClass $stdCadastro): never
        {
            $cadastroDAO = new CadastroDAO();
            $atual = $cadastroDAO->readById($stdCadastro->controle->Matricula);

            $cadastro = new CadastroUsuario();
            $cadastro
                ->setMatricula($stdCadastro->controle->Matricula)
                ->setNome(isset($stdCadastro->controle->Nome) ? $stdCadastro->controle->Nome : $atual['nome'])
                ->setSenha(isset($stdCadastro->controle->Senha) ? $stdCadastro->controle->Senha : $atual['senha'])
                ->setSenhaHash(isset($stdCadastro->controle->Senha) ? password_hash($stdCadastro->controle->Senha, PASSWORD_DEFAULT) : $atual['senha'])
                ->setCargo(isset($stdCadastro->controle->Cargo) ? $stdCadastro->controle->Cargo : $atual['cargo']);
                
            if (!empty($stdCadastro->controle->dataNascimento)) {
                $data = new DateTime($stdCadastro->controle->dataNascimento);
                $cadastro->setDataNascimento($data);
            } else {
                $data = new DateTime($atual['dataNascimento']);
                $cadastro->setDataNascimento($data);
            }

            $atualizado = $cadastroDAO->update($cadastro);
            if ($atualizado !== false) {
                (new Response(
                    success: true,
                    message: 'Cadastro atualizado com sucesso.',
                    data: ['Cadastro' => $atualizado],
                    httpCode: 200
                ))->send();
            } else {
                (new Response(
                    success: false,
                    message: 'Cadastro não atualizado.',
                    error: [
                        "code" => 'update_error',
                        "message" => 'Não foi possível atualizar o Cadastro.'
                    ],
                    httpCode: 400
                ))->send();
            }
            exit();
        }


        public function store(stdClass $stdCadastro): never
        {
            $senha = str_replace("-","",$stdCadastro->controle->dataNascimento);
            $alunosDAO = new AlunosDAO();
            $data = new DateTime($stdCadastro->controle->dataNascimento);
            $cadastro = new CadastroUsuario();
            $cadastro
                ->setMatricula($stdCadastro->controle->Matricula)
                ->setNome($stdCadastro->controle->Nome)
                ->setCargo($stdCadastro->controle->Cargo)
                ->setDataNascimento(dataNascimento: $data)
                ->setSenha($senha)
                ->setSenhaHash(senhaHash: password_hash($senha, PASSWORD_DEFAULT)); // Armazena a senha como hash
            
            
            $cadastroDAO = new CadastroDAO();
            $nomeCadastro = $cadastroDAO->create($cadastro);
            
            (new Response(
                success: true,
                message: 'Cadastro criado com sucesso.',
                data: ['cadastro' => $nomeCadastro],
                httpCode: 200
            ))->send();
            exit();  
        }

        public function delete(int $matricula): never
        {
            $cadastroDAO = new CadastroDAO();
            $success = $cadastroDAO->delete($matricula);

            if ($success) {
                (new Response(
                    success: true,
                    message: 'Cadastro deletado com sucesso.',
                    data: null,
                    httpCode: 200
                ))->send();
            } else {
                (new Response(
                    success: false,
                    message: 'falha ao deletar cadastro.',
                    data: null,
                    httpCode: 404
                ))->send();
            }

            exit();
        }
    }