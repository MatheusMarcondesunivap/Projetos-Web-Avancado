<?php
require_once 'api_2bim/src/DAO/alunosDAO.php';
require_once 'api_2bim/src/http/Response.php';
require_once 'api_2bim/src/DAO/cadastroDAO.php';

    class alunosControl{
        public function index(): never{
            $alunosDAO = new alunosDAO();
            $resposta = $alunosDAO->readAll();

            (new Response(
                success: true,
                message: 'alunos selecionados com sucesso.',
                data: ['alunos' => $resposta],
                httpCode: 200
            ))->send();

            exit();
        }
        public function show(int $idAluno): never
        {
            $alunosDAO = new alunosDAO();
            $resposta = $alunosDAO->readById(idAluno: $idAluno);

            (new Response(
                success: true,
                message: 'alunos selecionados com sucesso.',
                data: ['alunos' => $resposta],
                httpCode: 200
            ))->send();

            exit();
        }
        public function store(stdClass $stdAluno): never
        {
            $aluno = new Aluno();
            $aluno
                ->setidAluno($stdAluno->alunos->matricula)
                ->setNome(nome: $stdAluno->alunos->nome)
                ->setIdTurma(idTurma: $stdAluno->alunos->idTurma ?? null);

            $cadastroDAO = new CadastroDAO();
            if ($cadastroDAO->readById($aluno->getIdAluno()) == null || $cadastroDAO->readByName($aluno->getNome()) == null) {
                    (new Response(
                        success: false,
                        message: 'Cadastro não encontrado. Não é possível criar aluno.',
                        httpCode: 404
                    ))->send();
                    exit();
            }
            $respostaCargo = $cadastroDAO->readById($aluno->getIdAluno());
            if ($respostaCargo["cargo"] !== "aluno"){
                    (new Response(
                        success: false,
                        message: 'Cadastro não pertence ao cargo aluno. Não é possível criar aluno.',
                        httpCode: 404
                    ))->send();
                    exit();
            }
            $alunosDAO = new alunosDAO();
            $nome = $alunosDAO->create($aluno);
            (new Response(
                success: true,
                message: 'aluno criado com sucesso.',
                data: ['aluno' => $nome],
                httpCode: 200
            ))->send();
            exit();  
        }
        public function edit(stdClass $stdAluno): never
        {
            $Aluno = new Aluno();
            $Aluno
                ->setIdAluno(idAluno: $stdAluno->alunos->idAluno)
                ->setNome(nome: $stdAluno->alunos->nome)
                ->setIdTurma(idTurma: $stdAluno->alunos->idTurma);
            $AlunosDAO = new AlunosDAO();
            if($nome = $AlunosDAO->update($Aluno) == true){
                (new Response(
                    success: true,
                    message: 'Aluno atualizado com sucesso.',
                    data: ['Aluno' => $Aluno],
                    httpCode: 200
                ))->send();
            } else {
                (new Response(
                    success: false,
                    message: 'Aluno não atualizado.',
                    error: [
                        "code" => 'update_error',
                        "message" => 'Não foi possível atualizar o Aluno.'
                    ],
                    httpCode: 400
                ))->send();
              exit();  
            }
        }

        public function destroy(int $idAluno): never{

            $alunosDAO = new alunosDAO();
            if($alunosDAO->delete(idAluno: $idAluno)){
                (new Response(
                    success: true,
                    message: 'Aluno deletado com sucesso.',
                    httpCode: 204
                ))->send();
            } else {
                (new Response(
                    success: false,
                    message: 'Aluno não deletado.',
                    error: [
                        "code" => 'delete_error',
                        "message" => 'Não foi possível deletar o aluno.'
                    ],
                    httpCode: 400
                ))->send();
            }
            exit();
        }
    }
?>