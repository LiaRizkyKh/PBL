<!DOCTYPE html>
<html lang="en">

<body>
    <?php
    $rate = [
        'usd' => 14367,
        'jpy' => 1192, 
        'cny' => 2262,
        'krw' => 11.87,
        'myr' => 3416,
        'sgd' => 10621,
        'gbp' => 19074,
        'eur' => 15891,
    ];

    function konversiKeRupiah($jumlah, $kode, $rateMap)
    {
        $kode = strtolower(trim($kode));
        if (!isset($rateMap[$kode])) {
            return "Rate untuk kode mata uang '$kode' tidak tersedia.";
        }

        $jumlah = (float) $jumlah;
        $kurs = (float) $rateMap[$kode];

        $hasil = round($jumlah * $kurs);

        return $jumlah . " " . $kode . " dikonversi menjadi Rp " . $hasil;
    }

    echo konversiKeRupiah(8, 'usd', $rate) . "</br>";
    echo konversiKeRupiah(7, 'jpy', $rate) . "</br>";
    
    echo konversiKeRupiah(100, 'cny', $rate) . "</br>";
    echo konversiKeRupiah(150, 'krw', $rate) . "</br>";

    echo konversiKeRupiah(100, 'myr', $rate) . "</br>";
    echo konversiKeRupiah(100, 'sgd', $rate) . "</br>";

    echo konversiKeRupiah(100, 'gbp', $rate) . "</br>";
    echo konversiKeRupiah(3.5, 'eur', $rate) . "</br>";
    ?>

</body>

</html>