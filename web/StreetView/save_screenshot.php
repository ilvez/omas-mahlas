<?php
function base64_to_jpeg ($base64_string, $output_file)
{
    $ifp  = fopen($output_file, "wb");
    $data = explode(',', $base64_string);

    fwrite($ifp, base64_decode($data[1]));
    fclose($ifp);
}

$position  = $_POST['pos'];
$date      = new DateTime('@' . $_POST['epoch']);
$dateEpoch = $date->format('Ymd_His');
$fileName  = sprintf('%s-%s.jpg', $dateEpoch, $position);
$filePath  = '../../data/subjects/' . $_POST['subject'] . '/StreetView/' . $fileName;

base64_to_jpeg($_POST['blob'], $filePath);
