<?php

$ch = curl_init('http://ip-api.com/json/' . $_SERVER['REMOTE_ADDR'] . '?lang=ru');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HEADER, false);
$res = curl_exec($ch);
curl_close($ch);
 
$res = json_decode($res, true);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Статистика</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<script src="script.js"></script>
	
	<script>
		var ip = '<?php echo $_SERVER['REMOTE_ADDR']; ?>'
		var city = '<?php echo $res['city']; ?>'
		var gadget = /mobile|iphone|ipad|ipod|android|blackberry|mini|windows\sce|palm/i.test(navigator.userAgent.toLowerCase()) ? 'Мобильное устройство' : 'ПК'
	</script>
	
</head>
<body>

<div class="stat">
  <canvas id="stat"></canvas>
</div>

<div class="cities">
  <canvas id="cities"></canvas>
</div>




	
	<div class="grafik">
		
	</div>
	
</body>
</html>