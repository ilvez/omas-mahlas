var DATA_JSON = "data/omas-mullis.json";
//var TIME_PER_SLIDE = 5;

var STARTUP_TIME = 00000;
//var STARTUP_TIME = 10000;

var timerGlobal = 100;
var timerShot = timerGlobal;
var timerMap = 1000;
var timerClock = 30;
var timerContent = timerGlobal;
var timerStreet = 1000;
var fts = 0;
var inputPosition = null;

// Returns timestamp of current day start 00.00
function startOfDay() {
    var d = new Date();
    if (inputPosition != null) {
        d = timestampToDate(inputPositon);
    }
    return startOfDayTimestamp(d);
}

function startOfDayTimestamp(now) {
    var start = new Date(now.getFullYear(), now.getMonth(), now.getDate());
    return dateToTimestamp(start);
}

function midnight(ts) {
    var d = timestampToDate(ts);
    d.setHours(23,59,59,999);
    return dateToTimestamp(d);
}

function currentTime() {
    return Math.round($.now() / 1000);
}

//function fullStoryTime(elements) {
//    return elements.length * TIME_PER_SLIDE;
//}

function setFullStoryTime(time) {
    fts = time;
}

function getFullStoryTime() {
    return fts;
}

// Function takes all story elements and using system time
// returns current element
function position(elements) {
    var secondsToNow = currentTime() - startOfDay();
    var currentStoryTime = secondsToNow % getFullStoryTime();
    var searchTime = 0;
    var curPos = 0;
    //console.log("Current story time: " + currentStoryTime);
    // TODO: GETS MORE EXPENSIVE TO CALCULATE WHEN STORY END
    for (i = 0; searchTime <= currentStoryTime ; i++) {
        searchTime += elements[i].time_for_slide;
        curPos = i;
    }
    console.log("Calculated position: " + curPos);
    return curPos;
}

function nextPosition(elements) {
    var curPos = position(elements);
    var nextPos = curPos + 1;
    if (nextPos >= elements.length) {
        nextPos = 0;
    }
    return nextPos;
}

function getCurrentElem(elements) {
    return elements[position(elements)];
}

function getNextElem(elements) {
    return elements[nextPosition(elements)];
}

function getNextTime(curElem, nextElem) {
    // If next element is from new character
    // then lets count down to midnight
    var nextTime;
    if (nextElem.id != curElem.id) {
        nextTime = midnight(currentTimestamp);
    } else {
        nextTime = nextElem.time;
    }
    return nextTime;
}

function calculateStep(currentElem, nextElem, timer) {
    var next = getNextTime(currentElem, nextElem);
    var current = currentElem.time;
    console.log("next: " + next);
    console.log("current: " + current);
    return (next - current) / (nextElem.time_for_slide * (1000 / timer));
}

// Calculates how fast we have to move in own_time to reach to next
// element in TIME_PER_SLIDE
function calculateSpeedup(elements, timer) {
    var currentElem = getCurrentElem(elements);
    var nextElem = getNextElem(elements);
    console.log("calculateSpeedup: currentElem: " + currentElem);
    console.log("calculateSpeedup: nextElem: " + nextElem);
    return calculateStep(currentElem.time, nextElem.time, timer);
}

function timestampToDate(ts) {
    return new Date(ts * 1000);
}

function dateToTimestamp(d) {
    return Math.round(d / 1000);
}
