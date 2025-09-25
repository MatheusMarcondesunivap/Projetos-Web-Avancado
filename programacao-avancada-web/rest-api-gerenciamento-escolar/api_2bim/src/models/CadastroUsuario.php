<?php
class CadastroUsuario implements JsonSerializable {
        
        public function __construct(
            private ?int $matricula = null,
            private string $nome = '',
            private string $cargo = '',
            private DateTime $dataNascimento = new DateTime(),
            private string $senha = '',
            private string $senhaHash = ''
        ) {}

        
        public function JsonSerialize(): array{
            return [
                'matricula' => $this->matricula,
                'nome' => $this->nome,
                'cargo' => $this->cargo,
                'dataNascimento' => $this->dataNascimento,
                'senha' => $this->senha,
                'senhaHash' => $this->senhaHash
            ];
        }
        
        public function getSenhaHash(): string {
            return $this->senhaHash; // Retorna a senha hash
        }
        public function setSenhaHash(string $senhaHash): self {
            $this->senhaHash = $senhaHash;
            return $this;
        }

        public function getMatricula(): ?int {
            return $this->matricula;
        }

        public function setMatricula(?int $matricula): self {
            $this->matricula = $matricula;
            return $this;
        }


        public function getNome(): string {
            return $this->nome;
        }

        public function setNome(string $nome): self {
            $this->nome = $nome;
            return $this;
        }

        public function getCargo(): string {
            return $this->cargo;
        }

        public function setCargo(string $cargo): self {
            $this->cargo = $cargo;
            return $this;
        }

        public function getSenha(): string {
            return $this->senha;
        }

        public function setSenha(string $senha): self {
            $this->senha = $senha;
            return $this;
        }

        public function getDataNascimento(): DateTime {
            return $this->dataNascimento;
        }

        public function setDataNascimento(DateTime $dataNascimento): self {
            $this->dataNascimento = $dataNascimento;
            return $this;
        }
    }
