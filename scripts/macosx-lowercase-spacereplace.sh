find . -name '*.*' -exec sh -c '
  a=$(echo {} | tr '[:upper:]' '[:lower:]');
  [ "$a" != "{}" ] && mv "{}" "$a" ' \;

#### spaces to underscores
# cd victoriabalblabala 
# rename 's/\s+/_/g' pc\ screen\ 0*
