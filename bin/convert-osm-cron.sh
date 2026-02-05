#!/bin/bash

pushd $HOME/html

quota -gsl

echo "converting orm png->webp:"
convert-osm.sh webp orm/{3..17} | wc -l
echo "converting osm png->avif:"
convert-osm.sh avif osm/{3..17} | wc -l

quota -gsl
