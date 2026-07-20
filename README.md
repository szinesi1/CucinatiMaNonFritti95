# Cucinati Ma Non Fritti 95

Secondo progetto di Programmazione Web, opzione A: ristrutturazione del primo progetto con Python, Django e Bootstrap.

L'applicazione permette di consultare ricette, ingredienti, regioni e libri. Sono disponibili ricerca, filtri e operazioni di aggiunta, modifica ed eliminazione degli ingredienti. Il progetto funziona in locale e non richiede un IDE o una connessione a Internet.

## Requisiti

- Python 3.12
- Un browser web
- Un terminale o il Prompt dei comandi
- Permesso di scrittura nella cartella estratta

## Avvio su Windows

1. Estrarre completamente lo ZIP.
2. Aprire la cartella `CucinatiMaNonFritti95`.
3. Eseguire `avvia.bat`.
4. Attendere il messaggio `Progetto pronto`.
5. Aprire `http://127.0.0.1:8000/` nel browser.
6. Premere `CTRL+C` nel terminale per arrestare il server.

Avvio equivalente da Prompt dei comandi:

```bat
py -3.12 avvia.py
```

## Avvio su Linux o macOS

Aprire il terminale nella cartella del progetto ed eseguire:

```bash
chmod +x avvia.sh
./avvia.sh
```

## Procedura eseguita dal file di avvio

`avvia.py`:

1. controlla la versione di Python;
2. verifica che lo ZIP sia stato estratto completamente;
3. crea l'ambiente virtuale `.venv`;
4. installa Django dalle dipendenze incluse nel progetto;
5. applica le migrazioni;
6. carica i dati iniziali quando il database è vuoto;
7. controlla la configurazione Django;
8. avvia il server locale sulla porta 8000.

## Database

Il progetto usa SQLite. Il file `db.sqlite3` è già popolato con:

- 75 ricette;
- 300 righe di ingredienti;
- 20 regioni;
- 5 libri;
- 79 collegamenti tra libri e ricette.

I dump SQL del primo progetto sono conservati nella cartella `dati_sql`.

Per cancellare le modifiche e ricaricare i dati originali, arrestare il server ed eseguire su Windows:

```bat
.venv\Scripts\python.exe manage.py seed_data --reset
```

Su Linux o macOS:

```bash
.venv/bin/python manage.py seed_data --reset
```

## Controlli

Dopo il primo avvio è possibile verificare il progetto con:

Windows:

```bat
.venv\Scripts\python.exe manage.py check
.venv\Scripts\python.exe manage.py test
```

Linux o macOS:

```bash
.venv/bin/python manage.py check
.venv/bin/python manage.py test
```

## Struttura del progetto

```text
config/              configurazione Django
cucina/              model, view, form, URL, test e file statici
templates/           template HTML Django
dati_sql/            dump SQL del primo progetto
dipendenze/          pacchetti Python per l'installazione offline
licenze/              licenza di Bootstrap
avvia.py              preparazione e avvio del progetto
avvia.bat             avvio rapido su Windows
avvia.sh              avvio rapido su Linux/macOS
manage.py             comandi Django
db.sqlite3            database locale
manuale.pdf           istruzioni per installazione e utilizzo
scelte_progettuali.pdf breve descrizione delle scelte effettuate
```

## Problemi comuni

### Python non è la versione 3.12

Eseguire:

```bat
py -3.12 --version
```

Il risultato deve iniziare con `Python 3.12`.

### L'ambiente virtuale non funziona

Eliminare la cartella `.venv` e ripetere l'avvio.

### La porta 8000 è occupata

Chiudere eventuali server già aperti oppure eseguire:

```bat
.venv\Scripts\python.exe manage.py runserver 127.0.0.1:8001
```

Aprire quindi `http://127.0.0.1:8001/`.

### Compare `No module named config`

Estrarre nuovamente tutto lo ZIP. Le cartelle `config`, `cucina` e `templates` devono trovarsi accanto a `manage.py`.

### L'installazione delle dipendenze non parte

Verificare che la cartella `dipendenze` sia presente. Eliminare `.venv` e ripetere l'avvio.
