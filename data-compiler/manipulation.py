#!/usr/bin/python
# -*- coding: utf-8 -*-

import csv
import json
import logging
import time
import os
import re
import itertools
import sys
from datetime import datetime
from itertools import groupby

REL_PATH_SCREENSHOTS = '/data/screenshots/'
REL_PATH_MAPVIDEOS = '/data/mapvideo/'
REL_PATH_STREETVIDEOS = '/data/streetvideo/videos/'

CSV_TIME_FORMAT = '%d:%m:%Y-%H:%M'
MIN_TIME_PER_SLIDE = 2
MAX_TIME_PER_SLIDE = 6
DEFAULT_TIME_PER_SLIDE = 4

MAP_VIDEO = 'MAP_VIDEO'
STREET_VIDEO = 'STREET_VIDEO'


class StoryData:
    elements = []
    metadata = []
    fullStoryTime = 0

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
        self.calculate_time_for_slide()

        # Now we parse map and streetview video files
        mapvideos = self.parse_videos(mapvid, MAP_VIDEO)
        streetvideos = self.parse_videos(streetvid, STREET_VIDEO)
        self.sort_videofiles_by_time(mapvideos)
        self.sort_videofiles_by_time(streetvideos)
        self.add_videos(mapvideos, streetvideos)
        self.sort_elements_by_time()

        # TODO: at the moment fullstorytime gets updated in parse_maps, and
        # it is bad karma, because it should be calculated in one place, fix

    def sort_videofiles_by_time(self, files):
        files.sort(key=lambda x: x.begin, reverse=False)

    def sort_elements_by_time(self):
        self.elements.sort(key=lambda x: (x.id, x.time), reverse=False)

    def calculate_time_for_slide(self):
        # Lets fix every element display time and calculate fullStoryTime
        seq = [len(x.data) for x in self.elements]
        avg_len = sum(seq)/len(seq)
        logging.debug("Element data length avg: %s", avg_len)
        for e in self.elements:
            if e.light in ['facebookmessenger', 'facebook']:
                length = len(e.data) / avg_len
                if length > MAX_TIME_PER_SLIDE:
                    length = MAX_TIME_PER_SLIDE
                elif length < MIN_TIME_PER_SLIDE:
                    length = MIN_TIME_PER_SLIDE
                e.time_for_slide = length
            else:
                e.time_for_slide = DEFAULT_TIME_PER_SLIDE
            logging.debug("%s - %s - %s", e.time_for_slide, e.light, e.data)
            self.fullStoryTime += e.time_for_slide
        logging.info("Full story time: %s", self.fullStoryTime)

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
            lines = os.listdir(dir_or_file)
        elif os.path.isfile(dir_or_file):
            lines = self.read_regular_file(dir_or_file)
        else:
            logging.error("File missing: %s", dir_or_file)
            raise ValueError("File missing...")
        logging.info("Number of files: %s", len(lines))
        return lines

    def add_video(self, mapvideo, streetvideo, filtered):
        logging.debug("Searching elements for video: %s (%s -> %s)",
                      mapvideo.id,
                      ts_to_str(mapvideo.begin_ts), ts_to_str(mapvideo.end_ts))
        for e in filtered:
            logging.debug("\tFound: %s - %s", e.id, ts_to_str(e.time))
            e.mapvideo = mapvideo.relpath
            e.mapvideo_begin = mapvideo.begin_ts
            e.mapvideo_end = mapvideo.end_ts
            e.streetvideo = streetvideo.relpath
            e.streetvideo_begin = streetvideo.begin_ts
            e.streetvideo_end = streetvideo.end_ts

    def filter_events_with_video(self, videofile):
        return filter(lambda x: x.time >= videofile.begin_ts
                      and x.time <= videofile.end_ts and
                      x.id == videofile.id, self.elements)

    # Finds first timestamp with matching id, we need date
    def find_name(self, videofile):
        return next(elem for elem in self.elements
                    if elem.id == videofile.id).name

    def add_video_event(self, mapvideo, streetvideo):
        event = StoryElement(mapvideo=mapvideo, streetvideo=streetvideo,
                             name=self.find_name(mapvideo))
        self.elements.append(event)
        self.fullStoryTime += event.time_for_slide
        pass

    def add_videos(self, mapvideos, streetvideos):
        for mapvideo, streetvideo in itertools.izip(mapvideos, streetvideos):
            filtered = self.filter_events_with_video(mapvideo)
            if len(filtered) > 0:
                logging.debug("%s added to events: %s",
                              mapvideo.type, mapvideo.name)
                logging.debug("%s added to events: %s",
                              streetvideo.type, streetvideo.name)
                self.add_video(mapvideo, streetvideo, filtered)
            else:
                self.add_video_event(mapvideo, streetvideo)
        logging.debug("%s added alone: %s", mapvideo.type, mapvideo.name)

    def parse_videos(self, vids, type):
        logging.info("Parsing %s's: %s", type, vids)
        file_list = self.dir_file_to_list(vids)
        videos = []
        for name in file_list:
            videos.append(VideoFile(name, self.elements, type))
        return videos


class VideoFile:
    id = None
    begin = None
    end = None
    name = None
    relpath = None
    begin_ts = None
    end_ts = None
    type = None

    def __init__(self, filename, elements, t_type):
        parts = filename.split('_')
        self.id = self.remove_dash(parts[0])
        self.begin = parts[1]
        self.end = parts[2]
        self.name = filename.replace('\n', '')
        self.begin_end_time(elements)
        self.type = t_type
        if self.type == MAP_VIDEO:
            self.relpath = REL_PATH_MAPVIDEOS + self.name
        else:
            self.relpath = REL_PATH_STREETVIDEOS + self.name
        pass

    def begin_end_time(self, elements):
        day_ts = datetime.fromtimestamp(self.find_date(elements))
        self.begin_ts = d_to_ts(self.combine(day_ts, self.begin))
        self.end_ts = d_to_ts(self.combine(day_ts, self.end))
        logging.debug("%s: %s (%s) -> %s (%s)", self.id, self.begin_ts,
                      ts_to_str(self.begin_ts), self.end_ts,
                      ts_to_str(self.end_ts))

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
    time_dt = None
    name = None
    data = None
    data_pic = None
    light = None
    light_id = None
    action = None
    screenshot = None
    rating = 0
    mapvideo = None
    mapvideo_begin = None
    mapvideo_end = None
    streetvideo = None
    streetvideo_begin = None
    streetvideo_end = None
    time_for_slide = None

    # TODO: Remove spaghetti from constructor
    def __init__(self, row=None, mapvideo=None, streetvideo=None, name=None):
        if row is not None:
            raw_name = row[0]
            raw_datetime = self.fix(row[1] + "-" + self.format_time(row[2]))
            self.name = raw_name
            self.set_time(raw_datetime, CSV_TIME_FORMAT)
            self.time_dt = str(ts_to_str(self.time))
            self.light = row[3]
            self.action = self.action_mapping(row[4])
            self.parse_data_field(row[5])
            self.id = self.give_id()
            self.screenshot = REL_PATH_SCREENSHOTS + self.id + '/'\
                + self.format_path(row[6])
            self.light_id = LAMP_MAPPING[self.light]
            self.video_only = 0

            # Do some tests
            shot_abs_path = os.getcwd() + self.screenshot
            if not self.test_path(shot_abs_path):
                logging.error('No such file: %s', self.screenshot)
                self.screenshot = REL_PATH_SCREENSHOTS + 'missing.png'
        elif mapvideo is not None and streetvideo is not None:
            self.id = mapvideo.id
            self.time = mapvideo.begin_ts
            self.time_dt = str(ts_to_str(self.time))
            self.name = name
            self.time_for_slide = mapvideo.end_ts - mapvideo.begin_ts
            if self.time_for_slide > 15:
                self.time_for_slide = 15
            self.mapvideo = mapvideo.relpath
            self.mapvideo_begin = mapvideo.begin_ts
            self.mapvideo_end = mapvideo.end_ts
            self.streetvideo = streetvideo.relpath
            self.streetvideo_begin = streetvideo.begin_ts
            self.streetvideo_end = streetvideo.end_ts
            self.video_only = 1

    def parse_data_field(self, data):
        logging.debug("data before: %s", data)
        g = re.search('AA.*.jpg', data)
        pic = ''
        if g is not None:
            pic = g.group(0)
            data = data.replace(pic, '').strip()
        self.data = data
        self.data_pic = pic.lower()
        logging.debug("data after: '%s', pic: '%s'", data, pic)

    def action_mapping(self, action):
        action = action.strip()
        if action in ACTION_MAPPING:
            action = ACTION_MAPPING[action]
        else:
            logging.error("Missing action mapping: %s", action)
        return action

    def format_time(self, time):
        if (len(time) == 4):  # deeply sophisticated
            time = '0' + time
        return time

    def test_path(self, path):
        return os.path.exists(path)

    def format_path(self, path):
        new_path = path.lower().replace(' ', '_')
        new_path = new_path.replace(':', '_')
        new_path = new_path.replace('sceenshot', 'screenshot')  # Väga vajalik
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
                row_data = StoryElement(row=row)
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
    logging.basicConfig(level=lev, format=form, stream=sys.stdout)


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

LAMP_MAPPING = {
    "app": 1,
    "mang": 2,
    "pleier": 3,
    "delfi": 4,
    "sms": 5,
    "www": 6,
    "instagram": 7,
    "raadio": 8,
    "netflix": 9,
    "e-kool": 10,
    "google": 11,
    "telefon": 12,
    "twitter": 13,
    "aratuskell": 14,
    "kaamera": 15,
    "blogi": 16,
    "arvuti": 17,
    "skype": 18,
    "iosmessenger": 19,
    "facebookmessenger": 20,
    "ask.fm": 21,
    "e-mail": 22,
    "facebook": 23,
    "tv": 24,
    "wiki": 25,
    "youtube": 26,
    "foorum": 27,
    "ebay": 28,
    "snapchat": 29,
    "tumblr": 30,
    "whatsapp": 31}

ACTION_MAPPING = {
    'uus pilt': 'silm',
    'sõnum välja': 'valja',
    'sõnum sisse': 'sisse',
    'vaatan': 'silm',
    'teade': 'sisse',
    'teated': 'sisse',
    'uus postitus': 'valja',
    'uus säuts': 'valja',
    'otsin': 'otsing',
    'kõne välja': 'valja',
    'säutsun': 'valja',
    'kuulan': 'kuulan',
    'kõne sisse': 'sisse',
    'postitan': 'valja',
    'pildistan': 'silm',
    'Uus säuts': 'valja',
    'allalaadimine': 'sisse',
    'Kuulan': 'kuulan',
    'Vaatan': 'silm',
    'uus teade': 'sisse',
    'Saabuv sõnum': 'sisse',
    'Saadan sõnumi': 'valja',
    'Vaatan infot': 'silm',
    'telefon välja': 'valja',
    'telefon sisse': 'sisse',
    '': 'tyhi'}
