#!/usr/bin/python
# -*- coding: utf-8 -*-

import csv
import json
import logging
import time
import os
import re
from datetime import datetime
from itertools import groupby

REL_PATH_SCREENSHOTS = '/data/screenshots/'
REL_PATH_MAPVIDEOS = '/data/mapvideo/'
REL_PATH_STREETVIDEOS = 'streetvideo/videos/'


class StoryData:
    elements = []

    def __init__(self, elems, mapvid, streetvid):
        for key, group in groupby(elems, lambda x: x.idx()):
            elem_list = list(group)
            times_in_min = len(elem_list)
            if times_in_min > 59:
                logging.error("too many times in min: %s", times_in_min)
                raise ValueError()
            sec_inc = round(60 / times_in_min) % 60
            sec_add = 0
            logging.debug("count: %s, sec: %d - %s",
                          times_in_min, sec_inc, key)
            for e in elem_list:
                e.add_seconds(sec_add)
                e.rating = times_in_min
                sec_add += sec_inc
                logging.debug('Updated %s: rating: %s, time: %s',
                              e.idx(), e.rating, e.time)
        self.elements = elems

        # Now we parse map and streetview video files
        self.parse_map_video(mapvid)
        self.parse_street_video(streetvid)

    def to_json(self):
        return json.dumps(self, default=lambda o: o.__dict__,
                          sort_keys=True, indent=4)

    def read_regular_file(self, filepath):
        lines = []
        with open(filepath, 'r') as f:
            for line in f:
                lines.append(line)
        return lines

    def dir_file_to_list(self, dir_or_file):
        lines = []
        if os.path.isdir(dir_or_file):
            # Tagastame relational pathid
            pass
        elif os.path.isfile(dir_or_file):
            lines = self.read_regular_file(dir_or_file)
        else:
            logging.error("File missing: %s", dir_or_file)
            raise ValueError("File missing...")
        logging.info("Number of files: %s", len(lines))
        return lines

    def add_map_video(self, parts):
        logging.debug("Searching elements for video: %s (%s -> %s)", parts.id, 
                      ts_to_str(parts.begin_ts), ts_to_str(parts.end_ts))
        for e in filter(lambda x: x.time >= parts.begin_ts and
                                  x.time <= parts.end_ts and
                                  x.id == parts.id, self.elements):
            logging.debug("\tFound: %s - %s", e.id, ts_to_str(e.time))
            e.mapvideo = parts.relpath

    def parse_map_video(self, vids):
        logging.info("Parsing map videos: %s", vids)
        file_list = self.dir_file_to_list(vids)
        for name in file_list:
            parts = MapFileName(name, self.elements)
            self.add_map_video(parts)
        pass

    def parse_street_video(self, vids):
        logging.info("Parsing streetview videos: %s", vids)
        file_list = self.dir_file_to_list(vids)
        pass


class MapFileName:
    id = None
    begin = None
    end = None
    name = None
    relpath = None
    begin_ts = None
    end_ts = None

    def __init__(self, filename, elements):
        parts = filename.split('_')
        self.id = self.remove_dash(parts[0])
        self.begin = parts[1]
        self.end = parts[2]
        self.name = filename.replace('\n', '')
        self.relpath = REL_PATH_MAPVIDEOS + self.name
        self.begin_end_time(elements)
        pass

    def begin_end_time(self, elements):
        day_ts = datetime.fromtimestamp(self.find_date(elements))
        self.begin_ts = d_to_ts(self.combine(day_ts, self.begin))
        self.end_ts = d_to_ts(self.combine(day_ts, self.end))
        logging.debug("%s: %s (%s) -> %s (%s)", self.id, self.begin_ts, 
                      ts_to_str(self.begin_ts), self.end_ts, ts_to_str(self.end_ts))

    # Finds first timestamp with matching id, we need date
    def find_date(self, elements):
        return next(elem for elem in elements if elem.id == self.id).time

    def combine(self, d, str):
        timeparts = str.split('-')
        return datetime(d.year, d.month, d.day, int(timeparts[0]),
                        int(timeparts[1]), int(timeparts[2]))

    def __repr__(self):
        return self.id + ': ' + str(self.begin) + '->' + str(self.end) +\
            '\n\t\tname:' + self.name + '\n\t\tpath:' + self.relpath

    def remove_dash(self, s):
        return s.replace('-', '')


class StoryElement:
    '''StoryElement corresponds to one row in input CSV'''
    id = None
    time = None  # time is localtime Unix timestamp
    name = None
    data = None
    light = None
    action = None
    screenshot = None
    rating = 0
    mapvideo = None
    streetvideo = None

    global CSV_TIME_FORMAT
    CSV_TIME_FORMAT = '%d:%m:%Y-%H:%M'

    def __init__(self, row):
        # set variables
        raw_name = row[0]
        raw_datetime = self.fix(row[1] + "-" + self.format_time(row[2]))
        self.name = raw_name
        self.set_time(raw_datetime, CSV_TIME_FORMAT)
        self.light = row[3]
        self.action = row[4]
        self.data = row[5]
        self.id = self.give_id()
        self.screenshot = REL_PATH_SCREENSHOTS + self.id + '/'\
            + self.format_path(row[6])

        # Do some tests
        shot_abs_path = os.getcwd() + self.screenshot
        if not self.test_path(shot_abs_path):
            logging.error('No such file: %s', self.screenshot)
            self.screenshot = REL_PATH_SCREENSHOTS + 'missing.png'

    def format_time(self, time):
        if (len(time) == 4):  # deeply sophisticated
            time = '0' + time
        return time

    def test_path(self, path):
        return os.path.exists(path)

    def format_path(self, path):
        new_path = path.lower().replace(' ', '_')
        new_path = new_path.replace(':', '_')
        new_path = new_path.replace('sceenshot', 'screenshot') ## Väga vajalik
        new_path = re.sub('\s+', ' ', new_path)
        return new_path

    def fix(self, datetime):
        return datetime.replace('.', ':')

    def give_id(self):
        id = self.name.lower().replace(' ', '')
        id = id.replace('-', '')
        id = id.replace('õ', 'o')
        id = id.replace('ö', 'o')
        id = id.replace('ä', 'a')
        id = id.replace('ü', 'u')
        return id

    def idx(self):
        logging.debug("StoryElement: %s, %s", self.name, self.time)
        idx = self.give_id()
        idx = idx + "-" + str(self.time)
        return idx

    def set_timestamp(self, timestamp):
        self.time = int(round(timestamp))
        logging.debug("setting time : %s (int: %s)", self.time,
                      str(isinstance(self.time, int)))

    def add_seconds(self, sec):
        new_time = self.time + sec
        logging.debug("%s, %s", new_time,
                      str(isinstance(new_time, basestring)))
        self.set_timestamp(new_time)

    # Function takes in string
    def set_time(self, input_time, fmat):
        logging.debug("time before: %s (string: %s)", self.time,
                      str(isinstance(self.time, basestring)))
        if isinstance(input_time, basestring):
            new_time = datetime.strptime(input_time, fmat)
        elif isinstance(input_time, datetime):
            new_time = input_time
        else:
            raise TypeError('time must be string or datetime')
        self.set_timestamp(time.mktime(new_time.timetuple()))

    def get_time(self):
        logging.debug("returning time: %s, %s (int: %s)",
                      self.name, self.time,
                      str(isinstance(self.time, int)))
        return time.localtime(self.time)

    def __repr__(self):
        return str(self.id) + ' - ' + str(self.time)


def extract_csv(file):
    data = []
    with open(file, 'rb') as csvfile:
        csvdata = csv.reader(csvfile, delimiter=',', quotechar='"')
        for row in csvdata:
            logging.debug(row)
            if (row[0] != 'nimi'):
                row_data = StoryElement(row)
                data.append(row_data)
        return data


def to_json(data):
    return json.dumps(data, sort_keys=True, indent=2)


def setup_logging(debug):
    lev = logging.INFO
    form = '%(levelname)8s: %(message)s'
    if debug:
        lev = logging.DEBUG
        form = '%(levelname)8s: %(funcName)15s - %(message)s'
    logging.basicConfig(level=lev, format=form)


# Date to local timestamp
def d_to_ts(d):
    return int(round(time.mktime(d.timetuple())))

def ts_to_str(ts):
    return datetime.fromtimestamp(ts)

def compile(file, debug, json_path, mapvid, streetvid):
    setup_logging(debug)
    story = StoryData(extract_csv(file), mapvid, streetvid)
    if json_path is None:
        logging.info('%s', story.to_json())
    else:
        with open(json_path, 'w') as json_file:
            json_file.write(story.to_json())
        logging.info("Output written to: %s", json_path)
