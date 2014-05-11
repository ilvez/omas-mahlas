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
    var curPos = position(allElements);
    var elem = allElements[curPos];
    $("#kuvapauk").attr("src", elem.screenshot);
}

// TODO: this must me moved to omas-mullis.js
$.getJSON(DATA_JSON, function(data) {
    allElements = data.elements;
    setInterval(updateScreenshot, timerShot);
});
</script>

<img src="" id="kuvapauk" class="keset" />
