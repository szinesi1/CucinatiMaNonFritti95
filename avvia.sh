#!/usr/bin/env sh
set -e

PROJECT_DIR=$(CDPATH= cd -- "$(dirname -- "$0")" && pwd)
cd "$PROJECT_DIR"

if command -v python3.12 >/dev/null 2>&1; then
    python3.12 avvia.py
else
    python3 avvia.py
fi
