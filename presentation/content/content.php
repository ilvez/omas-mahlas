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

function updateContent() {
    var elem = allElements[position(allElements)];
    // var txt = " >> " + elem.action + " - " + elem.light + " - " + elem.data;
    // $("#content").text(txt);
    show_activity(elem.light, elem.action, elem.data, "aahannagrete1.jpg", "140407_12.wav");
}

// TODO: this must me moved to omas-mullis.js
$.getJSON(DATA_JSON, function(data) {
    allElements = data.elements;
    setInterval(updateContent, timerContent);
});

</script>

<div id="content"></div>
<div id="content_gradient"><img src="/content/content_gradient.png"></div>

<ul id="activities">
    <li></li>
    <li></li>
    <li></li>
    <li></li>
</ul>
