#!/usr/bin/python
# -*- coding: utf-8 -*-

import csv
import json
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
        for key, group in groupby(elems, lambda x: (x.id, x.time)):
            print((len(list(group)), key))
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
        if (raw_name != 'nimi'):  # toores
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

    def __repr__(self):
        return str(self.id) + ' - ' + str(self.time)


def extract_csv(file):
    data = []
    with open(file, 'rb') as csvfile:
        csvdata = csv.reader(csvfile, delimiter=',', quotechar='"')
        for row in csvdata:
            row_data = StoryElement(row)
            data.append(row_data)
        return data


def to_json(data):
    return json.dumps(data, sort_keys=True, indent=2)


def compile(file):
    data = extract_csv(file)
    story = StoryData(data)
    print(list(i for i in data if i.id == 'TOOMAS-PLIIATS'
               and str(i.time) == '2014-04-10 23:28:00'))
