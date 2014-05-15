<style type="text/css">
body{
    overflow: hidden;
}
    #notification {
        position:   fixed;
        z-index:    2;
        top:        50%;
        left:       50%;
        margin:     -20px 0 0 -20px;
        width:      40px;
        height:     40px;
    }
</style>
<div id="notification">
    <img id="lamp_icon" src="/data/images/lamp_icons/telefon.svg" width="40" alt="">
</div>

<video id="mapvidjo" width="1919" height="1080">
    <source id="mapvidjo-src" src="" type="video/ogg">
</video>

<script>
var allElements = [];
var currentElem = 0;
var currentVideo = "";
var currentBegin = 0;
var currentEnd = 0;
var currentVideoLength = 0;
var currentSpeed = -1;

function animate_lamp_icon(lamp){

    $("#lamp_icon").attr("src", "/data/images/lamp_icons/"+lamp+".svg");
    $("#lamp_icon").fadeIn(100).delay(2000).fadeOut(500);;

}

function playNewVideo(elem) {
    currentVideo = elem.mapvideo;
    $("#mapvidjo-src").attr("src", currentVideo);
    var player = $("#mapvidjo")[0];
    player.load();
    player.play();
    console.log("Playing: " + currentVideo);
}

function getSpeed() {
    var speed = -1;
    if (currentEnd != 0 && currentBegin != 0 && currentVideoLength != 0) {
        var ownTimeDuration = currentEnd - currentBegin;
        var ownTimeVideoSpeed = ownTimeDuration / currentVideoLength;
        var step = calculateSpeedup(allElements, timerMap);
        speed = step * ownTimeVideoSpeed;
        console.log("currentVideoLength: " + currentVideoLength);
        console.log("ownTimeDuration: " + ownTimeDuration);
        console.log("ownTimeVideoSpeed: " + ownTimeVideoSpeed);
        console.log("step: " + step);
        console.log("speed: " + speed);
    }
    // TODO ROUND
    return speed;
}

function setSpeed(speed) {
    $("#mapvidjo")[0].playbackRate = speed;
    currentSpeed = speed;
    console.log("Setting new speed: " + speed);
}

function updateVideo() {
    var curPos = position(allElements);
    var elem = allElements[curPos];

    if (elem.mapvideo != null 
            && elem.mapvideo != currentVideo) {
        currentBegin = elem.mapvideo_begin;
        currentEnd = elem.mapvideo_end;
        playNewVideo(elem);
    }
    var newSpeed = getSpeed();
    if (newSpeed != currentSpeed) {
        setSpeed(newSpeed);
    }

    if (elem != currentElem) {
        // TODO
            animate_lamp_icon(elem.light);
            currentElem  = elem;
    }
}

$.getJSON(DATA_JSON, function(data) {
    allElements = data.elements;
    var player = $("#mapvidjo")[0];
    player.addEventListener('loadedmetadata', function() {
        console.log("Length: " + player.duration);
        currentVideoLength = player.duration;
    });
    window.setTimeout(function() {
        setInterval(updateVideo, timerMap);
    }, STARTUP_TIME);
});
</script>
