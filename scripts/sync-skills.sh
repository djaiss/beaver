#!/usr/bin/env bash

set -euo pipefail

PROJECT_ROOT="${1:-$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)}"
SOURCE_PATH="$PROJECT_ROOT/.github/skills"
LOCK_PATH="$PROJECT_ROOT/.sync-skills.lock"
STAGING_PATH=""
SWAPPED_TARGETS=0
TARGETS=(
    "$PROJECT_ROOT/.agents/skills"
    "$PROJECT_ROOT/.ai/skills"
)

release_lock() {
    rm -rf "$LOCK_PATH"
}

rollback() {
    local index
    local target_path
    local backup_path

    for ((index = SWAPPED_TARGETS - 1; index >= 0; index--)); do
        target_path="${TARGETS[$index]}"
        backup_path="$STAGING_PATH/backups/$index"

        rm -rf "$target_path"

        if [[ -e "$backup_path" || -L "$backup_path" ]]; then
            mv "$backup_path" "$target_path"
        fi
    done
}

cleanup() {
    local exit_code=$?

    if [[ $exit_code -ne 0 && -n "$STAGING_PATH" ]]; then
        rollback
    fi

    if [[ -n "$STAGING_PATH" ]]; then
        rm -rf "$STAGING_PATH"
    fi

    release_lock
}

if [[ ! -d "$SOURCE_PATH" ]]; then
    echo "The source skills directory was not found at $SOURCE_PATH." >&2
    exit 1
fi

if ! mkdir "$LOCK_PATH" 2>/dev/null; then
    echo "Another skills synchronization is already running." >&2
    exit 1
fi

trap cleanup EXIT

STAGING_PATH="$(mktemp -d "${TMPDIR:-/tmp}/beaver-sync-skills.XXXXXX")"
mkdir -p "$STAGING_PATH/source" "$STAGING_PATH/replacements" "$STAGING_PATH/backups"

rsync -aL --delete "$SOURCE_PATH/" "$STAGING_PATH/source/"

for index in "${!TARGETS[@]}"; do
    replacement_path="$STAGING_PATH/replacements/$index"

    mkdir -p "$replacement_path"
    rsync -a --delete "$STAGING_PATH/source/" "$replacement_path/"
done

for index in "${!TARGETS[@]}"; do
    target_path="${TARGETS[$index]}"
    replacement_path="$STAGING_PATH/replacements/$index"
    backup_path="$STAGING_PATH/backups/$index"

    mkdir -p "$(dirname "$target_path")"

    if [[ -e "$target_path" || -L "$target_path" ]]; then
        mv "$target_path" "$backup_path"
    fi

    SWAPPED_TARGETS=$((index + 1))
    mv "$replacement_path" "$target_path"
done

rm -rf "$STAGING_PATH/backups"

echo "Skills synchronized."
