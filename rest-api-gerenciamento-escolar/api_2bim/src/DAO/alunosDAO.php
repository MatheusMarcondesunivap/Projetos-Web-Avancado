<?php
require_once 'api_2bim/src/db/Database.php';
require_once 'api_2bim/src/models/Aluno.php';

    class alunosDAO{
        public function readAll(){
            $resultados = [];
            $query = 'SELECT 
                matricula,
                nome,
                id_turma
                FROM alunos ORDER BY matricula ASC';

            $statement =  Database::getConnection()->query(query: $query); // impedir sql injection
            $statement->execute();
            $resultados = $statement->fetchAll(mode: PDO::FETCH_ASSOC);

            return $resultados;
        }




        public function readById(int $idAluno): Aluno | array{
            $resultados = [];
            $query = 'SELECT 
                matricula,
                nome,
                id_turma
                FROM alunos
                WHERE matricula = :idAluno
                ORDER BY matricula ASC;';

            $statement =  Database::getConnection()->prepare(query: $query); // impedir sql injection
            $statement->execute(
                params:[':idAluno' => (int)$idAluno]
            );
            $resultados = $statement->fetchAll(mode: PDO::FETCH_ASSOC);
            return $resultados;
        }




        public function readByName(string $nomeAluno, int $id): bool {

            $query = 'SELECT 
                matricula,
                nome
                FROM alunos
                WHERE nome = :nomeAluno AND matricula != :id
                ORDER BY matricula ASC;';

            $statement =  Database::getConnection()->prepare(query: $query); // impedir sql injection
            $statement->execute(
                params:[':nomeAluno' => $nomeAluno, ':id' => $id]
            );
            $objStdAluno = $statement->fetch(mode: PDO::FETCH_OBJ);
            if(!$objStdAluno){
                return true;
            }
            return false;

        }




        public function create(Aluno $aluno): Aluno|false{
            $query = 'INSERT INTO 
                    alunos (matricula, nome, id_turma) 
                    VALUES (:matricula, :nome, :idTurma);';

            $statement =  Database::getConnection()->prepare(query: $query); // impedir sql injection
            $statement->execute(
                params:[
                    ':matricula' => $aluno->getIdAluno(),
                    ':nome' => $aluno->getNome(),
                    ':idTurma' => $aluno->getIdTurma()
                ]
            );
            $aluno->setIdTurma(idTurma: Database::getConnection()->lastInsertId());

            return $aluno;
        }




        public function update(Aluno $Aluno): Aluno|false{
            $query = 'UPDATE alunos
                     SET nome = :nomeAluno,
                        id_turma = :idTurma
                     WHERE matricula = :idAluno;';

            $statement =  Database::getConnection()->prepare(query: $query); // impedir sql injection
            $success = $statement->execute(
                params:[
                    ':idAluno' => $Aluno->getIdAluno(),
                    ':nomeAluno' => $Aluno->getNome(),
                    ':idTurma' => $Aluno->getIdTurma()
                ]
            );
            if ($success) {
                return $Aluno;
            }
            return false;
        }



            
        public function delete(int $idAluno): bool{
            $query = 'DELETE FROM alunos
                WHERE matricula = :idAluno;';

            $statement =  Database::getConnection()->prepare(query: $query); // impedir sql injection
            $statement->execute(
                params:[':idAluno' => $idAluno]
            );

            return $statement->rowCount() > 0;
        }
    }
?>