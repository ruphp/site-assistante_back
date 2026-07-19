#!/usr/bin/env sh
set -eu

REPO_ROOT="$(git rev-parse --show-toplevel)"
HOOK="${REPO_ROOT}/.git/hooks/post-merge"

mkdir -p "${REPO_ROOT}/.git/hooks"

cat > "${HOOK}" <<'HOOK'
#!/usr/bin/env sh
set -eu

REPO_ROOT="$(git rev-parse --show-toplevel)"
SCRIPT="${REPO_ROOT}/scripts/prod-after-pull.sh"

if [ -x "${SCRIPT}" ]; then
  "${SCRIPT}"
else
  sh "${SCRIPT}"
fi
HOOK

chmod +x "${HOOK}"

echo "Installed ${HOOK}"
echo "Next normal git pull will run scripts/prod-after-pull.sh"
