<?php
require_once "config.php";

// norādam image extensions(paplašinājumus), kas parādīs bildes preview iekšā thumbnail
$imageTypes = ["jpg", "jpeg", "png", "gif"];

try {
    $stmt = $conn->query("SELECT * FROM upload_paths");
    $files = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // pārbauda vai faili vispār ir pieejami DB
    if ($files) {
        foreach ($files as $file) {
            $filePath = $file["file_path"];
            $fileName = $file["file_name"];
            $fileId = $file["id"];
            $fileExtention = strtolower(pathinfo($filePath, PATHINFO_EXTENSION)); //pasaka kāds ir faila extension(paplašinājums)

            echo "<div class='file-card' id='file-{$fileId}'>";

            // pārbauda vai attiecīgais fails ir bilde
            if (in_array($fileExtention, $imageTypes)) {
                // ja jā, tad bildi attēlo kā thumbnail
                echo "<img src='{$filePath}' alt='{$fileName}' class='thumbnail'><br>";
            } else {
                //  ja nē, tad attēlo kā dokumenta ikonu
                echo "<div class='file-icon'>📄</div>";
            }

            echo "<div>{$fileName}</div>";
            echo "<a href='{$filePath}' download>Download</a>";
            echo "<button class='delete-button' onclick='deleteFile({$fileId})'>Delete</button>";
            echo "</div>";
        }
    } else {
        echo "<p>No files uploaded yet.</p>";
    }
} catch (PDOException $e) {
    echo "<p>Error fatching files: " . $e->getMessage() . "</p>";
}

$conn = null;
?>