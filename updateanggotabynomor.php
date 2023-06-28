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

        // Memeriksa apakah nomor anggota sudah ada dalam tabel anggota
        $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM anggota WHERE nomor = :nomor");
        $stmt->bindParam(':nomor', $nomor);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result['count'] > 0) {
            // Nomor anggota ada, lanjutkan dengan operasi update

            // Memeriksa apakah jenis kelamin valid
            if ($jenis_kelamin === "Laki-laki" || $jenis_kelamin === "Perempuan") {
                // Jenis kelamin valid, lanjutkan dengan operasi update
                $statement = $conn->prepare("UPDATE `anggota` SET `nama` = :nama, `jenis_kelamin` = :jenis_kelamin, `alamat` = :alamat, `no_hp` = :no_hp, `tanggal_terdaftar` = :tanggal_terdaftar 
                                            WHERE `nomor` = :nomor");
                $statement->bindParam(':nomor', $nomor);
                $statement->bindParam(':nama', $nama);
                $statement->bindParam(':jenis_kelamin', $jenis_kelamin);
                $statement->bindParam(':alamat', $alamat);
                $statement->bindParam(':no_hp', $no_hp);
                $statement->bindParam(':tanggal_terdaftar', $tanggal_terdaftar);
                $statement->execute();
                $response["message"] = "Data anggota berhasil diperbarui";
            } else {
                // Jenis kelamin tidak valid
                $response["message"] = "Jenis kelamin tidak valid. Harap masukkan 'Laki-laki' atau 'Perempuan'.";
            }
        } else {
            // Nomor anggota tidak ditemukan dalam database
            $response["message"] = "Nomor anggota tidak ditemukan";
        }
    } else {
        $response["message"] = "Permintaan tidak valid";
    }
} catch (PDOException $e) {
    $response["message"] = "Error: " . $e->getMessage();
}

echo json_encode($response, JSON_PRETTY_PRINT);
?>
