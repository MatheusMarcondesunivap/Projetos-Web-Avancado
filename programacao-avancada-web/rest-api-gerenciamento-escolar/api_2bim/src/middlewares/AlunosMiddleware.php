<?php
require_once 'api_2bim/src/http/Response.php';
require_once 'api_2bim/src/DAO/alunosDAO.php';
    class AlunosMiddleware 
    {
        public function stringJsonToStdClass($requestBody): stdClass{
            $stdAluno = json_decode(json: $requestBody);
            if (json_last_error() !== JSON_ERROR_NONE){
                (new Response(
                    success: false,
                    message: "Aluno inválido",
                    error:[
                        "code" => 'validation_error',
                        "message" => 'Json inválido.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            else if (!isset($stdAluno->alunos)){
                (new Response(
                    success: false,
                    message: "Aluno inválido",
                    error:[
                        "code" => 'validation_error',
                        "message" => 'Não foi enviado o objeto alunos.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            else if (!isset($stdAluno->alunos->matricula)){
                (new Response(
                    success: false,
                    message: "Aluno inválido",
                    error:[
                        "code" => 'validation_error',
                        "message" => 'Não foi enviado a matrícula de um aluno.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            else if (!isset($stdAluno->alunos->nome)){
                (new Response(
                    success: false,
                    message: "Aluno inválido",
                    error:[
                        "code" => 'validation_error',
                        "message" => 'Não foi enviado o nome de um aluno.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }

            return $stdAluno;
        }

        public function IsValidNomeAluno($nomeAluno): self{
            if (!isset($nomeAluno)){
                (new Response(
                    success: false,
                    message: "Aluno inválido",
                    error:[
                        "code" => 'validation_error',
                        "message" => 'Não foi enviado a matrícula de um aluno.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            else if(strlen(string: $nomeAluno) < 3){
                (new Response(
                    success: false,
                    message: "Aluno inválido",
                    error:[
                        "code" => 'validation_error',
                        "message" => 'O nome do aluno deve ter no mínimo 3 letras.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }

            return $this;
        }
        public function hasNotAlunoByName($nomeAluno, $id): self{
            $alunoDAO = new AlunosDAO();
            $aluno = $alunoDAO->readByName($nomeAluno, $id);
            if(!$aluno){
                (new Response(
                    success: false,
                    message: "Aluno já cadastrado",
                    error:[
                        "code" => 'validation_error',
                        "message" => 'Já existe um aluno cadastrado com o nome informado.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
               return $this;
            }
            return $this;
        }
        public function IsValidID($idAluno): self
        {
            if(!isset($idAluno))
            {
               (new Response(
                success: false,
                message: "ID do aluno não foi informado.",
                error:[
                    "code" => 'aluno_validation_error',
                    "message" => 'O ID do aluno deve ser informado para a operação.'
                ],
                httpCode: 400
            ))->send();
            exit();
            }else if(!is_numeric($idAluno) || ((int)$idAluno) <= 0)
            {
                (new Response(
                    success: false,
                    message: "ID do aluno inválido.",
                    error:[
                        "code" => 'aluno_validation_error',
                        "message" => 'O ID do aluno deve ser um número positivo.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }else{
               return $this; 
            } 
        }
    }
?>