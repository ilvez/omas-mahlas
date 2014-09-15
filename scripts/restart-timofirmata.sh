#!/bin/bash

# Script tries to restart timofirmata process number of times

log_file=/Users/niisamalinnas/log/timofirmata.log
start_command="/usr/bin/python /usr/local/bin/timofirmata"
sleep_between=2

function get_pid() {
    pid=$(ps -ef | grep timofirmata | grep python | grep -v grep | tr -s ' ' | cut -d' ' -f 3)
    if [ -z $pid ]; then
        pid=0
    fi
    #echo pid: $pid
}

function kill_times() {
    i=1
    while [ "$i" -lt "$1" ]
    do
        get_pid
        if [ "$pid" -gt "0" ]; then
            kill $pid
            echo $(date) - DEBUG - killed: $pid >> $log_file
        else
            break
        fi
        i=$[$i+1]
        sleep $sleep_between
    done
    if [ "$i" -ge "$1" ]; then
        echo $(date) - ERROR - kill_times failed miserably after $i times >> $log_file
    fi
}

function restart_times() {
    i=1
    while [ "$i" -lt "$1" ]
    do
        get_pid
        if [ "$pid" -eq "0" ]; then
            $start_command &
            get_pid
            echo $(date) - DEBUG - started: $pid >> $log_file
        else
            break
        fi
        i=$[$i+1]
        sleep $sleep_between
    done
    if [ "$i" -ge "$1" ]; then
        echo $(date) - ERROR - restart_times failed miserably after $i times >> $log_file
    fi
}

kill_times 10
restart_times 5
