#!/usr/bin/env bash
# matrix.sh [parallel runs, default 2] — every combination of matrix.txt; one result line each, see README.md.
set -uo pipefail
HERE=$(cd "$(dirname "$0")" && pwd)
PARALLEL=${1:-2}
port=8800
while read -r contao versions; do
  [[ -z "$contao" || "$contao" == \#* ]] && continue
  for php in $versions; do port=$((port + 1)); echo "$contao $php $port"; done
done < "$HERE/matrix.txt" | xargs -P "$PARALLEL" -L 1 "$HERE/run.sh"
