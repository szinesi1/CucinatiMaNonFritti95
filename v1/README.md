# Cucinati Ma Non Fritti 95 — Django

Refactor del progetto PHP/MySQL in Python, Django e Bootstrap, mantenendo la presentazione grafica del progetto originale.

## Garanzie sulla grafica

- I 17 fogli CSS originali sono stati copiati senza cambiare colori, variabili, misure o regole di layout.
- Le immagini originali delle 75 ricette sono incluse.
- I template Django mantengono la struttura originale: header superiore, sidebar verticale a sinistra, contenuto a destra e footer inferiore.
- Le classi HTML originali (`top-header`, `layout`, `sidebar`, `card-grid`, `card`, `filters`, ecc.) sono state conservate.
- Bootstrap 5.3.3 è caricato prima del CSS originale.
- `bootstrap-compat.css` non introduce un nuovo design: neutralizza soltanto i conflitti tra Bootstrap e le classi già usate dal progetto (`.btn`, `.card`, `.carousel`, `.modal`).

## Avvio rapido su Windows

Eseguire:

```text
avvia_windows.bat
```

Oppure manualmente:

```powershell
py -m venv .venv
.venv\Scripts\activate
python -m pip install -r requirements.txt
python manage.py migrate
python manage.py seed_data
python manage.py runserver
```

Aprire `http://127.0.0.1:8000/`.

## Avvio su Linux/macOS

```bash
./avvia_linux_mac.sh
```

## Database

Il progetto usa SQLite e contiene già un database popolato. Per ricrearlo dai file SQL originali:

```bash
python manage.py migrate
python manage.py seed_data --reset
```

Dati importati:

- 75 ricette
- 300 utilizzi di ingredienti
- 145 nomi di ingrediente distinti
- 20 regioni
- 5 libri
- 79 collegamenti tra libri e ricette

## Funzioni migrate

- Home page
- Elenco e ricerca ricette
- Filtro per tipologia
- Dettaglio ricetta e carosello
- Elenco e ricerca ingredienti
- Filtro alfabetico
- Dettaglio ingrediente
- Aggiunta, modifica e cancellazione ingredienti
- Modifica globale del nome ingrediente
- Elenco e filtro regioni
- Dettaglio regione
- Elenco e filtro libri
- Dettaglio libro
- Pannello amministrativo Django

## File principali

```text
config/                         configurazione Django
cucina/models.py                modelli e relazioni del database
cucina/views.py                 logica delle pagine e dei filtri
cucina/forms.py                 form CRUD degli ingredienti
cucina/urls.py                  URL dell'applicazione
templates/                      HTML convertito in template Django
cucina/static/cucina/css/       CSS originale e compatibilità Bootstrap
cucina/static/cucina/js/        JavaScript del carosello e della modale
cucina/static/cucina/img/       immagini originali
cucina/seed_sql/                dati SQL originali
```
