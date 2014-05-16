<style type="text/css">
    img.keset {
        display:        block;
        margin-left:    auto;
        margin-right:   auto;
        max-height:     900px;
    }
</style>
<script>
var allElements = [];

function updateScreenshot() {
    var elem = getCurrentElem(allElements);
    $("#kuvapauk").attr("src", elem.screenshot);
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

<img src="" id="kuvapauk" class="keset" />
