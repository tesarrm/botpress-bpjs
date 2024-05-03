<?php
include "../utils/header.php"; // $conn, $method, #result

switch ($method) {
    case 'GET':

        $nik = $_GET['nik'];
        $ttl = $_GET['ttl'];

        $query1 = "SELECT * FROM peserta WHERE nik = $nik AND ttl = '$ttl'";
        $result1 = mysqli_query($conn, $query1);

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

                echo json_encode($anggota_keluarga);
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
