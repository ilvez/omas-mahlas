#!/usr/bin/python
# -*- coding: utf-8 -*-

import csv
import json
import logging
import calendar
import os
import re
from datetime import datetime, timedelta
from itertools import groupby


class StoryData:
    """..."""
    elements = []

    def __init__(self, elems):
        for key, group in groupby(elems, lambda x: x.idx()):
            elem_list = list(group)
            times_in_min = len(elem_list)
            if times_in_min > 59:
                logging.warn("too many times in min: %s", times_in_min)
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

    def to_json(self):
        return json.dumps(self, default=lambda o: o.__dict__,
                          sort_keys=True, indent=4)


class StoryElement:
    '''StoryElement corresponds to one row in input CSV'''
    id = None
    time = None  # We keep time in string to simplify JSON dump
    name = None
    data = None
    light = None
    action = None
    screenshot = None
    rating = 0
    global CSV_TIME_FORMAT, INTERNAL_FORMAT
    CSV_TIME_FORMAT = '%d:%m:%Y-%H:%M'
    INTERNAL_FORMAT = '%Y-%m-%d %H:%M:%S'

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
        self.screenshot = '/data/screenshots/' + self.id + '/' +self.format_path(row[6])

        # Do some tests
        shot_abs_path = os.getcwd() + self.screenshot
        if not self.test_path(shot_abs_path):
            logging.error('No such file: %s', self.screenshot)
            self.screenshot = '/data/screenshots/missing.png'

    def format_time(self, time):
        if (len(time) == 4):  # deeply sophisticated
            time = '0' + time
        return time

    def test_path(self, path):
        return os.path.exists(path)

    def format_path(self, path):
        new_path = path.lower().replace(' ','_')
        new_path = new_path.replace(':', '_')
        new_path = new_path.replace('sceenshot', 'screenshot')
        new_path = re.sub('\s+',' ', new_path)
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

    def add_seconds(self, sec):
        new_time = self.get_time() + timedelta(0, sec)
        logging.debug("%s, %s", new_time,
                      str(isinstance(new_time, basestring)))
        self.set_time(new_time, INTERNAL_FORMAT)

    # Function takes in string
    def set_time(self, time, fmat):
        logging.debug("time before: %s (string: %s)", self.time,
                      str(isinstance(self.time, basestring)))
        if isinstance(time, basestring):
            new_time = str(datetime.strptime(time, fmat))
        elif isinstance(time, datetime):
            new_time = str(time)
        else:
            raise TypeError('time must be string or datetime')
        self.time = new_time
        logging.debug("time after : %s (string: %s)", self.time,
                      str(isinstance(self.time, basestring)))

    def get_time(self):
        logging.debug("returning time: %s, %s (string: %s)",
                      self.name, self.time,
                      str(isinstance(self.time, basestring)))
        return datetime.strptime(self.time, INTERNAL_FORMAT)

    def idx(self):
        logging.debug("StoryElement: %s, %s", self.name, self.time)
        id = self.give_id()
        id = id + self.get_time().strftime('-%y%m%d-%H%M%S')
        return id

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


def compile(file, debug, json_path):
    setup_logging(debug)
    story = StoryData(extract_csv(file))
    if json_path is None:
        logging.info('%s', story.to_json())
    else:
        with open(json_path, 'w') as json_file:
            json_file.write(story.to_json())
        logging.info("Output written to: %s", json_path)
