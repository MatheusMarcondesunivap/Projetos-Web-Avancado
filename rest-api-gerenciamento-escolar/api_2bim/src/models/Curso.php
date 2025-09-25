<?php // representa sgbd
    class Curso implements JsonSerializable {
        
        public function __construct(
            private ?int $id_curso = null,
            private string $nome_curso = '',
            private string $coordenador = ''
        ) {}

        
        public function JsonSerialize(): array{
            return [
                'idCurso' => $this->id_curso,
                'nomeCurso' => $this->nome_curso,
                'coordenador' => $this->coordenador
            ];
        }
        
        public function getIdCurso(): ?int {
            return $this->id_curso;
        }
        public function setIdCurso(?int $id_curso): self {
            $this->id_curso = $id_curso;
            return $this;
        }
        public function getNomeCurso(): string {
            return $this->nome_curso;
        }

        public function setNomeCurso(string $nome_curso): self {
            $this->nome_curso = $nome_curso;
            return $this;
        }

        public function getCoordenador(): string {
            return $this->coordenador;
        }

        public function setCoordenador(string $coordenador): self {
            $this->coordenador = $coordenador;
            return $this;
        }
    }
?>