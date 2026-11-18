#!/usr/bin/env bash
# run.sh <contao minor> <php version> <port>
#
# One combination of the version matrix: installs the Contao Managed Edition the way the Contao Manager does
# (composer create-project, then composer require), with every package of packages.txt from its working copy;
# creates the database, an administrator and a small site with every front-end part of the extensions; then
# smoke.php opens the back end and the front end and the log is searched for errors.
#
# Needs: php<version> and composer on the PATH, a MySQL server (MATRIX_DATABASE_URL, a user that may create
# databases), the working copies under NEO_REPOS. Writes to MATRIX_WORK; a passed run removes its installation.
set -uo pipefail
C=$1; P=$2; PORT=$3
HERE=$(cd "$(dirname "$0")" && pwd)
: "${MATRIX_DATABASE_URL:?set MATRIX_DATABASE_URL, e.g. mysql://root:secret@127.0.0.1:3306}"
NEO_REPOS=${NEO_REPOS:-$(cd "$HERE/../../.." && pwd)}
WORK=${MATRIX_WORK:-/tmp/neo-version-matrix}
NAME="c${C//./}-p${P//./}"
DIR="$WORK/$NAME"; LOG="$WORK/$NAME.log"; DB="matrix_${NAME//-/_}"
PHP="php$P"; COMPOSER="$PHP -d memory_limit=-1 $(command -v composer)"
export COMPOSER_NO_INTERACTION=1
mkdir -p "$WORK"; rm -rf "$DIR"; : > "$LOG"
step() { echo "== $1" >> "$LOG"; }
fail() { echo "$NAME FAIL $1 — see $LOG"; exit 1; }

PINS=$(awk -v c="$C" '$1 == c { $1 = ""; print }' "$HERE/pins.txt" 2>/dev/null | xargs)
step "create-project contao/managed-edition:$C.*${PINS:+ (held back: $PINS)}"
if [ -n "$PINS" ]; then
  $COMPOSER create-project "contao/managed-edition:$C.*" "$DIR" --no-progress --no-install >> "$LOG" 2>&1 || fail "create-project"
  (cd "$DIR" && $COMPOSER require $PINS --no-update >> "$LOG" 2>&1 && $COMPOSER install --no-progress >> "$LOG" 2>&1) || fail "create-project with $PINS"
else
  $COMPOSER create-project "contao/managed-edition:$C.*" "$DIR" --no-progress >> "$LOG" 2>&1 || fail "create-project"
fi
cd "$DIR"
VERSION=$($PHP -r 'foreach (json_decode(file_get_contents("vendor/composer/installed.json"), true)["packages"] as $p) if ($p["name"] === "contao/core-bundle") echo $p["version"];')

step "require the extensions"
REQ=()
while read -r pkg folder; do
  [[ -z "$pkg" || "$pkg" == \#* ]] && continue
  # A version for the working copy, so that "^1.0" of the other packages is met.
  $COMPOSER config "repositories.${pkg//\//-}" "{\"type\":\"path\",\"url\":\"$NEO_REPOS/$folder\",\"options\":{\"symlink\":true,\"versions\":{\"$pkg\":\"1.99.0\"}}}" >> "$LOG" 2>&1
  REQ+=("$pkg:1.99.0")
done < "$HERE/packages.txt"
$COMPOSER require "${REQ[@]}" -W --no-progress >> "$LOG" 2>&1 || fail "composer require (Contao $VERSION)"
# Installed from git when archive downloads are not available: the histories are not needed and take much space
# (only now, Composer cannot change a package without its .git folder).
find vendor -mindepth 3 -maxdepth 3 -name .git -type d -prune -exec rm -rf {} + 2>/dev/null

step "database, administrator, site"
php "$HERE/dbadmin.php" create "$DB" >> "$LOG" 2>&1 || fail "database"
SECRET=$(php -r 'echo bin2hex(random_bytes(16));')
printf 'APP_ENV=prod\nAPP_SECRET=%s\nDATABASE_URL="%s/%s?charset=utf8mb4"\n' "$SECRET" "$MATRIX_DATABASE_URL" "$DB" > .env.local
$PHP vendor/bin/contao-console cache:clear --no-warmup >> "$LOG" 2>&1
$PHP vendor/bin/contao-console contao:migrate --no-interaction --no-backup >> "$LOG" 2>&1 || fail "contao:migrate (Contao $VERSION)"
PASS=$(php -r 'echo bin2hex(random_bytes(12));')
$PHP vendor/bin/contao-console contao:user:create --username=admin --name=Admin --email=admin@example.com --language=en --password="$PASS" --admin >> "$LOG" 2>&1 || fail "contao:user:create"
FX=$(php "$HERE/fixture.php" "$DB" 2>> "$LOG") || fail "fixture"
$PHP "$HERE/aikey.php" >> "$LOG" 2>&1 || fail "ai key"
$PHP vendor/bin/contao-console cache:warmup >> "$LOG" 2>&1
rm -f var/logs/*.log

step "server and smoke test"
cp "$HERE/router.php" "$DIR/router.php"
APP_ENV=prod $PHP -S 127.0.0.1:$PORT router.php >> "$WORK/$NAME.server.log" 2>&1 &
SERVER=$!
for i in $(seq 1 30); do curl -s -o /dev/null "http://127.0.0.1:$PORT/contao/login" && break; sleep 0.5; done
RESULT=$(php "$HERE/smoke.php" "http://127.0.0.1:$PORT" "$PASS" "$FX"); OK=$?
kill $SERVER 2>/dev/null; wait $SERVER 2>/dev/null
echo "$RESULT" >> "$LOG"
ERRORS=$(cat var/logs/*.log 2>/dev/null | grep -E '\.(CRITICAL|ALERT|EMERGENCY|ERROR):' | grep -v -E 'NotFoundHttpException|PageNotFoundException' | cut -c1-400)
[ -n "$ERRORS" ] && { echo "$ERRORS" >> "$LOG"; OK=1; }
VISITED=$(echo "$RESULT" | php -r '$d = json_decode(stream_get_contents(STDIN), true); echo ($d["visited"] ?? 0), " pages, spam protection: ", $d["spam_protection"] ?? "";')
php "$HERE/dbadmin.php" drop "$DB" > /dev/null 2>&1
if [ $OK -eq 0 ]; then
  echo "$NAME OK Contao $VERSION, PHP $($PHP -r 'echo PHP_VERSION;'), $VISITED"
  cd "$WORK" && rm -rf "$DIR"
else
  echo "$NAME FAIL Contao $VERSION, $VISITED — see $LOG"
fi
exit $OK
