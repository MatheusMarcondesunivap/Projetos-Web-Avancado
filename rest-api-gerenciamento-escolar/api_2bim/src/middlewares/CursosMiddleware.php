<?php
require_once 'api_2bim/src/http/Response.php';
    class CursosMiddleware 
    {
        public function stringJsonToStdClass($requestBody): stdClass{
            $stdCurso = json_decode(json: $requestBody);
            if (json_last_error() !== JSON_ERROR_NONE){
                (new Response(
                    success: false,
                    message: "Curso inválido",
                    error:[
                        "code" => 'validation_error',
                        "message" => 'Json inválido.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            else if (!isset($stdCurso->cursos)){
                (new Response(
                    success: false,
                    message: "Curso inválido",
                    error:[
                        "code" => 'validation_error',
                        "message" => 'Não foi enviado o objeto Cursos.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            else if (!isset($stdCurso->cursos->nomeCurso)){
                (new Response(
                    success: false,
                    message: "Curso inválido",
                    error:[
                        "code" => 'validation_error',
                        "message" => 'Não foi enviado o nome de um curso.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            else if (!isset($stdCurso->cursos->coordenador)){
                (new Response(
                    success: false,
                    message: "Curso inválido",
                    error:[
                        "code" => 'validation_error',
                        "message" => 'Não foi enviado o coordenador de um curso.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }   

            return $stdCurso;
        }

        public function IsValidNomeCurso($nomeCurso): self{
            if (!isset($nomeCurso)){
                (new Response(
                    success: false,
                    message: "Curso inválido",
                    error:[
                        "code" => 'validation_error',
                        "message" => 'Não foi enviado o nome de um curso.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            else if(strlen(string: $nomeCurso) < 7){
                (new Response(
                    success: false,
                    message: "Curso inválido",
                    error:[
                        "code" => 'validation_error',
                        "message" => 'O nome do curso deve ter no mínimo 7 letras.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }

            return $this;
        }


        public function hasNotCursoByName($nomeCurso): self{
            $CursoDAO = new CursosDAO();
            $Curso = $CursoDAO->readByName(nomeCurso: $nomeCurso);
            if(isset($Curso)){
                (new Response(
                    success: false,
                    message: "Curso já cadastrado",
                    error:[
                        "code" => 'validation_error',
                        "message" => 'Já existe um curso cadastrado com o nome informado.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
               return $this;
            }
            return $this;
        }
        public function IsValidID($idCurso): self
        {
            if(!isset($idCurso))
            {
               (new Response(
                success: false,
                message: "ID do Curso não foi informado.",
                error:[
                    "code" => 'Curso_validation_error',
                    "message" => 'O ID do Curso deve ser informado para a operação.'
                ],
                httpCode: 400
            ))->send();
            exit();
            }else if(!is_numeric($idCurso) || ((int)$idCurso) <= 0)
            {
                (new Response(
                    success: false,
                    message: "ID do Curso inválido.",
                    error:[
                        "code" => 'Curso_validation_error',
                        "message" => 'O ID do Curso deve ser um número positivo.'
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