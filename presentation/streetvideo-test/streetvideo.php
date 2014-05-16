<!-- OLULINE -->
<div id="status">VAADE</div>
<div id="streeviewlogo"><img src="/data/images/streetview_icon.svg" width="100"/></div>
<!-- OLULINE -->

<video id="streetvidjo" width="1919" height="1080">
    <source id="streetvidjo-src" src="" type="video/mp4">
</video>

<script>
var allElements = [];
var currentElem = 0;
var currentVideo = "";
var currentBegin = 0;
var currentEnd = 0;
var currentVideoLength = 0;
var currentSpeed = -1;


function playNewVideo(elem) {
    currentVideo = elem.streetvideo;
    $("#streetvidjo-src").attr("src", currentVideo);
    var player = $("#streetvidjo")[0];
    player.load();
    player.play();
    console.log("Playing: " + currentVideo);
}

function getSpeed() {
    var speed = -1;
    if (currentEnd != 0 && currentBegin != 0 && currentVideoLength != 0) {
        var ownTimeDuration = currentEnd - currentBegin;
        var ownTimeVideoSpeed = ownTimeDuration / currentVideoLength;
        var step = calculateSpeedup(allElements, timerStreet);
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
    $("#streetvidjo")[0].playbackRate = speed;
    currentSpeed = speed;
    console.log("Setting new speed: " + speed);
}

function updateVideo() {
    var curPos = position(allElements);
    var elem = allElements[curPos];

    if (elem.streetvideo != null 
            && elem.streetvideo != currentVideo) {
        currentBegin = elem.streetvideo_begin;
        currentEnd = elem.streetvideo_end;
        playNewVideo(elem);
    }
    var newSpeed = getSpeed();
    if (newSpeed != currentSpeed) {
        setSpeed(newSpeed);
    }

    if (elem != currentElem) {
        display_status(elem.name);
        currentElem  = elem;
    }
}

$.getJSON(DATA_JSON, function(data) {
    allElements = data.elements;
    setFullStoryTime(data.fullStoryTime);
    var player = $("#streetvidjo")[0];
    player.addEventListener('loadedmetadata', function() {
        console.log("Length: " + player.duration);
        currentVideoLength = player.duration;
    });
    window.setTimeout(function() {
        setInterval(updateVideo, timerStreet);
    }, STARTUP_TIME);
});

////////////////////////////////////////////////////////
// OLULINE: 

function display_status(name){
    $("#status").html(name + " / VAADE");
}
////////////////////////////////////////////////////////

</script> 
