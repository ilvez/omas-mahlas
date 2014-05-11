<style>
@import url(http://fonts.googleapis.com/css?family=Droid+Sans);

@font-face {
    font-family:    'Droid Sans';
}

body {
    background:     #000000;
}

#clock {
    margin-top:     100px;
    font-size:      200px;
    color:          yellow;
    text-align:     center;
    font-family:    pixel_supermarket;
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
 var txt = storyDate.toString("yyyy-MM-dd") + "\n" +
        storyDate.toString("HH:mm:ss") +"\n" + elem.name.toUpperCase();
    var obj = $("#clock").text(txt);
    obj.html(obj.html().replace(/\n/g,'<br/>'));

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
    setInterval(updateClock, timerClock);
});

</script>

<div id="clock" />
