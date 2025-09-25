<?php
require_once 'api_2bim/src/DAO/turmasDAO.php';
require_once 'api_2bim/src/http/Response.php';

    class turmasControl{
        public function index(): never{
            $turmasDAO = new turmasDAO();
            $resposta = $turmasDAO->readAll();

            (new Response(
                success: true,
                message: 'Turmas selecionadas com sucesso.',
                data: ['Turmas' => $resposta],
                httpCode: 200
            ))->send();

            exit();
        }
        public function show(int $idTurma): never
        {
            $TurmasDAO = new TurmasDAO();
            $resposta = $TurmasDAO->readById(idTurma: $idTurma);

            (new Response(
                success: true,
                message: 'Turmas selecionadas com sucesso.',
                data: ['Turmas' => $resposta],
                httpCode: 200
            ))->send();

            exit();
        }
          public function store(stdClass $stdTurma): never
        {
            $turma = new Turma();
            $turma
                ->setAnoTurma(anoTurma: $stdTurma->turmas->anoTurma)
                ->setLetra(letra: strtoupper($stdTurma->turmas->letra))
                ->setIdCurso(idCurso: $stdTurma->turmas->idCurso);
            $turmasDAO = new turmasDAO();
            $turmaCriada = $turmasDAO->create($turma);
            (new Response(
                success: true,
                message: 'Turma criada com sucesso.',
                data: ['Turma' => $turmaCriada],
                httpCode: 200
            ))->send();
            exit();  
        }
        public function edit(stdClass $stdTurma): never
        {
            $turma = new Turma();
            $turma
                ->setIdTurma(idTurma: $stdTurma->turmas->idTurma)
                ->setAnoTurma(anoTurma: $stdTurma->turmas->anoTurma)
                ->setLetra(strtoupper($stdTurma->turmas->letra))
                ->setIdCurso(idCurso: $stdTurma->turmas->idCurso);
            $turmasDAO = new turmasDAO();
            if($turmaAtualizada = $turmasDAO->update($turma) == true){
                (new Response(
                    success: true,
                    message: 'Turma atualizada com sucesso.',
                    data: ['Turma' => $turma],
                    httpCode: 200
                ))->send();
            } else {
                (new Response(
                    success: false,
                    message: 'Erro ao atualizar turma.',
                    data: [],
                    httpCode: 400
                ))->send();
            }
            exit();
        }
        public function destroy(int $idTurma): never
        {
            $turmasDAO = new turmasDAO();
            if ($turmasDAO->delete($idTurma)) {
                (new Response(
                    success: true,
                    message: 'Turma deletada com sucesso.',
                    httpCode: 204
                ))->send();
            } else {
                (new Response(
                    success: false,
                    message: 'Turma não deletada.',
                    error: [
                        "code" => 'delete_error',
                        "message" => 'Não foi possível deletar a turma.'
                    ],
                    httpCode: 400
                ))->send();
            }
            exit();
        }
    }
?>