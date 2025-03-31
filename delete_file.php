<?php
ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
error_reporting(E_ALL);

require_once "config.php";

if (isset($_POST["fileId"])) {
    $fileId = $_POST["fileId"];
    
    try {
        $stmt = $conn->prepare("SELECT file_path FROM upload_paths WHERE id = :id");
        $stmt->execute([":id" => $fileId]);
        $file = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($file) {
            $filePath = $file["file_path"];

            // izdzēš failu no mūsu uploads mapes
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            $stmt = $conn->prepare("DELETE FROM upload_paths WHERE id = :id");
            $stmt->execute([":id" => $fileId]);

            if ($stmt->rowCount() > 0) {
                echo "✅ File deleted successfully!";
            } else {
                echo "Failed to delete file from the databse.";
            }
        } else {
            echo "File not found.";
        }
    } catch (PDOException $e) {
        echo "Error deleting file: " . $e->getMessage();
    }
} else {
    echo "No file ID provided.";
}

$conn = null;
?>