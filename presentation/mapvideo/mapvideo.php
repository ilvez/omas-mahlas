
<div id="notification">
    <img id="lamp_icon" src="/data/images/lamp_icons/telefon.svg" width="40" height="40" alt=""/>
</div>

<div id="status">ASUKOHT</div>

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

    $("#lamp_icon").fadeOut(1);
    $("#lamp_icon").attr("src", "/data/images/lamp_icons/"+lamp+".svg");
    $("#lamp_icon").delay(2).fadeIn(50).delay(1000).fadeOut(300);;

}

function display_status(name){
    $("#status").html(name + " / ASUKOHT");
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

          display_status(elem.name);
            currentElem  = elem;
    }
}

$.getJSON(DATA_JSON, function(data) {
    allElements = data.elements;
    setFullStoryTime(data.fullStoryTime);
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
