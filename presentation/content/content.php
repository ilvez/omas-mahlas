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
<script>

var allElements = [];

function updateContent() {
    var elem = allElements[position(allElements)];
    var txt = " >> " + elem.action + " - " + elem.light + " - " + elem.data;
    $("#content").text(txt);
}

// TODO: this must me moved to omas-mullis.js
$.getJSON(DATA_JSON, function(data) {
    allElements = data.elements;
    setInterval(updateContent, 1000);
});

</script>

<div id="content" />
