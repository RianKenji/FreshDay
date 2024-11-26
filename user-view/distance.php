<?php
// API Key OpenCage Geocoder Anda
$apiKey = 'eb728b6c86d84efe81359b6372d3a254'; // Ganti dengan API Key Anda

// Alamat asal dan tujuan
$alamatAsal = 'Setia Warga 7 no 22A, Jelambar Baru, Grogol Petamburan, Jakarta Barat, DKI Jakarta, Indonesia';
$alamatTujuan = 'Citra Garden 7, Jakarta , Indonesia';

// URL untuk mengakses API OpenCage
function getCoordinates($alamat, $apiKey) {
    $alamat = urlencode($alamat);
    $url = "https://api.opencagedata.com/geocode/v1/json?q=$alamat&key=$apiKey";
    
    // Menangani kesalahan saat mendapatkan respons
    $response = @file_get_contents($url);
    
    // Memeriksa apakah file_get_contents berhasil
    if ($response === FALSE) {
        echo "Gagal mengakses API atau batas penggunaan tercapai.\n";
        return null;
    }

    $data = json_decode($response, true);

    if (isset($data['status']['code']) && $data['status']['code'] == 200 && !empty($data['results'])) {
        return $data['results'][0]['geometry']; // Mengembalikan koordinat
    } else {
        echo "Gagal mendapatkan koordinat untuk alamat: $alamat\n";
        return null;
    }
}

// Menghitung jarak antara dua titik menggunakan rumus Haversine
function calculateDistance($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 6371; // Radius bumi dalam kilometer

    // Menghitung perbedaan koordinat
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);

    // Menghitung jarak menggunakan rumus Haversine
    $a = sin($dLat / 2) * sin($dLat / 2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
         sin($dLon / 2) * sin($dLon / 2);

    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    $distance = $earthRadius * $c; // Jarak dalam kilometer

    return $distance;
}

// Mendapatkan koordinat dari kedua alamat
$coordinatesAsal = getCoordinates($alamatAsal, $apiKey);
$coordinatesTujuan = getCoordinates($alamatTujuan, $apiKey);

if ($coordinatesAsal && $coordinatesTujuan) {
    // Menghitung jarak jika kedua koordinat berhasil ditemukan
    $lat1 = $coordinatesAsal['lat'];
    $lon1 = $coordinatesAsal['lng'];
    $lat2 = $coordinatesTujuan['lat'];
    $lon2 = $coordinatesTujuan['lng'];

    $jarak = calculateDistance($lat1, $lon1, $lat2, $lon2);
    echo "Jarak antara '$alamatAsal' dan '$alamatTujuan' adalah: " . round($jarak, 2) . " km\n";
} else {
    echo "Gagal mendapatkan koordinat untuk salah satu atau kedua alamat.\n";
}
?>
