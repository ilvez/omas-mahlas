<style>

@font-face {
  font-family: 'pixel_supermarket';
  src: url('data/fonts/SUPERMAR_SQUARE.TTF') format('truetype');
}

body{
	background: #000000;

}

#clock{
	margin-top:450px;
	color: yellow;
	text-align: center;
	font-family: pixel_supermarket;
	font-size: 400px;

}

</style>
<div id="clock">
<?

print  date('G:H:s');

?>
</div>
<script>
//location.reload();
</script>