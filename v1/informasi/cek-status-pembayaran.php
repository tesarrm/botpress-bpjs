<?php
include "../utils/header.php"; // $conn, $method, #result

switch ($method) {
    case 'GET':

        $nik = $_GET['nik'];
        $ttl = $_GET['ttl'];

        $query1 = "SELECT * FROM peserta WHERE nik = $nik AND ttl = '$ttl'";
        $result1 = mysqli_query($conn, $query1);

        $pembayaran = array();
        $query9 = "SELECT * FROM pembayaran";
        $result9 = mysqli_query($conn, $query9);
        while ($row = mysqli_fetch_assoc($result9)) {
            $pembayaran[] = $row;
        }


        if ($result1 && mysqli_num_rows($result1) > 0) {
            $peserta = mysqli_fetch_assoc($result1);
            $nik_kepala_keluarga = $peserta['nik_kepala_keluarga'];


            $query2 = "SELECT * FROM peserta WHERE nik_kepala_keluarga = $nik_kepala_keluarga";
            $result2 = mysqli_query($conn, $query2);

            if ($result2 && mysqli_num_rows($result1) > 0) {
                $anggota_keluarga = array();
                while ($row = mysqli_fetch_assoc($result2)) {
                    $anggota_keluarga[] = $row;
                }


                // Array untuk menampung hasil penggabungan data
                $hasil_gabung = [];

                // Loop melalui setiap anggota keluarga
                foreach ($anggota_keluarga as $anggota) {
                    // Inisialisasi array untuk data anggota keluarga
                    $data_anggota = [
                        'nama_peserta' => $anggota['nama_peserta'],
                        'tagihan' => null,
                        'status_pembayaran' => null
                    ];

                    // Loop melalui setiap pembayaran untuk mencocokkan NIK kepala keluarga
                    foreach ($pembayaran as $bayar) {
                        if ($bayar['nik_kepala_keluarga'] === $anggota['nik_kepala_keluarga']) {
                            $data_anggota['tagihan'] = $bayar['tagihan'];
                            $data_anggota['status_pembayaran'] = $bayar['status_pembayaran'];
                            // Jika sudah ditemukan, keluar dari loop
                            break;
                        }
                    }

                    // Tambahkan data anggota ke hasil
                    $hasil[] = $data_anggota;
                }
                echo json_encode($hasil);
            } else {
                http_response_code(500);
                echo json_encode(array('error' => 'Data tidak ditemukan'));
            }
        } else {
            http_response_code(500);
            echo json_encode(array('error' => 'Data tidak ditemukan'));
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array('error' => 'Method not allowed'));
        break;
}

// Close the connection
mysqli_close($conn);
