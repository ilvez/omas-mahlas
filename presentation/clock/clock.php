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
    $("#clock").text(elem.time);
}

// TODO: this must me moved to omas-mullis.js
$.getJSON(DATA_JSON, function(data) {
    allElements = data.elements;
    setInterval(updateClock, 1000);
});

</script>

<div id="clock" />
