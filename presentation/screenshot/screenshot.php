<style type="text/css">
</style>
<script>

var TIME_PER_SLIDE = 3;
var allElements = [];
function imgPath(elem) {
    return "/data/screenshots/" + elem.id + "/" + elem.screenshot;
};

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
    var storyTime = fullStoryTime(elements);
    var currentPos = secondsToNow % storyTime;
    return Math.round(currentPos / TIME_PER_SLIDE);
}

function updateScreenshot() {
    var curPos = position(allElements);
    console.log(curPos);
    $("#kuvapauk").attr("src", imgPath(allElements[curPos]));
}

$.getJSON("data/omas-mullis.json", function(data) {
    allElements = data.elements;
    setInterval(updateScreenshot, 1000);
});
</script>

<img src="" id="kuvapauk" />
