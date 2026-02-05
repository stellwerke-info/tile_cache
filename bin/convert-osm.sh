#!/bin/bash

set -euo pipefail

t="$1"; shift
if [[ "$t" != "avif" && "$t" != "webp" ]]; then
        echo "invalid type" >&2
        exit 1
fi

function convert_tile() {
	echo "$1"
	png_file="$1"
	out_file="${1}.avif"

	if [[ "$t" == "avif" ]]; then
		avifenc --qcolor 70 --qalpha 70 -d 8 -j 1 "$png_file" -o "$out_file" >/dev/null
	elif [[ "$t" == "webp" ]]; then
		cwebp -quiet -lossless "$png_file" -o "$out_file"
	fi

	if [[ -f "$out_file" ]]; then
		rm "$png_file"
	fi
}
#export -f convert_avif

#find "$@" -type f -name '*.png' -exec bash -c 'convert_avif "$0"' {} \;
find "$@" -type f -name '*.png' -print0 | while IFS= read -r -d '' file; do convert_tile "$file"; done

