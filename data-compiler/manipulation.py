#!/usr/bin/python
# -*- coding: utf-8 -*-

import csv
import json
import logging
from datetime import datetime
from itertools import groupby


class StoryData:
    """..."""
    d = []

    def __init__(self, elems):
        '''
        1. Siin peaks võtma elemsitest järjest elemente;
        siis filtreerima teised sama id ja ajaga (minutitäpsus) elemendid;
        saadud elementide arvu jagama 60 ja saame sekundid
        muudame aegu ja lisame sageduse
        paneme uude listi.
        elemsist eemaldame vastavad elemendid ja algusesse, kuni elems tühi
        Saadud listis on elemendid sekunditäpsusega.
        '''
        for key, group in groupby(elems, lambda x: x.idx()):
            times_in_min = len(list(group))
            logging.debug("%s, %s", times_in_min, key)
            for e in list(group):
                logging.debug('\t%s', e.id)
        pass


class StoryElement:
    '''StoryElement corresponds to one row in input CSV'''
    id = None
    time = None
    name = None
    data = None
    light = None
    action = None
    screenshot = None

    def __init__(self, row):
        raw_name = row[0]
        raw_datetime = self.fix(row[1] + "-" + self.format_time(row[2]))
        self.name = raw_name
        self.time = datetime.strptime(raw_datetime, '%d:%m:%Y-%H:%M')
        self.light = row[3]
        self.action = row[4]
        self.data = row[5]
        self.screenshot = row[6]
        self.id = self.give_id()

    def format_time(self, time):
        if (len(time) == 4):  # deeply sophisticated
            time = '0' + time
        return time

    def fix(self, datetime):
        return datetime.replace('.', ':')

    def give_id(self):
        id = self.name.upper().replace(' ', '-')
        return id

    def idx(self):
        id = self.name.upper().replace(' ', '-')
        id = id + self.time.strftime('-%y%m%d-%H%M%S')
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


def compile(file, debug):
    setup_logging(debug)
    data = extract_csv(file)
    story = StoryData(data)
    logging.debug(list(i for i in data if i.id == 'TOOMAS-PLIIATS'
                       and str(i.time) == '2014-04-10 23:28:00'))
