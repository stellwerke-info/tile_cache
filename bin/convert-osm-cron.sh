#!/bin/bash

pushd $HOME/html

quota -gsl

echo "converting orm png->webp:"
convert-osm.sh webp orm/{3..17} | wc -l
echo "converting osm png->avif:"
convert-osm.sh avif osm/{3..17} | wc -l

echo "dedeuplicting (small) tile files"
rdfind -makehardlinks true -maxsize 3000 -makeresultsfile false orm/{3..17}
rdfind -makehardlinks true -maxsize 3000 -makeresultsfile false osm/{3..17}

quota -gsl
