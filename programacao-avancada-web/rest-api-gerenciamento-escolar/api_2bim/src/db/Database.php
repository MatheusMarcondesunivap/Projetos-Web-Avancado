<?php 
// lidar com conexão ao banco de dados, backup e restauração
// estático, não precisa de instância
require_once "api_2bim/src/http/Response.php";
require_once "api_2bim/src/utils/Logger.php";

class Database
{
  private const HOST = '127.0.0.1';
  private const USER = 'root';
  private const PASSWORD = '';
  private const DATABASE = 'escola';
  private const PORT = 3306;

  // suporta todos os caracteres Unicode (incluindo emojis) 
  private const CHARACTER_SET = 'utf8mb4';

  private static ?PDO $CONNECTION = null; // armazena a conexão PDO
  
  public static function getConnection(): PDO|null
    {
      // Verifica se a conexão já existe
      if (Database::$CONNECTION === null) {
        // Se não existir, estabelece uma nova conexão
        Database::connect();
      }

      // Retorna a conexão existente ou recém-criada
      return Database::$CONNECTION;
    }
  private static function connect(): PDO // retorna uma conexão
  {
    // Formata a string DSN (Data Source Name) com os parâmetros de conexão
    $dsn = sprintf(  // escreve em uma string formatada
      'mysql:host=%s;port=%d;dbname=%s;charset=%s',
      Database::HOST,
      Database::PORT,
      Database::DATABASE,
      Database::CHARACTER_SET
    );

    // Cria a instância PDO com os parâmetros de conexão
    Database::$CONNECTION = new PDO(
      dsn: $dsn,                          // String de conexão formatada
      username: Database::USER,           // Usuário do banco de dados
      password: Database::PASSWORD,       // Senha do banco de dados
      options: [                          // Opções de configuração
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Lança exceções em erros, pegar uma try catch
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ // Retorna objetos por padrão, mais intuitivo
      ]
    );

    return Database::$CONNECTION; // Retorna a conexão PDO criada
  }

 
  /* public static function backup(): void
  {
    // Caminho relativo do arquivo de backup
    $backupPath = "system/backup_" . date('Y_m_d_H_i_s') . ".sql";

    // Garante que o diretório exista
    $directory = dirname($backupPath);
    if (!is_dir($directory)) {
      mkdir($directory, 0777, true); // Cria o diretório e os pais, se necessário
    }

    $pdo = self::getConnection();
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

    $backupFile = fopen($backupPath, 'w');

    if ($backupFile === false) {
      Logger::log(new \Exception('Erro ao criar o arquivo de backup.'));
      (new Response(
        success: false,
        message: 'Erro ao criar o arquivo de backup.',
        httpCode: 500
      ))->send();
      return;
    }

    foreach ($tables as $table) {
      // Escrever estrutura da tabela
      $createTableStmt = $pdo->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_ASSOC);
      fwrite($backupFile, $createTableStmt['Create Table'] . ";\n\n");

      // Contar total de registros da tabela
      $total = $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
      $limit = 1000;

      for ($offset = 0; $offset < $total; $offset += $limit) {
        $stmt = $pdo->prepare("SELECT * FROM `$table` LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $row) {
          $columns = array_keys($row);
          $values = array_map([$pdo, 'quote'], array_values($row)); // forma segura

          $insertStmt = sprintf(
            "INSERT INTO `%s` (`%s`) VALUES (%s);\n",
            $table,
            implode('`, `', $columns),
            implode(', ', $values)
          );
          fwrite($backupFile, $insertStmt);
        }
      }

      fwrite($backupFile, "\n\n"); // Separação entre tabelas
    }

    fclose($backupFile);

    // Envia o arquivo para download
    header('Content-Description: File Transfer');
    header('Content-Type: application/sql');
    header('Content-Disposition: attachment; filename="' . basename($backupPath) . '"');
    header('Content-Length: ' . filesize($backupPath));
    header('Pragma: no-cache');
    header('Expires: 0');
    readfile($backupPath);

    //unlink($backupPath); // Opcional: remover após envio
    exit;
  } */


}