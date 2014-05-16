
<!-- OLULINE -->
<div id="status">VAADE</div>
<div id="streeviewlogo"><img src="/data/images/streetview_icon.svg" width="100"/></div>
<!-- OLULINE -->



<? if (!$_GET["video"]){

	$_GET["video"] = "anu-kuri_14-25-01_14-30-00_59.414554,24.718133_59.418062,24.720678";
}
?>
<video id="video" width="1919" height="1080" onended="next_video()" loop>
  <source src="data/streetvideo/videos/<?=$_GET["video"]?>.mp4" type="video/mp4">
  Your browser does not support HTML5 video.
</video>




<script>

////////////////////////////////////////////////////////

// OLULINE: 

function display_status(name){
    $("#status").html(name + " / VAADE");
}

////////////////////////////////////////////////////////

function next_video(){
	
}

setTimeout(function () {


document.getElementById("video").play();
		speed = document.getElementById("video").playbackRate = 1;


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