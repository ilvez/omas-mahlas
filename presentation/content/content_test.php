<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<style type="text/css">
	body{
		background: #000000;

	}
	</style>
</head>
<body>
<?

$conf["path"] = "/Users/niisamalinnas/omas-mahlas/";

$conf["content_images"] =  "data/content";

if (($handle = fopen("/Users/niisamalinnas/omas-mahlas/presentation/images/action_icons/action_icons_mapping.csv", "r")) !== FALSE) {
	    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

	 	$action_mapping[$data[0]] = $data[1];
	 }
	}


	if (($handle = fopen("/Users/niisamalinnas/omas-mahlas/data/data-compiler-test/omas-mullis-input.csv", "r")) !== FALSE) {
	    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

	    	$data[4] = trim($data[4]);

	    	$data[3] = trim($data[3]);
			$data[3] = str_replace("kõne","telefon",$data[3]);
			$data[3] = str_replace("peatus.ee","www",$data[3]);
			$data[3] = str_replace("ä","a",$data[3]);
			$data[3] = str_replace(" ","",$data[3]);


			$data[4] = str_replace("sõnum sisse","sõnum välja",$data[4]);

			//$actions[] = $data[4];

	    	 print $data[3];

	    	 // 4+5
	    	 ?><img src="../images/lamp_icons/<?=$data[3]?>.svg" alt="" width="50" />
				<img src="../images/action_icons/<?=$action_mapping[$data[4]]?>.svg" alt="" width="50" />
	    	 <?
	    	 print "<br/>";
	    	 

	    }

	}

$actions = array_unique($actions);

foreach ($actions as $key => $value) {
	print $value."<br/>";
}


?>


sonum_sisse - explode('/','sõnum sisse/Saabuv sõnum/uus teade/teade/teated');
sonum_valja - Saadan sõnumi / sõnum välja / Uus säuts / uus postitus / säutsun / postitan / uus säuts
kone_sisse - kõne sisse
kone_valja - kõne välja
otsing - otsin
download - allalaadimine
pilt - uus pilt / pildistan / vaatan / Vaatan / Vaatan infot
kuulan - kuulan / Kuulan





















