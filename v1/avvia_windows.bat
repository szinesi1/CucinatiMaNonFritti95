@echo off
if not exist .venv (
    py -m venv .venv
)
call .venv\Scripts\activate
python -m pip install -r requirements.txt
python manage.py migrate
python manage.py seed_data
python manage.py runserver
