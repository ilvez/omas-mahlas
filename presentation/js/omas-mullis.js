var TIME_PER_SLIDE = 3;
var allElements = [];

// Returns timestamp of current day start 00.00
function startOfDay() {
    var now = new Date();
    var start = new Date(now.getFullYear(), now.getMonth(), now.getDate());
    return Math.round(start / 1000);
}

function currentTime() {
    return Math.round($.now() / 1000);
}

function fullStoryTime(elements) {
    return elements.length * TIME_PER_SLIDE;
}

// Function takes all story elements and using system time
// and returns current element
function position(elements) {
    var secondsToNow = currentTime() - startOfDay();
    var currentPos = secondsToNow % fullStoryTime(elements);
    var curPos = Math.round(currentPos / TIME_PER_SLIDE);
    console.log("Current position: " + curPos);
    return curPos;
}
