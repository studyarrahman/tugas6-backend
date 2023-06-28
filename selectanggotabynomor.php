<?php
include 'connection.php';

$conn = getConnection();
$response = array();

if (isset($_GET['nomor'])) {
    $nomor = $_GET['nomor'];

    try {
        $statement = $conn->prepare("SELECT * FROM `anggota` WHERE `nomor` = :nomor");
        $statement->bindParam(':nomor', $nomor);
        $statement->execute();
        $anggota = $statement->fetch(PDO::FETCH_ASSOC);

        if ($anggota) {
            $response["data"] = $anggota;
        } else {
            $response["message"] = "Anggota tidak ditemukan";
        }
    } catch (PDOException $e) {
        $response["message"] = "Error: " . $e->getMessage();
    }
} else {
    $response["message"] = "Permintaan tidak valid";
}

echo json_encode($response, JSON_PRETTY_PRINT);
?>
