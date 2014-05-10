<html>
<head>
	<? include("config.php"); ?>
	<title>OMAS_MULLIS_<?=$_GET["node"]?>_<?=$conf[$_GET["node"]]["title"];?></title>
	<link rel="stylesheet" href="css/main.css">
	<link rel="stylesheet" href="css/splash.css">
	<script src="js/jquery-2.1.1.min.js"></script>
	<script src="js/splash.js"></script>
</head>

<div id="splash">
	<h1>E<?=$_GET["node"]?></h1>
	<h3><?=$conf[$_GET["node"]]["title"];?></h3>
</div>

<?

include(strtolower($conf[$_GET["node"]]["title"].'/'.$conf[$_GET["node"]]["title"].'.php'));

?>
</body>
</html>
