<?php
require_once 'api_2bim/src/DAO/cursosDAO.php';
require_once "api_2bim/src/DAO/cadastroDAO.php";
require_once 'api_2bim/src/http/Response.php';

    class cursosControl{
        public function index(): never{
            $cursosDAO = new cursosDAO();
            $resposta = $cursosDAO->readAll();

            (new Response(
                success: true,
                message: 'Cursos selecionados com sucesso.',
                data: ['Cursos' => $resposta],
                httpCode: 200
            ))->send();

            exit();
        }
        public function show(int $idCurso): never
        {
            $CursosDAO = new CursosDAO();
            $resposta = $CursosDAO->readById(idCurso: $idCurso);

            (new Response(
                success: true,
                message: 'Cursos selecionados com sucesso.',
                data: ['Cursos' => $resposta],
                httpCode: 200
            ))->send();

            exit();
        }
        public function store(stdClass $stdCurso): never
        {
            $Curso = new Curso();
            $Curso
                ->setNomeCurso(nome_curso: $stdCurso->cursos->nomeCurso)
                ->setCoordenador(coordenador: $stdCurso->cursos->coordenador);
            
            $cadastroDAO = new CadastroDAO();
            if (empty($cadastroDAO->readByName($stdCurso->cursos->coordenador))){
                (new Response(
                    success: false,
                    message: 'Coordenador não encontrado. Não é possível criar curso.',
                    error: [
                        "code" => 'validation_error',
                        "message" => 'O coordenador informado não está cadastrado no sistema.'
                    ],
                    httpCode: 400
                ))->send();
            }
            $cursosDAO = new cursosDAO();
            $nomeCurso = $cursosDAO->create($Curso);
            (new Response(
                success: true,
                message: 'Curso criado com sucesso.',
                data: ['Curso' => $nomeCurso],
                httpCode: 200
            ))->send();
            exit();  
        }
        public function edit(stdClass $stdCurso): never
        {
            $Curso = new Curso();
            $Curso
                ->setIdCurso(id_curso: $stdCurso->cursos->idCurso)
                ->setNomeCurso(nome_curso: $stdCurso->cursos->nomeCurso)
                ->setCoordenador(coordenador: $stdCurso->cursos->coordenador);
            $CursosDAO = new cursosDAO();
            if($nomeCurso = $CursosDAO->update($Curso) == true){
                (new Response(
                    success: true,
                    message: 'Curso atualizado com sucesso.',
                    data: ['Curso' => $Curso],
                    httpCode: 200
                ))->send();
            } else {
                (new Response(
                    success: false,
                    message: 'Curso não atualizado.',
                    error: [
                        "code" => 'update_error',
                        "message" => 'Não foi possível atualizar o curso.'
                    ],
                    httpCode: 400
                ))->send();
              exit();  
            }
        }
        public function destroy(int $idCurso): never{

            $cursosDAO = new cursosDAO();
            if($cursosDAO->delete(idCurso: $idCurso)){
                (new Response(
                    success: true,
                    message: 'Curso deletado com sucesso.',
                    httpCode: 204
                ))->send();
            } else {
                (new Response(
                    success: false,
                    message: 'Curso não deletado.',
                    error: [
                        "code" => 'delete_error',
                        "message" => 'Não foi possível deletar o curso.'
                    ],
                    httpCode: 400
                ))->send();
            }
            exit();
        }
    }
?>