<?php
require_once 'api_2bim/src/db/Database.php';
require_once 'api_2bim/src/models/CadastroUsuario.php';
require_once "api_2bim/src/utils/Logger.php";

    class CadastroDAO{
       public function readAll(){
            $resultados = [];
            $query = 'SELECT 
                matricula,
                nome, 
                cargo, 
                dataNascimento,
                senha
                FROM controle ORDER BY matricula ASC';

            $statement =  Database::getConnection()->query(query: $query); // impedir sql injection
            $statement->execute();

            $resultados = $statement->fetchAll(mode: PDO::FETCH_ASSOC);
            return $resultados;
        }
        public function readById(int $matricula): array|null
        {
            $resultados = [];
            $query = 'SELECT 
                    matricula,
                    nome, 
                    cargo, 
                    dataNascimento,
                    senha
                    FROM controle
                    WHERE matricula = :matricula;';

            $statement = Database::getConnection()->prepare(query: $query);
            $statement->execute([':matricula' => $matricula]);

            $resultados = $statement->fetch(mode: PDO::FETCH_ASSOC);

            if (!$resultados) {
                return null;
            }

            return $resultados;
        }

        public function readByName(string $name): array {
            $query = 'SELECT 
                    matricula,
                    nome, 
                    cargo, 
                    dataNascimento,
                    senha
                    FROM controle
                    WHERE nome = :nome;';

            $statement =  Database::getConnection()->prepare(query: $query); // impedir sql injection
            $statement->execute([':nome' => $name]);
            $resultados = $statement->fetchAll(mode: PDO::FETCH_ASSOC);
            return $resultados;
        }

        public function create(CadastroUsuario $usuario): CadastroUsuario|false{

            $query = 'INSERT INTO 
                    controle (
                        matricula,
                        nome, 
                        cargo,
                        dataNascimento, 
                        senha
                    ) 
                    VALUES (
                        :matricula,
                        :nome, 
                        :cargo,
                        :dataNascimento,
                        :senha
                    );';

            $statement =  Database::getConnection()->prepare(query: $query); // impedir sql injection
            $success = $statement->execute([
                ':matricula' => $usuario->getMatricula(),
                ':nome' => $usuario->getNome(),
                ':cargo' => $usuario->getCargo(),
                ':dataNascimento' => $usuario->getDataNascimento()->format('Y-m-d'),
                ':senha' => $usuario->getSenhaHash()
            ]);

            if (!$success) {
                return false;
            }
            $usuario->setMatricula((int) Database::getConnection()->lastInsertId());

            return $usuario;
        }

        public function delete(int $matricula): bool
        {
            $query = 'DELETE FROM controle WHERE matricula = :matricula';
            $statement = Database::getConnection()->prepare(query: $query);
            $statement->execute([':matricula' => $matricula]);
            return $statement->rowCount() > 0;
        }

        public function update(CadastroUsuario $usuario): CadastroUsuario|false {

            $query = 'UPDATE controle SET 
                        nome = :nome, 
                        cargo = :cargo,
                        dataNascimento = :dataNascimento,
                        senha = :senhaHash
                    WHERE matricula = :matricula;';

            $statement = Database::getConnection()->prepare(query: $query);
            $success = $statement->execute([
                ':matricula' => $usuario->getMatricula(),
                ':nome' => $usuario->getNome(),
                ':cargo' => $usuario->getCargo(),
                ':dataNascimento' => $usuario->getDataNascimento()->format('Y-m-d'),
                ':senhaHash' => $usuario->getSenhaHash()
            ]);

            if (!$success) {
                return false;
            }

            return $usuario;
        }


    }