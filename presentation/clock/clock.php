<style>
@font-face {
    font-family: 'pixel_supermarket';
    src: url('data/fonts/SUPERMAR_SQUARE.TTF') format('truetype');
}

body {
    background: #000000;
}

#clock {
    margin-top:200px;
    color: yellow;
    text-align: center;
    font-family: pixel_supermarket;
    font-size: 200px;
}
</style>
<script>

var allElements = [];

function updateClock() {
    var elem = allElements[position(allElements)];
    var txt = elem.time + "\n" + elem.name.toUpperCase();
    var obj = $("#clock").text(txt);
    obj.html(obj.html().replace(/\n/g,'<br/>'));
}

// TODO: this must me moved to omas-mullis.js
$.getJSON(DATA_JSON, function(data) {
    allElements = data.elements;
    setInterval(updateClock, timerClock);
});

</script>

<div id="clock" />
