
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
    $("#mapvidjo-src").attr("src", elem.mapvideo);
    var player = $("#mapvidjo")[0];
    player.playbackRate = 0.0001;
    player.load();
    player.play();
    console.log("Playing: " + elem.mapvideo);
}

function setSpeed(speed) {
    if (isNaN(speed) == false) {
        $("#mapvidjo")[0].playbackRate = speed;
        currentSpeed = speed;
        console.log("Setting new speed: " + speed);
    }
}

function updateVideo() {
    var elem = getCurrentElem(allElements);

    if (elem.mapvideo != null 
            && elem.mapvideo != currentElem.mapvideo) {
        playNewVideo(elem);
    }

    if (typeof elem.mapvideo_begin != 'undefined') {
        var newSpeed = getVideoSpeed(elem, elem.mapvideo_begin, elem.mapvideo_end, currentVideoLength, timerMap);
        if (newSpeed != currentSpeed) {
            setSpeed(newSpeed);
        }
    }

    if (elem != currentElem) {
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
