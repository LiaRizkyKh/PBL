<!DOCTYPE html>
<html>
<body>
<?php
function fungsi1() {
  $x = 5; // local scope
  echo "<p>Variable x  di dlm fungsi: $x</p>";
} 
fungsi1();
// menggunakan  variabel x di luar fungsi  akan mengakibatkan error
echo "<p>Variable x di luar fungsi : $x</p>";
?>
</body>
</html>
