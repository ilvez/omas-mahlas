<?php
function gpx_to_json ($filePath)
{
    $gpxXml = new \SimpleXMLElement(file_get_contents($filePath));

    $trackPoints = array();
    foreach ($gpxXml->trk->trkseg->trkpt as $trackPoint) {
        $epoch = strtotime((string)$trackPoint->time);
        $trackPoints[] = array(
            'latitude'  => (string)$trackPoint['lat'],
            'longitude' => (string)$trackPoint['lon'],
            'epoch'     => $epoch,
            //'course'    => (string)$trackPoint->course,
            'speed'     => (int)ceil((string)$trackPoint->speed),
        );
    }

    return json_encode($trackPoints);
}
