<?php
require_once 'api_2bim/src/db/Database.php';
require_once 'api_2bim/src/models/Turma.php';

    class turmasDAO{
        public function readAll(){
            $resultados = [];
            $query = 'SELECT
                id_turma,
                ano_turma,
                letra,
                id_curso
                FROM turmas ORDER BY id_turma ASC, letra ASC';

            $statement =  Database::getConnection()->query(query: $query); // impedir sql injection
            $statement->execute();
            $I = 1;
            /* while ($stdLinha = $statement->fetch(mode: PDO::FETCH_OBJ)) {
                $curso = new Curso();
                $curso
                    ->setIdCurso(id_curso: $stdLinha->id_curso)
                    ->setAnoCurso(Ano_curso: $stdLinha->Ano_curso)
                    ->setCoordenador(coordenador: $stdLinha->coordenador);
                $resultados[] = $curso;
            }*/
            $resultados = $statement->fetchAll(mode: PDO::FETCH_ASSOC);

            return $resultados;
        }




        
        public function readById(int $idTurma): Turma | array{
            $resultados = [];
            $query = 'SELECT 
                id_turma,
                ano_turma,
                letra,
                id_curso
                FROM Turmas
                WHERE id_turma = :idTurma
                ORDER BY id_turma ASC;';

            $statement =  Database::getConnection()->prepare(query: $query); // impedir sql injection
            $statement->execute(
                params:[':idTurma' => (int)$idTurma]
            );
            $resultados = $statement->fetchAll(mode: PDO::FETCH_ASSOC);
            return $resultados;

        }





        public function readByLetraAndAno(string $letra, int $ano): Turma|null{
            $resultados = [];
            $query = 'SELECT 
                id_turma,
                ano_turma,
                letra,
                id_curso
                FROM turmas
                WHERE letra = :letra AND ano_turma = :ano
                ORDER BY id_turma ASC;';
            $statement =  Database::getConnection()->prepare(query: $query); // impedir sql injection
            $statement->execute(
                params:[':letra' => strtoupper($letra), ':ano' => $ano]
            );
            $objStdTurma = $statement->fetch(mode: PDO::FETCH_OBJ);
            if(!$objStdTurma){
                return null;
            }

            return (new Turma())
                ->setAnoTurma(anoTurma: $objStdTurma->ano_turma)
                ->setLetra(letra: $objStdTurma->letra)
                ->setIdCurso(idCurso: $objStdTurma->id_curso);
        }





        public function create(Turma $turma): Turma|false{
            $query = 'INSERT INTO 
                    turmas (ano_turma, letra, id_curso) 
                    VALUES (:anoTurma, :letra, :idCurso);';

            $statement =  Database::getConnection()->prepare(query: $query); // impedir sql injection
            $statement->execute(
                params:[
                    ':anoTurma' => $turma->getAnoTurma(),
                    ':letra' => $turma->getLetra(),
                    ':idCurso' => $turma->getidCurso()
                ]
            );
            $turma->setidCurso(idCurso: Database::getConnection()->lastInsertId());

            return $turma;
        }





        public function update(turma $turma): turma|false{
            $query = 'UPDATE turmas
                SET ano_turma = :anoTurma,
                    letra = :letra,
                    id_curso = :idCurso
                WHERE id_turma = :idTurma;';

            $statement =  Database::getConnection()->prepare(query: $query); // impedir sql injection
            $statement->execute(
                params:[
                    ':anoTurma' => $turma->getAnoTurma(),
                    ':letra' => $turma->getLetra(),
                    ':idCurso' => $turma->getidCurso(),
                    ':idTurma' => $turma->getIdTurma()
                ]
            );

            return $turma;
        }





        public function delete(int $idTurma): bool{
            $query = 'DELETE FROM turmas
                WHERE id_turma = :idTurma;';

            $statement =  Database::getConnection()->prepare(query: $query); // impedir sql injection
            $statement->execute(
                params:[':idTurma' => $idTurma]
            );

            return $statement->rowCount() > 0;
        }
    }
?>