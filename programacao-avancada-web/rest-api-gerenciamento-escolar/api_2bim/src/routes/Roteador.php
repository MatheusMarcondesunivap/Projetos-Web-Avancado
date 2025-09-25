<?php
require_once "api_2bim/src/routes/Router.php";
require_once "api_2bim/src/utils/Logger.php";
require_once "api_2bim/src/http/Response.php";

require_once "api_2bim/src/middlewares/AlunosMiddleware.php";
require_once "api_2bim/src/middlewares/CursosMiddleware.php";
require_once "api_2bim/src/middlewares/TurmasMiddleware.php";
require_once "api_2bim/src/middlewares/LoginMiddleware.php";
require_once "api_2bim/src/middlewares/CadastroMiddleware.php";
require_once "api_2bim/src/middlewares/JWTMiddleware.php";

require_once "api_2bim/src/DAO/alunosDAO.php";
require_once "api_2bim/src/DAO/cursosDAO.php";
require_once "api_2bim/src/DAO/turmasDAO.php";
require_once "api_2bim/src/DAO/loginDAO.php";

require_once "api_2bim/src/controller/alunosControl.php";
require_once "api_2bim/src/controller/cursosControl.php";
require_once "api_2bim/src/controller/turmasControl.php";
require_once "api_2bim/src/controller/loginControl.php";
require_once "api_2bim/src/controller/cadastroControl.php";
class Roteador {
    public function __construct(private Router $router = new Router())
    {
        $this->router = new Router();
        $this->setupLoginRoutes();
        $this->setUpHeaders();
        $this->setUpAlunos();
        $this->setUpCursos();
        $this->setUpTurmas();
        $this->setUp404Route();
    }
    private function setUpHeaders(): void {
        // Set up CORS headers
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
    }

    private function setupLoginRoutes() {
        $this->router->post('/auth/register', function() {
            try{
                $requestBody = file_get_contents('php://input');

                $cadastroMiddleware = new CadastroMiddleware();
                $objStd = $cadastroMiddleware->stringJsonToStdClass($requestBody);

                $cadastroMiddleware
                    ->isValidID($objStd->controle->Matricula)
                    ->isValidNome($objStd->controle->Nome)
                    ->isValidCargo($objStd->controle->Cargo);

                (new CadastroControl())->store(stdCadastro: $objStd);
            }
            catch (Throwable $exception) {
                $this->handleError($exception, "Error during registration");
            }
            exit();
        });
        $this->router->post('/auth/login', function() {
            try{
                $requestBody = file_get_contents('php://input');
                $LoginMiddleware = new LoginMiddleware();
                $stdLogin = $LoginMiddleware->stringJsonToStdClass($requestBody);

                $LoginMiddleware
                    ->isValidMatricula($stdLogin->controle->Matricula)
                    ->isValidSenha($stdLogin->controle->Senha);

                (new LoginControl())->autenticar($stdLogin);

            }
            catch (Throwable $exception) {
                $this->handleError($exception, "Error during login");
            }
            exit();
        });



        $this->router->get('/usuarios', function() {  
            try{
                $jwtMiddleware = new JWTMiddleware();
                $claims = $jwtMiddleware->isValidToken();  
                if ($claims->public->Role === "admin") {
                    (new CadastroControl())->index();
                } else {
                    $this->unauthorizedResponse();
                }
            }
            catch (Throwable $exception) {
                $this->handleError($exception, "Error fetching users");
            }
            exit();
        });
        $this->router->get('/usuarios/(\d+)', function($id) {
            try{
                $jwtMiddleware = new JWTMiddleware();
                $claims = $jwtMiddleware->isValidToken();
                if ($claims->public->Role === "admin" || $claims->private->matricula == (int)$id) {
                    (new CadastroMiddleware())->isValidID((int)$id);
                    (new CadastroControl())->show((int)$id);
                } else {
                    $this->unauthorizedResponse();
                }
                
            }
            catch (Throwable $exception) {
                $this->handleError($exception, "Error fetching user");
            }
            exit();
        });
        $this->router->put('/usuarios/(\d+)', function($id) {
            try{
                $jwtMiddleware = new JWTMiddleware();
                $claims = $jwtMiddleware->isValidToken();
                $requestBody = file_get_contents('php://input');
                $cadastroMiddleware = new CadastroMiddleware();
                $stdCadastro = $cadastroMiddleware->stringJsonToStdClass($requestBody);
                
                    if ($claims->public->Role === "admin") {
                        $requestBody = file_get_contents('php://input');
                        $cadastroMiddleware = new CadastroMiddleware();
                        $stdCadastro = $cadastroMiddleware->stringJsonToStdClass($requestBody);
                        $cadastroMiddleware->isValidID((int)$id);

                        if (!empty($stdCadastro->controle->Nome)) {
                            $cadastroMiddleware->isValidNome($stdCadastro->controle->Nome);
                        }
                        if (!empty($stdCadastro->controle->Cargo)) {
                            $cadastroMiddleware->isValidCargo($stdCadastro->controle->Cargo);
                        }
                        if (!empty($stdCadastro->controle->Senha)) {
                            $cadastroMiddleware->isValidSenha($stdCadastro->controle->Senha);
                        } 
                        $stdCadastro->controle->Matricula = (int)$id;
                        (new CadastroControl())->edit($stdCadastro);
                    } else if ($claims->public->Role === "aluno" && $claims->private->matricula === (int)$id
                                && !empty($stdCadastro->controle->Senha)) {
                        $cadastroMiddleware->isValidSenha($stdCadastro->controle->Senha);
                        (new CadastroControl())->edit($stdCadastro);
                    } else {
                        $this->unauthorizedResponse();
                    }
                
            }
            catch (Throwable $exception) {
                $this->handleError($exception, "Error updating user");
            }
            exit();
        });
        $this->router->delete('/usuarios/(\d+)', function($id) {
            try{
                $claims = (new JWTMiddleware())->isValidToken();
                    if ($claims->public->Role === "admin") {
                        (new CadastroMiddleware())->isValidID((int)$id);

                        (new CadastroControl())->delete((int)$id);
                    } else {
                        $this->unauthorizedResponse();
                    }
            }
            catch (Throwable $exception) {
                $this->handleError($exception, "Error deleting user");
            }
            exit();
        });
    }
    






    private function setUpAlunos(): void {#Get alunos by curso
        $this->router->get(pattern: '/alunos', fn: function(): never {
            try{
                $claims = (new JWTMiddleware())->isValidToken();
                if ($claims->public->Role === "admin") {
                    (new alunosControl())->index();
                } else {
                    $this->unauthorizedResponse();
                }
            }catch (Throwable $exception) {
                $this->handleError(exception: $exception, message: "Erro na seleção de alunos.");
            }
            
            exit();
        });
        $this->router->get(pattern: '/alunos/(\d+)', fn: function($idAluno): never{
            try{
                $claims = (new JWTMiddleware())->isValidToken();
                if ($claims->public->Role === "admin" || ($claims->private->matricula == (int)$idAluno && $claims->public->Role === "aluno")) {
                    (new AlunosMiddleware())
                        ->IsValidID(idAluno: (int)$idAluno);

                    (new alunosControl())
                        ->show(idAluno: (int)$idAluno);
                } else {
                    $this->unauthorizedResponse();
                }
                
            }catch (Throwable $exception) {
                $this->handleError(exception: $exception, message: "Erro na seleção de alunos.");
            }
            
            exit();
        });
        $this->router->post(pattern: '/alunos', fn: function(): never {
            try{
                $claims = (new JWTMiddleware())->isValidToken();
                if ($claims->public->Role === "admin") {
                    $requestBody = file_get_contents(filename: 'php://input');
                    $AlunosMiddleware = new AlunosMiddleware();
                    $objStd = $AlunosMiddleware->StringJsonToStdClass(requestBody: $requestBody);
                
                    $AlunosMiddleware
                        ->isValidNomeAluno($objStd->alunos->nome)
                        ->hasNotAlunoByName($objStd->alunos->nome, $objStd->alunos->matricula);

                    $alunosControl = new alunosControl();
                    $alunosControl->store(stdAluno: $objStd);
                } else {
                    $this->unauthorizedResponse();
                }
                
            }catch (Throwable $exception) {
                $this->handleError(exception: $exception, message: "Erro na seleção de alunos.");
            }
            
            exit();
        });
        $this->router->put(pattern: '/alunos/(\d+)', fn: function($idAluno): never {
            try{
                $claims = (new JWTMiddleware())->isValidToken();
                if ($claims->public->Role === "admin") {
                    $requestBody = file_get_contents(filename: 'php://input');
                    $AlunosMiddleware = new AlunosMiddleware();
                    $stdAluno = $AlunosMiddleware->StringJsonToStdClass(requestBody: $requestBody);
                    $AlunosMiddleware
                        ->IsValidID(idAluno: (int)$idAluno)
                        ->isValidNomeAluno(nomeAluno: $stdAluno->alunos->nome)
                        ->hasNotAlunoByName($stdAluno->alunos->nome, $stdAluno->alunos->matricula);
                    $stdAluno->alunos->idAluno = (int)$idAluno;
                    $alunosControl = new alunosControl();
                    $alunosControl->edit(stdAluno: $stdAluno);
                } else {
                    $this->unauthorizedResponse();
                }
            }catch (Throwable $exception) {
                $this->handleError(exception: $exception, message: "Erro na atualização de alunos.");
            }
            
            exit();
        });
        $this->router->delete(pattern: '/alunos/(\d+)', fn: function($idAluno): never {
            try{
                $claims = (new JWTMiddleware())->isValidToken();
                if ($claims->public->Role === "admin" || $claims->private->matricula == (int)$idAluno) {
                    $alunosMiddleware = new AlunosMiddleware();  
                    $alunosMiddleware->IsValidID(idAluno: $idAluno); 
                    $alunosControl = new alunosControl();
                    $alunosControl->destroy(idAluno: (int)$idAluno);
                } else {
                    $this->unauthorizedResponse();
                }
            }catch (Throwable $exception) {
                $this->handleError(exception: $exception, message: "Erro na deleção de alunos.");
            }
            
            exit();
        });
    }


    private function setUpCursos(): void {
        $this->router->get(pattern: '/cursos', fn: function(): never {
            try{
                
                (new cursosControl())->index();
            }catch (Throwable $exception) {
                $this->handleError(exception: $exception, message: "Erro na seleção de cursos.");
            }
            
            exit();
        });
        $this->router->get(pattern: '/cursos/(\d+)', fn: function($idCurso): never {
            try{
                (new CursosMiddleware())
                    ->IsValidID(idCurso: (int)$idCurso);

                (new cursosControl())
                    ->show(idCurso: (int)$idCurso);

            }catch (Throwable $exception) {
                $this->handleError(exception: $exception, message: "Erro na seleção de cursos.");
            }
            
            exit();
        });
        $this->router->post(pattern: '/cursos', fn: function(): never {
            try{
                $claims = (new JWTMiddleware())->isValidToken();
                if ($claims->public->Role === "admin") {
                    $requestBody = file_get_contents(filename: 'php://input');
                    $cursosMiddleware = new CursosMiddleware();
                    $objStd = $cursosMiddleware->StringJsonToStdClass(requestBody: $requestBody);
                
                    $cursosMiddleware
                        ->isValidNomeCurso($objStd->cursos->nomeCurso)
                        ->hasNotCursoByName($objStd->cursos->nomeCurso);

                    $cursosControl = new cursosControl();
                    $cursosControl->store(stdCurso: $objStd);
                } else {
                    $this->unauthorizedResponse();
                }
                
                
            }catch (Throwable $exception) {
                $this->handleError(exception: $exception, message: "Erro na seleção de cursos.");
            }
            
            exit();
        });
        $this->router->put(pattern: '/cursos/(\d+)', fn: function($idCurso): never {
            try{
                $claims = (new JWTMiddleware())->isValidToken();
                if ($claims->public->Role === "admin") {
                    $requestBody = file_get_contents(filename: 'php://input');
                    $cursosMiddleware = new CursosMiddleware();
                    $stdCurso = $cursosMiddleware->StringJsonToStdClass(requestBody: $requestBody);
                    $cursosMiddleware
                        ->IsValidID(idCurso: (int)$idCurso)
                        ->hasNotcursoByName(nomeCurso: $stdCurso->cursos->nomeCurso);
                    $stdCurso->cursos->idCurso = (int)$idCurso;
                    $cursosControl = new cursosControl();
                    $cursosControl->edit(stdCurso: $stdCurso);
                } else {
                    $this->unauthorizedResponse();
                } 
            }catch (Throwable $exception) {
                $this->handleError(exception: $exception, message: "Erro na atualização de cursos.");
            }
            
            exit();
        });
        $this->router->delete(pattern: '/cursos/(\d+)', fn: function($idCurso): never {
            try {
                $claims = (new JWTMiddleware())->isValidToken();
                if ($claims->public->Role === "admin") {
                    $cursosMiddleware = new CursosMiddleware();  
                    $cursosMiddleware->IsValidID(idCurso: $idCurso); 
                    $cursosControl = new cursosControl();
                    $cursosControl->destroy(idCurso: (int)$idCurso);
                } else {
                    $this->unauthorizedResponse();
                }
            } catch (Throwable $exception) {
                $this->handleError(exception: $exception, message: "Erro na deleção de cursos.");
            }
            
            exit();
        });
    }


    private function setUpTurmas(): void {
        $this->router->get(pattern: '/turmas', fn: function(): never {
            try{
                
                (new turmasControl())->index();
            }catch (Throwable $exception) {
                $this->handleError(exception: $exception, message: "Erro na seleção de turmas.");
            }
            exit();
        });
        $this->router->get(pattern: '/turmas/(\d+)', fn: function($idTurma): never {
            try {
                (new TurmasMiddleware())
                    ->IsValidID(idTurma: (int)$idTurma);

                (new turmasControl())
                    ->show(idTurma: (int)$idTurma);
            } catch (Throwable $exception) {
                $this->handleError(exception: $exception, message: "Erro na seleção de turmas.");
            }
            
            exit();
        });
        $this->router->post(pattern: '/turmas', fn: function(): never {
            try {
                $claims = (new JWTMiddleware())->isValidToken();
                if ($claims->public->Role === "admin") {
                    $requestBody = file_get_contents(filename: 'php://input');
                    $turmasMiddleware = new TurmasMiddleware();
                    $objStd = $turmasMiddleware->StringJsonToStdClass(requestBody: $requestBody);
                
                    $turmasMiddleware
                        ->isValidLetra($objStd->turmas->letra)
                        ->isValidAno(ano: (int)$objStd->turmas->anoTurma)
                        ->hasNotTurmaByLetraAndAno($objStd->turmas->letra, ano: (int)$objStd->turmas->anoTurma);

                    $turmasControl = new turmasControl();
                    $turmasControl->store(stdTurma: $objStd);
                } else {
                    $this->unauthorizedResponse();
                }
            } catch (Throwable $exception) {
                $this->handleError(exception: $exception, message: "Erro na seleção de turmas.");
            }
            
            exit();
        });
        $this->router->put(pattern: '/turmas/(\d+)', fn: function($idTurma): never {
            try {
                $claims = (new JWTMiddleware())->isValidToken();
                if ($claims->public->Role === "admin") {
                    $requestBody = file_get_contents(filename: 'php://input');
                    $turmasMiddleware = new TurmasMiddleware();
                    $stdTurma = $turmasMiddleware->StringJsonToStdClass(requestBody: $requestBody);
                    $turmasMiddleware
                        ->IsValidID(idTurma: (int)$idTurma)
                        ->isValidLetra(letra: $stdTurma->turmas->letra)
                        ->hasNotTurmaByLetraAndAno($stdTurma->turmas->letra, ano: (int)$stdTurma->turmas->anoTurma);
                    $stdTurma->turmas->idTurma = (int)$idTurma;
                    $turmasControl = new turmasControl();
                    $turmasControl->edit(stdTurma: $stdTurma);
                } else {
                    $this->unauthorizedResponse();
                }
            } catch (Throwable $exception) {
                $this->handleError(exception: $exception, message: "Erro na atualização de turmas.");
            }
            
            exit();
        });
        $this->router->delete(pattern: '/turmas/(\d+)', fn: function($idTurma): never {
            try {
                $claims = (new JWTMiddleware())->isValidToken();
                if ($claims->public->Role === "admin") {
                    $turmasMiddleware = new TurmasMiddleware();  
                    $turmasMiddleware->IsValidID(idTurma: $idTurma); 
                    $turmasControl = new turmasControl();
                    $turmasControl->destroy(idTurma: (int)$idTurma);
                } else {
                    $this->unauthorizedResponse();
                }

            } catch (Throwable $exception) {
                $this->handleError(exception: $exception, message: "Erro na deleção de turmas.");
            }
            
            exit();
        });
    }




    public function unauthorizedResponse(){
        $response = new Response(
            success: false,
            message: 'Você não possui autorização para executar a operação',
            error: ['codigoError' => 'validation_error', 'message' => 'Credencial de acesso inválida', ],
            httpCode: 401
        );
        $response->send();
    }

    private function handleError(Throwable $exception, $message): void {
        // Log the error
        Logger::log(exception: $exception);
        (new Response(
            success: false,
            message: $message,
            error: [
                'problemCode' => $exception->getCode(),
                'message' => $exception->getMessage(),
            ],
            httpCode: 500
        ))->send();
        exit();
    }

    private function setUp404Route(): void {
        $this->router->set404(match_fn: function(): void {
            (new Response(
                success: false,
                message: "Rota não encontrada.",
                error: [
                    'problemCode' => 'routing_error',
                    'message' => "A rota solicitada não foi mapeada."
                ],
                httpCode: 404
            ))->send();
            exit();
        });
    }


    public function start(): void {
        // Start the router
            $this->router->run();
    }
}
?>