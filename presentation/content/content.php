<div id="content"></div>
<div id="content_gradient"><img src="/content/content_gradient.png"></div>

<ul id="activities">
    <li></li>
    <li></li>
    <li></li>
    <li></li>
</ul>

<style>
body {
    background: #000000;
}

#content {
    font-size: 100px;
    color: white;
    max-width: 1100px;
    margin-left: 200px;
    margin-top: 50px;
}
</style>
    <script src="/js/jquery.newsTicker.min.js"></script>
    <script src="/js/jquery.textfill.min.js"></script>
    <script src="/content/content.js"></script>
<script>

var allElements = [];
var contentElement = 1;

function updateContent() {
    var elem = allElements[position(allElements)];
    if (elem != contentElement) {
        contentElement = elem;
        show_activity(elem.light, elem.action, elem.data, "aahannagrete1.jpg", "140407_12.wav");
    }
}

function displayData() {
    $.getJSON(DATA_JSON, function(data) {
        allElements = data.elements;
        window.setTimeout(function() {
            setInterval(updateContent, timerContent);
        }, STARTUP_TIME);
    });
}

displayData();
</script>
