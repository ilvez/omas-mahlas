#!/usr/bin/env python
# -*- coding: utf-8 -*-

import os
import sys


try:
    from setuptools import setup
except ImportError:
    from distutils.core import setup



requirements = [
    'twisted',
    'pyserial'
]


setup(
    name='timofirmata',
    version='0.1.0',
    description='Control IO over HTTP',
    long_description='',
    author='Tanel Karindi',
    author_email='mou@pelltech.eu',
    packages=[
        'timofirmata',
    ],
    package_dir={'timofirmata':
                 'timofirmata'},
    include_package_data=True,
    install_requires=requirements,
    license="BSD",
    zip_safe=False,
    keywords='timofirmata',
    classifiers=[
        'Development Status :: 2 - Pre-Alpha',
        'License :: OSI Approved :: BSD License',
        'Natural Language :: English',
        "Programming Language :: Python :: 2",
        'Programming Language :: Python :: 2.7',
    ],
    entry_points={
        'console_scripts':
            ['timofirmata=timofirmata:main', ]
    }
)
