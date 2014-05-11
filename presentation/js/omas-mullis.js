var DATA_JSON = "data/omas-mullis.json";
var TIME_PER_SLIDE = 5;

var timerGlobal = 100;
var timerShot = timerGlobal;
var timerMap = timerGlobal;
var timerClock = 30;
var timerContent = timerGlobal;
var timerStreet = timerGlobal;

// Returns timestamp of current day start 00.00
function startOfDay() {
    return startOfDayTimestamp(new Date());
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

function dateToTimestamp(d) {
    return Math.round(d / 1000);
}

function timestampToDate(ts) {
    return new Date(ts * 1000);
}

function currentTime() {
    return Math.round($.now() / 1000);
}

function fullStoryTime(elements) {
    return elements.length * TIME_PER_SLIDE;
}

// Function takes all story elements and using system time
// returns current element
function position(elements) {
    var secondsToNow = currentTime() - startOfDay();
    var currentPos = secondsToNow % fullStoryTime(elements);
    var curPos = Math.round(currentPos / TIME_PER_SLIDE);
    console.log("Current position: " + curPos);
    return curPos;
}
