<style>


@font-face
{
    font-family: DigitalDismay;
    src: url('/data/fonts/Digital Dismay.otf');
}

@font-face
{
    font-family: LihulaBold;
    src: url('/data/fonts/LihulaBold_v12.otf');
}



body {
    background:     #000000;
}

#time {
    margin-top:  200px;
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
     font-family: LihulaBold;
}
</style>

<script>

var allElements = [];
var originalTime = 0;
var currentTimestamp = 0;
var timeStep = 0;

function updateHtml(elem, storyDate) {
    var txt =  "<div id='time'>" + storyDate.toString("HH:mm:ss") + "</div>\n<div id='name'>" + elem.name.toUpperCase() + ", 17a<br/>kolmapaev, "+ storyDate.toString("dd.MM.yyyy") +"</div>";
    $("#clock").html(txt);
}

// Currently quite expensive, triggered too many times & updates nothing
function updateClock() {
    var curPos = position(allElements);
    var elem = getCurrentElem(allElements);

    // If we have new element lets calulate new step
    if (originalTime != elem.time) {
        originalTime = elem.time;
        currentTimestamp = originalTime;
        timeStep = calculateStep(originalTime, getNextTime(elem, getNextElem(allElements)), timerClock);
    } else {
        currentTimestamp = currentTimestamp + timeStep;
    };
    updateHtml(elem, timestampToDate(currentTimestamp));
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
