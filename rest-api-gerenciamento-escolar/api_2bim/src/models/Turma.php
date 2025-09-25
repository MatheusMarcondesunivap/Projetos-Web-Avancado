<?php // representa sgbd
    class Turma implements JsonSerializable {
        
        public function __construct(
            private ?int $idTurma = null,
            private ?int $anoTurma = null,
            private string $letra = '',
            private ?int $idCurso = null,
        ) {}

        
        public function JsonSerialize(): array{
            return [
                'idTurma' => $this->idTurma,
                'anoTurma' => $this->anoTurma,
                'letra' => $this->letra,
                'idCurso' => $this->idCurso
            ];
        }
        public function getIdTurma(): ?int
        {
            return $this->idTurma;
        }
        public function setIdTurma(?int $idTurma): self
        {
            $this->idTurma = $idTurma;
            return $this;
        }
        public function getAnoTurma(): ?int
        {
            return $this->anoTurma;
        }

        public function setAnoTurma(?int $anoTurma): self
        {
            $this->anoTurma = $anoTurma;
            return $this;
        }

        public function getLetra(): string
        {
            return $this->letra;
        }

        public function setLetra(string $letra): self
        {
            $this->letra = $letra;
            return $this;
        }

        public function getIdCurso(): ?int
        {
            return $this->idCurso;
        }

        public function setIdCurso(?int $idCurso): self
        {
            $this->idCurso = $idCurso;
            return $this;
        }

    }
?>