<style type="text/css">

body{
	background-color: #000000;
}
#speed{
	z-index: 10;
	position: absolute;
	right:10px;
	top:10px;
	background-color: #FFFFFF;

}
video {
  width: 100%    !important;
  height: auto   !important;
}

</style>

<div id="speed"></div>

<? if (!$_GET["video"]){

	$_GET["video"] = "anu-kuri_14-25-01_14-30-00_59.414554,24.718133_59.418062,24.720678";
}
?>
<video id="video" width="1919" height="1080" onended="next_video()">
  <source src="data/streetvideo/videos/<?=$_GET["video"]?>.mp4" type="video/mp4">
  Your browser does not support HTML5 video.
</video>




<script>



setTimeout(function () {


document.getElementById("video").play();
		speed = document.getElementById("video").playbackRate = 10;


},1000);

myVid=document.getElementById("video");

	for ( i = 0; i <= 3000; i++) {
 		
 		 doSetTimeout(i);

	}

function doSetTimeout(i) {
  setTimeout(function() { 

  		//document.getElementById("video").playbackRate=i*0.01;
		speed = document.getElementById("video").playbackRate;
		$("#speed").html(speed);


   }, i*10+10000);
}

</script> 