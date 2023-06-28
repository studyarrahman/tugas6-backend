<?php
include 'connection.php';

$conn = getConnection();
$response = array();

try {
    if ($_POST) {
        $nomor = $_POST["nomor"];
        $nama = $_POST["nama"];
        $jenis_kelamin = $_POST["jenis_kelamin"];
        $alamat = $_POST["alamat"];
        $no_hp = $_POST["no_hp"];
        $tanggal_terdaftar = $_POST["tanggal_terdaftar"];

        // Memeriksa apakah nomor anggota sudah terdaftar dalam tabel anggota
        $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM anggota WHERE nomor = :nomor");
        $stmt->bindParam(':nomor', $nomor);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result['count'] > 0) {
            // Nomor anggota sudah terdaftar
            $response["message"] = "Nomor anggota sudah terdaftar";
        } else {
            // Nomor anggota belum terdaftar, lanjutkan dengan operasi insert
            $statement = $conn->prepare("INSERT INTO `anggota`(`nomor`, `nama`, `jenis_kelamin`, `alamat`, `no_hp`, `tanggal_terdaftar`) 
                                        VALUES (:nomor, :nama, :jenis_kelamin, :alamat, :no_hp, :tanggal_terdaftar)");
            $statement->bindParam(':nomor', $nomor);
            $statement->bindParam(':nama', $nama);
            $statement->bindParam(':jenis_kelamin', $jenis_kelamin);
            $statement->bindParam(':alamat', $alamat);
            $statement->bindParam(':no_hp', $no_hp);
            $statement->bindParam(':tanggal_terdaftar', $tanggal_terdaftar);
            $statement->execute();
            $response["message"] = "Data anggota berhasil ditambahkan";
        }
    } else {
        $response["message"] = "Permintaan tidak valid";
    }
} catch (PDOException $e) {
    $response["message"] = "Error: " . $e->getMessage();
}

echo json_encode($response, JSON_PRETTY_PRINT);
?>
