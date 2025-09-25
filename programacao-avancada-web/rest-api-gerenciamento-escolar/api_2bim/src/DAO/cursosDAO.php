<?php
require_once 'api_2bim/src/db/Database.php';
require_once 'api_2bim/src/models/Curso.php';

    class cursosDAO{
        public function readAll(){
            $resultados = [];
            $query = 'SELECT
                id_curso,
                nome_curso,
                coordenador
                FROM cursos ORDER BY nome_curso ASC';

            $statement =  Database::getConnection()->query(query: $query); // impedir sql injection
            $statement->execute();
            $I = 1;
            /* while ($stdLinha = $statement->fetch(mode: PDO::FETCH_OBJ)) {
                $curso = new Curso();
                $curso
                    ->setIdCurso(id_curso: $stdLinha->id_curso)
                    ->setNomeCurso(nome_curso: $stdLinha->nome_curso)
                    ->setCoordenador(coordenador: $stdLinha->coordenador);
                $resultados[] = $curso;
            }*/
            $resultados = $statement->fetchAll(mode: PDO::FETCH_ASSOC);

            return $resultados;
        }




        public function readById(int $idCurso): Curso | array{
            $resultados = [];
            $query = 'SELECT 
                id_curso,
                nome_curso,
                coordenador
                FROM Cursos
                WHERE id_curso = :idCurso
                ORDER BY id_curso ASC;';

            $statement =  Database::getConnection()->prepare(query: $query); // impedir sql injection
            $statement->execute(
                params:[':idCurso' => (int)$idCurso]
            );
            $resultados = $statement->fetchAll(mode: PDO::FETCH_ASSOC);
            return $resultados;
        }




        public function readByName(string $nomeCurso): Curso|null{
            $resultados = [];
            $query = 'SELECT 
                id_curso,
                nome_curso,
                coordenador
                FROM Cursos
                WHERE nome_curso = :nomeCurso
                ORDER BY id_curso ASC;';

            $statement =  Database::getConnection()->prepare(query: $query); // impedir sql injection
            $statement->execute(
                params:[':nomeCurso' => $nomeCurso]
            );
            $objStdCurso = $statement->fetch(mode: PDO::FETCH_OBJ);
            if(!$objStdCurso){
                return null;
            }

            return (new Curso())
                ->setNomeCurso(nome_curso: $objStdCurso->nome_curso)
                ->setCoordenador(coordenador: $objStdCurso->coordenador);

        }




        public function create(Curso $Curso): Curso|false{
            $query = 'INSERT INTO Cursos
                (nome_curso, coordenador)
                VALUES
                (:nomeCurso, :Coordenador);';

            $statement =  Database::getConnection()->prepare(query: $query); // impedir sql injection
            $statement->execute(
                params:[
                    ':nomeCurso' => $Curso->getNomeCurso(),
                    ':Coordenador' => $Curso->getCoordenador()
                ]
            );
            $Curso->setIdCurso(Database::getConnection()->lastInsertId());

            return $Curso;
        }




        public function update(Curso $Curso): Curso|false{
            $query = 'UPDATE cursos
                SET nome_curso = :nomeCurso, coordenador = :Coordenador
                WHERE id_curso = :idCurso;';

            $statement =  Database::getConnection()->prepare(query: $query); // impedir sql injection
            $success = $statement->execute(
                params:[
                    ':idCurso' => $Curso->getIdCurso(),
                    ':nomeCurso' => $Curso->getNomeCurso(),
                    ':Coordenador' => $Curso->getCoordenador()
                ]
            );
            if ($success) {
                return $Curso;
            }
                return false;
            }




        public function delete(int $idCurso): bool{
            $query = 'DELETE FROM cursos
                WHERE id_curso = :idCurso;';

            $statement =  Database::getConnection()->prepare(query: $query); // impedir sql injection
            $statement->execute(
                params:[':idCurso' => $idCurso]
            );

            return $statement->rowCount() > 0;
        }
    }
?>