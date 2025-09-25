<?php

// Declaração de modo estrito para tipagem forte (evita coerção de tipos)
declare(strict_types=1);

class Response implements JsonSerializable // implementar JsonSerialize
{
    public function __construct(
        private bool $success = true,
        private ?string $message = null, // indica possibilidade de ser nulo
        private ?array $data = null,
        private ?array $error = null,
        private int $httpCode = 200 // Código de status HTTP padrão é 200 (OK)
    ) {

    }
    public function jsonSerialize(): array
    {
        $response = [];
        $response['success'] = $this->success;

        if (!empty($this->message)) {
            $response['message'] = $this->message;
        }
        // Se o atributo $data não estiver vazio, inclui no array de resposta
        if (!empty($this->data)) {
            $response['data'] = $this->data;
        }

        if (!empty($this->error)) {
            $response['error'] = $this->error;
        }

        // Retorna o array que será convertido em JSON
        return $response;
    }
    public function send(): never
    {
        header(header: "Content-Type: application/json"); // Define o cabeçalho da resposta como JSON
        
        http_response_code(response_code: $this->httpCode);// Define o código de status HTTP da resposta

        echo json_encode(value: $this);// Converte o objeto para JSON e envia para a saída padrão

        // Encerra o script imediatamente após enviar a resposta
        exit(); // Encerra a execução após enviar a resposta
    }
}


/**$obj = new Response(
    success: true,
    message: "Operação realizada com sucesso",
    data: ["id" => 1, "name" => "Exemplo"],
    
);
$obj->send(); // Exemplo de uso, converte o objeto para JSON e exibe**/
?>