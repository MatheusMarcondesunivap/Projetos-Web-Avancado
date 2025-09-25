<?php
require_once '../model/Curso.php';
require_once '../middleware/CursoMiddleware.php';

class CursoController {
    public function inserir() {
        $dados = json_decode(file_get_contents('php://input'), true);
        $middleware = new CursoMiddleware();
        $erros = $middleware->validarInsercao($dados);

        if (!empty($erros)) {
            http_response_code(400);
            echo json_encode(['erros' => $erros]);
            return;
        }

        $curso = new Curso();
        $curso->nome_curso = $dados['nome_curso'];
        $curso->coordenador = $dados['coordenador'];

        if ($curso->inserir()) {
            http_response_code(201);
            echo json_encode(['mensagem' => 'Curso inserido com sucesso']);
        } else {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao inserir curso']);
        }
    }

    public function listar() {
        $curso = new Curso();
        $dados = $curso->buscarTodos();
        echo json_encode($dados);
    }

    public function buscarPorId($id) {
        $curso = new Curso();
        $dados = $curso->buscarPorId($id);
        if ($dados) {
            echo json_encode($dados);
        } else {
            http_response_code(404);
            echo json_encode(['erro' => 'Curso não encontrado']);
        }
    }

    public function atualizar($id) {
        $dados = json_decode(file_get_contents('php://input'), true);
        $middleware = new CursoMiddleware();
        $erros = $middleware->validarAtualizacao($dados);

        if (!empty($erros)) {
            http_response_code(400);
            echo json_encode(['erros' => $erros]);
            return;
        }

        $curso = new Curso();
        $curso->nome_curso = $dados['nome_curso'];
        $curso->coordenador = $dados['coordenador'];

        if ($curso->atualizar($id)) {
            echo json_encode(['mensagem' => 'Curso atualizado com sucesso']);
        } else {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao atualizar curso']);
        }
    }

    public function excluir($id) {
        $curso = new Curso();
        if ($curso->excluir($id)) {
            echo json_encode(['mensagem' => 'Curso excluído com sucesso']);
        } else {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao excluir curso']);
        }
    }
}
