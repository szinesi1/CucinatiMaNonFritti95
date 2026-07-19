#!/usr/bin/env sh
set -e
if [ ! -d .venv ]; then
  python3 -m venv .venv
fi
. .venv/bin/activate
python -m pip install -r requirements.txt
python manage.py migrate
python manage.py seed_data
python manage.py runserver
