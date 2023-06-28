<?php
include 'connection.php';

$conn = getConnection();
$response = array();

if (isset($_GET['nomor'])) {
    $nomor = $_GET['nomor'];

    try {
        // Memeriksa apakah nomor anggota terdapat dalam tabel anggota
        $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM anggota WHERE nomor = :nomor");
        $stmt->bindParam(':nomor', $nomor);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result['count'] > 0) {
            // Nomor anggota terdapat dalam database, lanjutkan dengan operasi delete
            $statement = $conn->prepare("DELETE FROM `anggota` WHERE `nomor` = :nomor");
            $statement->bindParam(':nomor', $nomor);
            $statement->execute();
            $response["message"] = "Data anggota berhasil dihapus";
        } else {
            // Nomor anggota tidak terdapat dalam database
            $response["message"] = "Nomor anggota tidak ditemukan";
        }
    } catch (PDOException $e) {
        $response["message"] = "Error: " . $e->getMessage();
    }
} else {
    $response["message"] = "Permintaan tidak valid";
}

echo json_encode($response, JSON_PRETTY_PRINT);
?>
