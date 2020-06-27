<?php

$month = date('m');
$conn = $pdo->open();

try {
    $inc = 4;
    $stmt = $conn->prepare("SELECT *, SUM(quantity) AS total_qty FROM details LEFT JOIN sales ON sales.id=details.sales_id LEFT JOIN products ON products.id=details.product_id WHERE MONTH(sales_date) = '$month' GROUP BY details.product_id ORDER BY total_qty DESC LIMIT 8");
    $stmt->execute();
    foreach ($stmt as $row) {
        $image = (!empty($row['photo'])) ? 'images/' . $row['photo'] : 'images/noimage.jpg';
        $inc = ($inc == 4) ? 1 : $inc + 1;
        if ($inc == 1)
            echo "<div class='row'>";
        echo "
	       							<div class='col-sm-3'>
	       								<div class='box box-solid'>
		       								<div class='box-body prod-body'>
		       									<img src='" . $image . "' width='100%' height='230px' class='thumbnail'>
		       									<h5><a href='product?product=" . $row['slug'] . "'>" . $row['name'] . "</a></h5>
		       								</div>
		       								<div class='box-footer'>
		       									<b>&#36; " . number_format($row['price'], 2) . "</b>
		       								</div>
	       								</div>
	       							</div>
	       						";
        if ($inc == 4)
            echo "</div>";
    }
    if ($inc == 1)
        echo "<div class='col-sm-4'></div><div class='col-sm-4'></div></div>";
    if ($inc == 2)
        echo "<div class='col-sm-4'></div></div>";
} catch (PDOException $e) {
    echo "Existen problemas en la conexion: " . $e->getMessage();
}

$pdo->close();
?> 