#!/bin/bash

set -euo pipefail

t="$1"; shift
if [[ "$t" != "avif" && "$t" != "webp" ]]; then
        echo "invalid type" >&2
        exit 1
fi

function cleanup_tile() {
        out_file="${1}.$t"

        if [[ -f "$out_file" ]]; then
                #echo "delete $1"
                rm "$1"
        fi
}

find "$@" -type f -name '*.png' -print0 | while IFS= read -r -d '' file; do cleanup_tile "$file"; done
