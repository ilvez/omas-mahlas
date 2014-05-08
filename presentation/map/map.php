<style type="text/css">

#speed{
	z-index: 10;
	position: absolute;
	right:10px;
	top:10px;
	background-color: #FFFFFF;

}

</style>

<div id="speed"></div>

<video id="video" width="1919" height="1080" loop>
  <source src="data/map/test.ogg" type="video/ogg">
  Your browser does not support HTML5 video.
</video>




<script>
setTimeout(function () {

document.getElementById("video").play();

},10000)

myVid=document.getElementById("video");

	for ( i = 0; i <= 3000; i++) {
 		
 		 doSetTimeout(i);

	}

function doSetTimeout(i) {
  setTimeout(function() { 

  		document.getElementById("video").playbackRate=i*0.01;
		speed = document.getElementById("video").playbackRate;
		$("#speed").html(speed);


   }, i*10+10000);
}

</script> 