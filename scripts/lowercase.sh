#!/bin/bash

#find -name "* *" -exec rename 's/ /_/g' {} \;

# Linux
#find screenshots -exec rename 'y/A-Z/a-z/' {} \;
#find sounds -exec rename 'y/A-Z/a-z/' {} \;

# Macos case insensitive rename workaround
#find . -name '*.*' -exec sh -c '
#  a=$(echo {} | tr '[:upper:]' '[:lower:]');
#  [ "$a" != "{}" ] && mv "{}" "$a" ' \;
