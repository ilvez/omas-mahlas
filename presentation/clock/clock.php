<style>


@font-face
{
    font-family: DigitalDismay;
    src: url('/data/fonts/Digital Dismay.otf');
}


body {
    background:     #000000;
}

#time {
    margin-top:  100px;
    font-size:   400px;
    line-height: 300px;
    color:       yellow;
    text-align:  center;
    font-family: DigitalDismay;
}

#name{

    font-family: "helvetica neue", helvetica;
    font-weight: 300;
    color: #999999;
    text-align: center;
    font-size: 100px;
    margin-top: 150px;

}
</style>

<script>

var allElements = [];
var originalTime = 0;
var currentTimestamp = 0;
var timeStep = 0;

function calculateStep(current, next) {
    return (next - current) / (TIME_PER_SLIDE * (1000 / timerClock));
}

function updateHtml(elem, storyDate) {
    var txt =  "<div id='time'>" + storyDate.toString("HH:mm:ss") + "</div>\n<div id='name'>" + elem.name.toUpperCase() + "<br/>"+ storyDate.toString("dd.MM.yyyy") +"</div>";
    $("#clock").html(txt);
}

function getNextTime(elem, nextPos) {
    // If next element is from new character
    // then lets count down to midnight
    var nextTime;
    if (nextPos >= allElements.length
            || allElements[nextPos].id != elem.id) {
        nextTime = midnight(currentTimestamp);
    } else {
        nextTime = allElements[nextPos].time;
    }
    return nextTime;
}

// Currently quite expensive, triggered too many times & updates nothing
function updateClock() {
    var curPos = position(allElements);
    var elem = allElements[curPos];

    // If we have new element lets calulate new step
    if (originalTime != elem.time) {
        originalTime = elem.time;
        currentTimestamp = originalTime;
        timeStep = calculateStep(originalTime, getNextTime(elem, curPos + 1));
    } else {
        currentTimestamp = currentTimestamp + timeStep;
    };
    updateHtml(elem, new Date(currentTimestamp * 1000));
}

// TODO: this must me moved to omas-mullis.js
$.getJSON(DATA_JSON, function(data) {
    allElements = data.elements;
    window.setTimeout(function() {
        setInterval(updateClock, timerClock);
    }, STARTUP_TIME);
});

</script>

<div id="clock" />
