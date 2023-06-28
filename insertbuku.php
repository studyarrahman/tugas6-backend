<?php
// Panggil file untuk koneksi
include 'connection.php';

// Prepare, bind, execute
$conn = getConnection();

try {
    if ($_POST) {
        $kode = $_POST["kode"];
        $kode_kategori = $_POST["kode_kategori"];
        $judul = $_POST["judul"];
        $pengarang = $_POST["pengarang"];
        $penerbit = $_POST["penerbit"];
        $tahun = $_POST["tahun"];
        $tanggal_input = $_POST["tanggal_input"];
        $harga = $_POST["harga"];

        if (isset($_FILES["file_cover"]["name"])) {
            $file_name = $_FILES["file_cover"]["name"];
            $extensions = ["jpg", "png", "jpeg"];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if (in_array($file_ext, $extensions)) {
                $upload_path = 'upload/file_cover/' . $file_name;

                if (move_uploaded_file($_FILES["file_cover"]["tmp_name"], $upload_path)) {
                    $file_cover = "http://localhost/library/" . $upload_path;

                    // Memeriksa apakah kode buku sudah ada
                    $statement = $conn->prepare("SELECT COUNT(*) AS count FROM buku WHERE kode = :kode");
                    $statement->bindParam(':kode', $kode);
                    $statement->execute();
                    $result = $statement->fetch(PDO::FETCH_ASSOC);

                    if ($result['count'] > 0) {
                        $response["message"] = "Kode buku sudah ada. Tidak bisa menambah data. Periksa kembali!";
                    } else {
                        // Memeriksa apakah kode kategori ada dalam database
                        $statement = $conn->prepare("SELECT COUNT(*) AS count FROM kategori WHERE kode = :kode_kategori");
                        $statement->bindParam(':kode_kategori', $kode_kategori);
                        $statement->execute();
                        $result = $statement->fetch(PDO::FETCH_ASSOC);

                        if ($result['count'] > 0) {
                            try {
                                $sql = "INSERT INTO buku (kode, kode_kategori, judul, pengarang, penerbit, tahun, tanggal_input, harga, file_cover)
                                        VALUES (:kode, :kode_kategori, :judul, :pengarang, :penerbit, :tahun, :tanggal_input, :harga, :file_cover)";
                                $stmt = $conn->prepare($sql);

                                $stmt->bindParam(':kode', $kode);
                                $stmt->bindParam(':kode_kategori', $kode_kategori);
                                $stmt->bindParam(':judul', $judul);
                                $stmt->bindParam(':pengarang', $pengarang);
                                $stmt->bindParam(':penerbit', $penerbit);
                                $stmt->bindParam(':tahun', $tahun);
                                $stmt->bindParam(':tanggal_input', $tanggal_input);
                                $stmt->bindParam(':harga', $harga);
                                $stmt->bindParam(':file_cover', $file_cover);

                                $stmt->execute();

                                $response["message"] = "Data buku berhasil ditambahkan.";
                            } catch (PDOException $e) {
                                $response["message"] = "Error: " . $e->getMessage();
                            }
                        } else {
                            $response["message"] = "Kode kategori tidak ditemukan. Tidak bisa menambah data. Periksa kembali!";
                        }
                    }
                } else {
                    $response["message"] = "Error uploading file.";
                }
            } else {
                $response["message"] = "Ekstensi file yang diunggah tidak valid. Hanya file dengan ekstensi JPG, JPEG, atau PNG yang diperbolehkan.";
            }
        } else {
            $response["message"] = "File cover tidak ditemukan.";
        }
    }
} catch (PDOException $e) {
    $response["message"] = "Error: " . $e->getMessage();
}

echo json_encode($response, JSON_PRETTY_PRINT);
?>
