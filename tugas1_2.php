<!DOCTYPE html>
<html>

<body>
    <?php
    $suhuUdara = [29, 35, 38, 31, 34, 36, 39, 33, 34, 40, 35, 32, 37, 34, 31, 36, 33, 39, 30, 33, 41];

    $avg = array_sum($suhuUdara) / count($suhuUdara);

    $unik = array_values(array_unique($suhuUdara));
    sort($unik);

    $low5 = array_slice($unik, 0, 5);
    $high5 = array_slice($unik, -5);


    echo "Rata-rata suhu adalah " . number_format($avg, 1, '.', '') . "</br>";
    echo "5 suhu paling rendah adalah " . implode(',', $low5) . "</br>";
    echo "5 suhu paling tinggi adalah " . implode(',', $high5) . "</br>";

    $sum = 0;
    $cnt = 0;
    foreach ($suhuUdara as $x) {
        $sum += $x;
        $cnt++;
    }
    $avg = $cnt > 0 ? ($sum / $cnt) : 0;

    $unik = [];
    foreach ($suhuUdara as $v) {
        $sudahAda = false;
        for ($i = 0; $i < count($unik); $i++) {
            if ($unik[$i] === $v) {
                $sudahAda = true;
                break;
            }
        }
        if (!$sudahAda) {
            $unik[] = $v;
        }
    }

    for ($i = 0; $i < count($unik) - 1; $i++) {
        $minIdx = $i;
        for ($j = $i + 1; $j < count($unik); $j++) {
            if ($unik[$j] < $unik[$minIdx]) {
                $minIdx = $j;
            }
        }
        if ($minIdx !== $i) {
            $tmp = $unik[$i];
            $unik[$i] = $unik[$minIdx];
            $unik[$minIdx] = $tmp;
        }
    }

    $low5 = [];
    $high5 = [];

    $ambilLow = 5;
    for ($i = 0; $i < count($unik) && $i < $ambilLow; $i++) {
        $low5[] = $unik[$i];
    }

    $ambilHigh = 5;
    $len = count($unik);
    $startHigh = $len - $ambilHigh;
    if ($startHigh < 0)
        $startHigh = 0;
    for ($i = $startHigh; $i < $len; $i++) {
        $high5[] = $unik[$i];
    }

    function gabungKoma($arr)
    {
        $s = "";
        for ($i = 0; $i < count($arr); $i++) {
            if ($i > 0)
                $s .= ",";
            $s .= $arr[$i];
        }
        return $s;
    }
    function formatSatuDesimal($angka)
    {
        return sprintf("%.1f", $angka);
    }

    echo "Rata-rata suhu adalah " . formatSatuDesimal($avg) . "</br>";
    echo "5 suhu paling rendah adalah " . gabungKoma($low5) . "</br>";
    echo "5 suhu paling tinggi adalah " . gabungKoma($high5) . "</br>";
    ?>
</body>

</html>