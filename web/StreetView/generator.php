<?php
require_once '_gpx_to_json.php';
require_once '_rrmdir.php';

// Parse GPX from GET into JSON
if (!isset($_GET['subject'])) {
    die('Puudu GET parameeter "subject"');
}
$gpxFile = '../../data/subjects/' . $_GET['subject'] . '.gpx';
if (!file_exists($gpxFile)) {
    die('Ei leia GPX faili: "' . $gpxFile . '"');
}
$gpxParsed = gpx_to_json($gpxFile);
// Test set of sliced data (only 2 coords)
//$gpxParsed = json_encode(array_slice(json_decode($gpxParsed), 0, 2));

$targetFolder = substr(realpath($gpxFile), 0, -4);
if (!isset($_GET['offset'])) {
    // Purge existing folder
    rrmdir($targetFolder); mkdir($targetFolder);
    $_GET['offset'] = 1;
}

?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
<title>Omas Mahlas StreetView</title>

<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false" type="text/javascript"></script>
<script src="ext/hyperlapse/examples/js/three.min.js" type="text/javascript"></script>
<script src="ext/hyperlapse/examples/js/GSVPano.js" type="text/javascript"></script>
<script src="ext/hyperlapse/src/Hyperlapse.js" type="text/javascript"></script>
<script src="ext/jquery/jquery-2.1.1.js" type="text/javascript"></script>
<script type="text/javascript">
    function initHyperlapse (offset)
    {
        function showHyperlapseFeedback (msg) {
            msg = offset + '/' + jsonCoords.length + ': ' + msg;
            $('#feedback').val(
                msg + "\n" +
                $('#feedback').val()
            );
        }

        function nextIteration () {
            window.location = 'http://timo.dev/StreetView/generator.php?subject=<?=$_GET['subject']?>&offset=' + (offset+1);
        }

        // Are we done with the recursion?
        if (offset > jsonCoords.length - 1) {
            showHyperlapseFeedback('All done, YAY');
            $('#feedback').val('Finished at ' + getCurrentTime() + "\n" + $('#feedback').val());
            return;
        }

        // Purge old canvases, if any
        $('#pano canvas').remove();

        var start_coords = jsonCoords[offset-1],
            end_coords   = jsonCoords[offset],
            start_point  = new google.maps.LatLng(start_coords.latitude, start_coords.longitude),
            end_point    = new google.maps.LatLng(end_coords.latitude,   end_coords.longitude),
            map,
            overlay,
            directions_renderer,
            directions_service,
            start_pin,
            end_pin,
            camera_pin,
            hyperlapse,
            _isLastFrame,
            _frameStepSec,
            _screenieEpoch
        ;

        /* Map */
        map = new google.maps.Map(document.getElementById('map'), {
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            center:    start_point,
            zoom:      15
        });

        overlay = new google.maps.StreetViewCoverageLayer();
        overlay.setMap(map);

        directions_service  = new google.maps.DirectionsService();
        directions_renderer = new google.maps.DirectionsRenderer({ draggable: false, markerOptions: { visible: false }});
        directions_renderer.setMap(map);
        directions_renderer.setOptions({ preserveViewport: true });

        camera_pin = new google.maps.Marker({ position: start_point, map: map });
        start_pin  = new google.maps.Marker({ position: start_point, map: map });
        end_pin    = new google.maps.Marker({ position: end_point,   map: map });

        /* Hyperlapse */
        hyperlapse = new Hyperlapse(document.getElementById('pano'), {
            lookat:                  null,
            fov:                     80,
            millis:                  50,
            width:                   window.innerWidth,
            height:                  window.innerHeight,
            zoom:                    2,
            use_lookat:              false,
            distance_between_points: 5,
            max_points:              100,
            elevation:               0
        });

        hyperlapse.onError = function (e) {
            showHyperlapseFeedback("ERROR: "+ e.message);
        };
        hyperlapse.onRouteComplete = function (e) {
            directions_renderer.setDirections(e.response);
            showHyperlapseFeedback("Number of Points: "+ hyperlapse.length());
            if (hyperlapse.length() < 1) {
                nextIteration();
            } else {
                hyperlapse.load();
            }
        };
        hyperlapse.onLoadComplete = function (e) {
            hyperlapse.play();
        };
        hyperlapse.onFrame = function (e) {
            _isLastFrame   = e.position + 1 === hyperlapse.length(),
            _frameStepSec  = Math.round((end_coords.epoch - start_coords.epoch) / hyperlapse.length()),
            _screenieEpoch = start_coords.epoch + (_frameStepSec * e.position);

            // Pause lapse, take a screenshot, screenshot callback toggles playing of next frame or next iteration of coords
            hyperlapse.pause();
            $.post('/StreetView/save_screenshot.php', {
                blob:    hyperlapse.getCurrentImage().toDataURL('image/jpeg'),
                epoch:   _screenieEpoch,
                pos:     e.position,
                subject: '<?=$_GET['subject']?>'
            }, function () {
                if (_isLastFrame) {
                    // Next iteration
                    nextIteration();
                } else {
                    hyperlapse.play();
                }
            });

            showHyperlapseFeedback(
                "Position: " + (e.position+1) +" of "+ hyperlapse.length()
            );
            camera_pin.setPosition(e.point.location);
        };

        /* Start route generation */
        showHyperlapseFeedback("Generating route...");
        directions_renderer.setDirections({routes: []});
        directions_service.route({
                origin:      start_point,
                destination: end_point,
                travelMode:  google.maps.DirectionsTravelMode[start_coords.speed > 5 ? 'DRIVING' : 'WALKING']
            },
            function (response, status) {
                if (status == google.maps.DirectionsStatus.OK) {
                    hyperlapse.generate({ route: response });
                } else {
                    showHyperlapseFeedback('Google directions service error: ' + status);
                }
            }
        );
        //console.log('travelmode: ' + (start_coords.speed > 5 ? 'DRIVING' : 'WALKING'));
    }

    function getCurrentTime () {
        var objToday = new Date(),
            hour     = objToday.getHours() > 12 ? objToday.getHours() - 12 : (objToday.getHours() < 10 ? "0" + objToday.getHours() : objToday.getHours()),
            minute   = objToday.getMinutes() < 10 ? "0" + objToday.getMinutes() : objToday.getMinutes(),
            seconds  = objToday.getSeconds() < 10 ? "0" + objToday.getSeconds() : objToday.getSeconds();
        return hour + ':' + minute + ':' + seconds;
    }

    // GPX data
    var jsonCoords = <?=$gpxParsed?>;
    // Start the hyperlapse!
    $(document).ready(function () {
        $('#feedback').val('Started at ' + getCurrentTime());
        initHyperlapse(<?=$_GET['offset']?>);
    });
</script>
<style>
#pano {
    position: absolute;
    left: 0px;
    top: 0px;
    right: 0px;
    bottom: 0px;
    z-index: -1;
}
#map  {
    position: absolute;
    left: 10px;
    top: 10px;
    width: 400px;
    height: 300px;
    padding: 0;
    border: 2px solid black;
}
#feedback {
    position: absolute;
    left: 10px;
    top: 320px;
    width: 380px;
    height: 120px;
    padding: 10px;
    z-index: 0;
    border: 2px solid black;
    background-color: white;
}
</style>
</head>
<body>
    <div id="pano"></div>
    <div id="map"></div>
    <textarea id="feedback"></textarea>
</body>
</html>
