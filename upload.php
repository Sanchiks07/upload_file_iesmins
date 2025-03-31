<?php
ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
error_reporting(E_ALL);

require_once "config.php";

$successMessage = "";

//pārbauda vai fails tika augšupielādēts caur mūsu formu
if (isset($_FILES["uploadedFile"])) {
    $file = $_FILES["uploadedFile"];
    $fileName = $file["name"];
    $fileTempName = $file["tmp_name"];
    $fileError = $file["error"];

    $uploadDir = "uploads/";

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true); //777 = read+write+exe
    }

    $filePath = $uploadDir . basename($fileName); //pilnais path kur fails tiks augšupielādēts

    if ($fileError === 0) {
        if (move_uploaded_file($fileTempName, $filePath)) { //temp fialu pārceļam uz īsto lokāciju
            $successMessage .= "✅ File successfully uploaded to folder.\n";

            $sql = "INSERT INTO upload_paths (file_name, file_path) VALUES (:fileName, :filePath)";
            $stmt = $conn->prepare($sql);

            try {
                $stmt->execute([
                    ":fileName" => $fileName,
                    ":filePath" => $filePath
                ]);

                if ($stmt->rowCount() > 0) { //pārbauda vai datubāzē dati tika ievietoti veiksmīgi
                    $successMessage .= "✅ File path saved in the database successfully!";
                } else {
                    $successMessage .= "Failed to save file path in the database.";
                }
            } catch (PDOException $e) {
                $successMessage .= "Error saving file to the database: " . $e->getMessage();
            }
        } else {
            $successMessage .= "Failed to upload the file!";
        }
    } else {
        $successMessage .= "Error uploading your file!";
    }
}

$conn = null; //aizver db connection, lai taupītu mūsu servera resursus

header("Location: index.html?successMessage=" . urlencode($successMessage));
exit();
?>