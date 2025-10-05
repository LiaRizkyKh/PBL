<!DOCTYPE html>
<html>

<body>
    <?php
    $negaraAsia = [
        "Indonesia" => "Jakarta",
        "India" => "NewDelhi",
        "Jepang" => "Tokyo",
        "Cina" => "Beijing",
        "Malaysia" => "KualaLumpur",
        "Filipina" => "Manila",
        "KoreaUtara" => "Pyongyang", 
        "KoreaSelatan" => "Seoul",
        "Iran" => "Teheran",
        "Irak" => "Bahgdad",
        "Vietnam" => "Hanoi", 
        "Thailand" => "Bangkok",
    ];

    $i = 1;
    foreach ($negaraAsia as $negara => $ibukota) {
        echo $i . ". " . $negara . " ibukotanya " . $ibukota . "</br>";
        $i++;
    }
    ?>
</body>

</html>