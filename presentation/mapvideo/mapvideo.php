<style type="text/css">
    #speed {
        z-index: 10;
        position: absolute;
        right:10px;
        top:10px;
        background-color: #FFFFFF;
    }
</style>

<div id="speed"></div>

<video id="mapvidjo" width="1919" height="1080">
    <source id="mapvidjo-src" src="" type="video/ogg">
</video>

<script>
var allElements = [];
var currentElem = 0;
var currentVideo = "";
var currentBegin = 0;
var currentEnd = 0;

function playNewVideo(elem) {
    currentVideo = elem.mapvideo;
    $("#mapvidjo-src").attr("src", currentVideo);
    $("#mapvidjo")[0].load();
    $("#mapvidjo")[0].play();
}

function updateVideo() {
    var curPos = position(allElements);
    var elem = allElements[curPos];

    if (elem.mapvideo != null 
            && elem.mapvideo != currentVideo) {
        playNewVideo(elem);
        currentBegin = elem.mapvideo_begin;
        currentEnd = elem.mapvideo_end;
    }

    if (elem != currentElem) {
        // TODO
    }
}

$.getJSON(DATA_JSON, function(data) {
    allElements = data.elements;
    setInterval(updateVideo, timerMap);
});


/** ONLY FOR SPEED EXAMPLE

function doSetTimeout(i) {
    setTimeout(function() { 
        //document.getElementById("video").playbackRate=i*0.01;
        speed = document.getElementById("video").playbackRate;
        $("#speed").html(speed);
   }, i*10+10000);
}
 */

</script> 
