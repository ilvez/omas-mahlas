<!DOCTYPE html>
<html>
<head>
    <? include("config.php"); ?>
    <title>OMAS_MULLIS_<?=$_GET["node"]?>_<?=$conf[$_GET["node"]]["title"];?></title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="<?=strtolower($conf[$_GET["node"]]["title"].'/'.$conf[$_GET["node"]]["title"].'.css')?>">

    <script src="js/jquery-2.1.1.min.js"></script>
    <script src="js/date.js"></script>
    <script src="js/purl.js"></script>
    <script src="js/omas-mullis.js"></script>
</head>

<?
include(strtolower($conf[$_GET["node"]]["title"].'/'.$conf[$_GET["node"]]["title"].'.php'));
?>
</body>
</html>
