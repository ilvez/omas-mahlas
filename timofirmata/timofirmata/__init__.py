#!/usr/bin/env python
# -*- coding: utf-8 -*-

__author__ = 'Tanel Karindi'
__email__ = 'mou@pelltech.eu'
__version__ = '0.1.0'

from timofirmata import run

def main():
    run(port="/dev/ttyUSB0", baudrate=9600)
