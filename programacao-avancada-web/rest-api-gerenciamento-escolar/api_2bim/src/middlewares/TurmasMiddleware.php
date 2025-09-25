<?php
require_once 'api_2bim/src/http/Response.php';
require_once 'api_2bim/src/DAO/turmasDAO.php';
    class TurmasMiddleware 
    {
        public function stringJsonToStdClass($requestBody): stdClass{
            $stdTurma = json_decode(json: $requestBody);
            if (json_last_error() !== JSON_ERROR_NONE){
                (new Response(
                    success: false,
                    message: "Turma inválida",
                    error:[
                        "code" => 'validation_error',
                        "message" => 'Json inválido.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            else if (!isset($stdTurma->turmas)){
                (new Response(
                    success: false,
                    message: "Turma inválida",
                    error:[
                        "code" => 'validation_error',
                        "message" => 'Não foi enviado o objeto Turmas.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            else if (!isset($stdTurma->turmas->anoTurma)){
                (new Response(
                    success: false,
                    message: "Turma inválida",
                    error:[
                        "code" => 'validation_error',
                        "message" => 'Não foi enviado o ano de uma Turma.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            else if (!isset($stdTurma->turmas->letra)){
                (new Response(
                    success: false,
                    message: "Turma inválida",
                    error:[
                        "code" => 'validation_error',
                        "message" => 'Não foi enviado a letra de uma Turma.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            else if (!isset($stdTurma->turmas->idCurso)){
                (new Response(
                    success: false,
                    message: "Turma inválida",
                    error:[
                        "code" => 'validation_error',
                        "message" => 'Não foi enviado o ID do curso de uma Turma.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }    

            return $stdTurma;
        }
        public function isValidLetra($letra): self
        {
            if(!isset($letra))
            {
                (new Response(
                    success: false,
                    message: "Letra da Turma não foi informada.",
                    error:[
                        "code" => 'Turma_validation_error',
                        "message" => 'A letra da Turma deve ser informada para a operação.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }else if(!is_string($letra) || strlen($letra) > 1)
            {
                (new Response(
                    success: false,
                    message: "Letra da Turma inválida.",
                    error:[
                        "code" => 'Turma_validation_error',
                        "message" => 'A letra da Turma deve ser uma string de um único caractere.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
               return $this; 
        }




        public function isValidAno($ano): self
        {
            if(!isset($ano))
            {
                (new Response(
                    success: false,
                    message: "Ano da Turma não foi informado.",
                    error:[
                        "code" => 'Turma_validation_error',
                        "message" => 'O ano da Turma deve ser informado para a operação.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }else if(!is_numeric($ano) || /*!is_int($ano) ||*/ $ano >= 10 || $ano <= 0)
            {
                (new Response(
                    success: false,
                    message: "Ano da Turma inválido.",
                    error:[
                        "code" => 'Turma_validation_error',
                        "message" => 'O ano da Turma deve ser um número inteiro MENOR que 10 e MAIOR que 0'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            return $this; 
        }
        public function hasNotTurmaByLetraAndAno($letra, $ano): self
        {
            $turmasDAO = new TurmasDAO();
            $turmaLetra = $turmasDAO->readByLetraAndAno(letra: $letra, ano: $ano);
            if((isset($turmaLetra))&&(isset($turmaAno))){
                (new Response(
                    success: false,
                    message: "Turma já cadastrada",
                    error:[
                        "code" => 'Turma_validation_error',
                        "message" => 'Já existe uma turma cadastrada com a letra e o ano informado.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            return $this;
        }
        public function IsValidID($idTurma): self
        {
            if(!isset($idTurma))
            {
               (new Response(
                success: false,
                message: "ID da Turma não foi informado.",
                error:[
                    "code" => 'Turma_validation_error',
                    "message" => 'O ID da Turma deve ser informado para a operação.'
                ],
                httpCode: 400
            ))->send();
            exit();
            }else if(!is_numeric($idTurma) || ((int)$idTurma) <= 0)
            {
                (new Response(
                    success: false,
                    message: "ID da Turma inválido.",
                    error:[
                        "code" => 'Turma_validation_error',
                        "message" => 'O ID da Turma deve ser um número positivo.'
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