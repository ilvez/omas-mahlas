#!/bin/bash

find -name "* *" -exec rename 's/ /_/g' {} \;

find screenshots -exec rename 'y/A-Z/a-z/' {} \;
find sounds -exec rename 'y/A-Z/a-z/' {} \;
