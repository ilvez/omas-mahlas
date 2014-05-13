<?

$conf["path"] =  "/Users/niisamalinnas/omas-mahlas/data/";

$conf["input_images_dir"] = "subjects/";
$conf["morph_images_dir"] = "streetvideo/morphs/";
$conf["output_video_dir"] = "streetvideo/videos/";

date_default_timezone_set('Europe/Tallinn');


if ($handle = opendir($conf["path"].$conf["input_images_dir"])) {
    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != ".." && $entry != "" && $entry != ".DS_Store") {
    		print "<hr/><b>OPEN DIR:".$entry."</b>";
	print "<br/>";

          // remove_duplicates($entry);
          //morph($entry);
          video($entry);
            //exit();
        }
    }
    closedir($handle);
}

function video($dir){


	global $conf;

	//anu-kuri_07-30-01_07-50-00_59.402774,24.818489_59.418062,24.720678
	$name = explode("_",$dir);
	$time_start = str_replace("-",":",$name[1]);
	$time_end = str_replace("-",":",$name[2]);

	print "DURATION OF TRACK: ";
	print $duration_sec = strtotime($time_end. ' 01.04.2014') - strtotime($time_start. ' 01.04.2014');
	print "<br/>";

	$cmd = '/usr/local/bin/ffmpeg -pattern_type glob -i "'.$conf["path"].$conf["morph_images_dir"].$dir.'/*.jpg"  -c:v libx264 -pix_fmt yuv420p "'.$conf["path"].$conf["output_video_dir"].$dir.'.mp4"';
	print $cmd;
	system($cmd);
	
	print "<br/>";
	print "<br/>";


} // function

function morph($dir){
	global $conf;

	print "MORPH FILE:".$dir."";
	print "<br/>";
	if (!file_exists($conf["path"].$conf["morph_images_dir"].$dir.'/')){
		mkdir($conf["path"].$conf["morph_images_dir"].$dir.'/');
	}
	$cmd = '/Applications/MAMP/Library/bin/convert "'.$conf["path"].$conf["input_images_dir"].$dir.'/*.jpg" -flop -delay 5 -morph 5 "'.$conf["path"].$conf["morph_images_dir"].$dir.'/%05d.morph.jpg" ';
	system($cmd);
	print $cmd;
	print "<br/>";
	print "<br/>";

}

function remove_duplicates($dir){

	print "DUPLICATES DIR:".$dir."";
	print "<br/>";

	global $conf;
	$hashes[] = "";

	if ($handle = opendir($conf["path"].$conf["input_images_dir"].$dir)) {
	    while (false !== ($entry = readdir($handle))) {
	        if ($entry != "." && $entry != ".." && $entry != "" && $entry != ".DS_Store") {
	            	
	            	$img_hash = hash_file('md5', $conf["path"].$conf["input_images_dir"].$dir.'/'.$entry );
	            	$olemas = 0;

	            	if ($hashes){

	            	foreach ($hashes as $hash) {
	            		if($hash==$img_hash){
	            			$olemas = 1;
	            		}
	            	}

	            	}

	            	if ($olemas==1){
	            		print $cmd = "rm ".$conf["path"].$conf["input_images_dir"].$dir.'/'.$entry;
	            		//system($cmd);
					    print "<br/>";
						unset($hashes);
	            	} else {
	            		$hashes[] = $img_hash;
	            	}
	            	 

	        }
	    }
	    closedir($handle);
	}
	
	print "<br/>";

}