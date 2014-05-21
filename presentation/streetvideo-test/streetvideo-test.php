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
var currentVideoLength = 0;
var currentSpeed = -1;


function playNewVideo(elem) {
    $("#streetvidjo-src").attr("src", elem.streetvideo);
    var player = $("#streetvidjo")[0];
    player.playbackRate = 0.0001;
    player.load();
    player.play();
    console.log("Playing: " + elem.streetvideo);
}

function setSpeed(speed) {
    if (isNaN(speed) == false) {
        $("#streetvidjo")[0].playbackRate = speed;
        currentSpeed = speed;
        console.log("Setting new speed: " + speed);
    }
}

function updateVideo() {
    var elem = getCurrentElem(allElements);

    if (elem.streetvideo != null 
            && elem.streetvideo != currentElem.streetvideo) {
        playNewVideo(elem);
    }

    if (typeof elem.streetvideo_begin != 'undefined') {
        var newSpeed = getVideoSpeed(elem, elem.streetvideo_begin, elem.streetvideo_end, currentVideoLength, timerStreet);
        if (newSpeed != currentSpeed) {
            setSpeed(newSpeed);
        }
    }

    if (elem != currentElem) {
        display_status(elem.name);
        currentElem = elem;
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
