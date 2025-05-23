<?php
namespace App\Model;
use App\Lib\Database;


use PDO;
class RequestDocumentModel
{
    protected PDO $pdo;
    
    
    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function saveDocument(int $requestId, string $filePath, string $label): void
    {
        var_dump('save');
        $stmt = $this->pdo->prepare("INSERT INTO request_documents (
            request_id, file_path, label, status, uploaded_at
        ) VALUES (
            :request_id, :file_path, :label, :status, NOW()
        )");

        $stmt->execute([
            'request_id' => $requestId,
            'file_path'  => $filePath,
            'label'      => $label,
            'status'     => 'submitted'
        ]);
    }
    public function getDocumentsForRequest(int $requestId): array
    {
        $sql = "SELECT label, file_path FROM request_documents WHERE request_id = :requestId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['requestId' => $requestId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

}
