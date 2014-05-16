
<script>
var allElements = [];

function updateScreenshot() {
    var elem = getCurrentElem(allElements);
    $("#kuvapauk").attr("src", elem.screenshot);
    display_status(elem.name);

}

function display_status(name){
    $("#status").html(name + " / SEADE");
}

// TODO: this must me moved to omas-mullis.js
$.getJSON(DATA_JSON, function(data) {
    allElements = data.elements;
    setFullStoryTime(data.fullStoryTime);
    window.setTimeout(function() {
        setInterval(updateScreenshot, timerShot);
    }, STARTUP_TIME);
});
</script>

<div id="status">SEADE</div>
<img src="" id="kuvapauk" class="keset" />