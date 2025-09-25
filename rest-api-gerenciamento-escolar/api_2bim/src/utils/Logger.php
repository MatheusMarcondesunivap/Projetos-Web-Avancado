<?php
// gravar logs

class Logger
{
    private static string $LOG_FILE = 'system/log.log';
    
    public static function logError(string $errorMessage): void
    {
        self::writeLog(type: "THROWABLE", message: $errorMessage);
    }
    public static function log(Throwable $exception): void
    {
        $message = "Throwable:\n";
        $message .= "Message: " . $exception->getMessage() . "\n";
        $message .= "Code: " . $exception->getCode() . "\n";
        $message .= "File: " . $exception->getFile() . "\n";
        $message .= "Line: " . $exception->getLine() . "\n";
        $message .= "Trace:\n" . $exception->getTraceAsString();

        self::writeLog(type: "Throwable", message: $message);
    }

    private static function writeLog(string $type, string $message): void
    {
        // Define o caminho completo para o arquivo de log
        $directoryPath = dirname(path: self::$LOG_FILE); // Obtém o diretório do arquivo de log

        // Verifica se o diretório existe; caso contrário, cria o diretório
        if (!is_dir(filename: $directoryPath)) {
            mkdir(directory: $directoryPath, permissions: 0777, recursive: true); // Cria o diretório, incluindo diretórios pai, se necessário
        }

        // Cria a entrada de log
        $dateTime = date(format: 'Y-m-d H:i:s.v');
        $separador = str_repeat(string: "#", times: 50);
        $entry = "[$dateTime] [$type] \n $message \n $separador \n";

        // Escreve no arquivo de log, :: pq é estático
        file_put_contents(filename: self::$LOG_FILE, data: $entry, flags: FILE_APPEND | LOCK_EX); // adiciona o arquivo e garante que não haja concorrência
    }

}