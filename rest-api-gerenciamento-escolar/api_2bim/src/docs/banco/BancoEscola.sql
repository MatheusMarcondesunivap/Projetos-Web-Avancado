CREATE DATABASE IF NOT EXISTS Escola ;
USE Escola;

create table controle(
	matricula INT PRIMARY KEY,
	nome VARCHAR(128) NOT NULL,
	cargo VARCHAR(20) NOT NULL,
	senha VARCHAR(64) NOT NULL
);
-- 1. Tabela de cursos
CREATE TABLE cursos (
	id_curso INT PRIMARY KEY AUTO_INCREMENT,
	nome_curso VARCHAR(20) NOT NULL,
	coordenador VARCHAR(128) NOT NULL
);

-- 2. Tabela de turmas (depende de cursos)
CREATE TABLE turmas (
	id_turma INT PRIMARY KEY AUTO_INCREMENT,
	ano_turma INT NOT NULL,
	letra VARCHAR(1) NOT NULL,
	id_curso INT NOT NULL,
	FOREIGN KEY (id_curso) REFERENCES cursos(id_curso) ON DELETE CASCADE
);

SELECT * FROM controle;

-- 3. Tabela de alunos (depende de turmas)
CREATE TABLE alunos (
	matricula INT PRIMARY KEY,
	nome VARCHAR(128) NOT NULL,
	data_nascimento DATE,
	id_turma INT,
	FOREIGN KEY (id_turma) REFERENCES turmas(id_turma) ON DELETE CASCADE
);

-- Inserindo cursos
INSERT INTO cursos (nome_curso, coordenador) VALUES
('Informática', 'Prof. Alberson Wander'),
('Eletrônica', 'Prof. Alberson Wander'),
('Análises Clínicas', 'Prof. Daniela'),
('Administração', 'Prof. Carlos');

-- Conferir cursos
SELECT * FROM cursos;

-- Inserindo turmas
INSERT INTO turmas (ano_turma, letra, id_curso) VALUES
(1, 'A', 2),
(1, 'B', 4),
(2, 'I', 1),
(2, 'C', 3);

-- Conferir turmas
SELECT * FROM turmas;

-- Inserindo alunos (corrigido o ano da Maria)
INSERT INTO alunos (nome, data_nascimento, id_turma) VALUES
('João da Silva', '2008-05-15', 3),
('Maria Oliveira', '2010-08-20', 1),
('Pedro Santos', '2006-12-30', 3),
('Ana Costa', '2006-03-10', 4);
-- Conferir alunos
SELECT * FROM alunos;