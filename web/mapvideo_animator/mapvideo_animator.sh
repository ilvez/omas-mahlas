#!/bin/bash
cd /home/erm/gpxanim
export DISPLAY=:0
for i in `ls /home/erm/gps/track_gpx_speed_added/*.gpx`; do
echo $i

python gpxanim.py -z 18 --width 1920 --height 1080 -o "$i.ogg" -s 10 "$i"
done
