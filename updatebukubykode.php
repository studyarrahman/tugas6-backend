<?php
// updatebukubykode.php
include 'connection.php';

$conn = getConnection();

$response = array();

try {
    if ($_POST) {
        // Mendapatkan data dari permintaan POST
        $kode = $_POST['kode'];
        $kode_kategori = $_POST['kode_kategori'];
        $judul = $_POST['judul'];
        $pengarang = $_POST['pengarang'];
        $penerbit = $_POST['penerbit'];
        $tahun = $_POST['tahun'];
        $tanggal_input = $_POST['tanggal_input'];
        $harga = $_POST['harga'];

        $statement = $conn->prepare("SELECT * FROM buku WHERE kode = :kode");
        $statement->bindParam(':kode', $kode);
        $statement->execute();
        $result = $statement->fetch();

        if ($result) {
            if (isset($_FILES['file_cover']['name'])) {
                $image_name = $_FILES['file_cover']['name'];
                $extension_file = ["jpg", "png", "jpeg"];
                $extension = pathinfo($image_name, PATHINFO_EXTENSION);

                if (in_array($extension, $extension_file)) {
                    $upload_path = 'upload/file_cover/' . $image_name;

                    if (move_uploaded_file($_FILES['file_cover']['tmp_name'], $upload_path)) {
                        $message = "berhasil";
                        $file_cover = "http://localhost/library/" . $upload_path;

                        $statement = $conn->prepare("UPDATE buku SET kode_kategori = :kode_kategori, judul = :judul, pengarang = :pengarang, penerbit = :penerbit, tahun = :tahun, tanggal_input = :tanggal_input, harga = :harga, file_cover = :file_cover WHERE kode = :kode");

                        $statement->bindParam(':kode', $kode);
                        $statement->bindParam(':kode_kategori', $kode_kategori);
                        $statement->bindParam(':judul', $judul);
                        $statement->bindParam(':pengarang', $pengarang);
                        $statement->bindParam(':penerbit', $penerbit);
                        $statement->bindParam(':tahun', $tahun);
                        $statement->bindParam(':tanggal_input', $tanggal_input);
                        $statement->bindParam(':harga', $harga);
                        $statement->bindParam(':file_cover', $file_cover);
                        $statement->execute();
                        
                        $response["message"] = "Data berhasil diubah!";
                    } else {
                        $message = "Terjadi kesalahan saat mengupload gambar";
                    }
                } else {
                    $message = "Hanya diperbolehkan mengupload file gambar!";
                    $response["message"] = $message;
                    $json = json_encode($response, JSON_PRETTY_PRINT);
                    echo $json;
                    die();
                }
            } else {
                $statement = $conn->prepare("UPDATE buku SET kode_kategori = :kode_kategori, judul = :judul, pengarang = :pengarang, penerbit = :penerbit, tahun = :tahun, tanggal_input = :tanggal_input, harga = :harga WHERE kode = :kode");

                $statement->bindParam(':kode', $kode);
                $statement->bindParam(':kode_kategori', $kode_kategori);
                $statement->bindParam(':judul', $judul);
                $statement->bindParam(':pengarang', $pengarang);
                $statement->bindParam(':penerbit', $penerbit);
                $statement->bindParam(':tahun', $tahun);
                $statement->bindParam(':tanggal_input', $tanggal_input);
                $statement->bindParam(':harga', $harga);
                $statement->execute();
                
                $response["message"] = "Data berhasil diubah!";
            }
        } else {
            $response["message"] = "Data tidak ditemukan!";
        }
    }
} catch (PDOException $e) {
    $response["message"] = "Error: " . $e->getMessage();
}

$json = json_encode($response, JSON_PRETTY_PRINT);

// Print json
echo $json;

// Tutup koneksi
$conn = null;
?>
