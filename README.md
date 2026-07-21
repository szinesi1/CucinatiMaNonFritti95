# Cucinati Ma Non Fritti 95

## Descrizione

Questo progetto è stato realizzato per il secondo progetto di Programmazione Web, scegliendo l'opzione A: ristrutturazione del primo progetto con Python, Django e Bootstrap.

L'applicazione permette di consultare:

- ricette;
- ingredienti;
- regioni italiane;
- libri collegati alle ricette.

Sono presenti funzioni di ricerca e filtro. Per gli ingredienti sono inoltre disponibili le operazioni di aggiunta, modifica ed eliminazione.

Il progetto funziona interamente in locale. Non è necessario usare un IDE e non serve una connessione a Internet, perché le dipendenze richieste sono già incluse nella cartella del progetto.

## Requisiti

Prima di avviare il progetto verificare di avere:

- Python 3.12;
- un browser web;
- il Prompt dei comandi o un terminale;
- il permesso di scrittura nella cartella estratta.

Per controllare la versione di Python su Windows:

```bat
py -3.12 --version
```

Il risultato deve iniziare con:

```text
Python 3.12
```

## Avvio su Windows

1. Estrarre completamente il file ZIP.
2. Aprire la cartella `CucinatiMaNonFritti95`.
3. Fare doppio clic sul file `avvia.bat`.
4. Attendere il completamento della preparazione del progetto.
5. Quando compare il messaggio `Progetto pronto`, aprire il browser.
6. Collegarsi all'indirizzo:

```text
http://127.0.0.1:8000/
```

Durante il primo avvio può essere necessario attendere qualche minuto, perché viene creato l'ambiente virtuale `.venv` e vengono installati i pacchetti presenti nella cartella `dipendenze`.

La cartella `.venv` non è inclusa nella consegna: viene generata automaticamente al primo avvio.

### Avvio alternativo dal Prompt dei comandi

Aprire il Prompt dei comandi nella cartella del progetto ed eseguire:

```bat
py -3.12 avvia.py
```

## Avvio su Linux o macOS

Aprire il terminale nella cartella del progetto ed eseguire:

```bash
chmod +x avvia.sh
./avvia.sh
```
<<<<<<< HEAD

Al termine aprire nel browser:

```text
http://127.0.0.1:8000/
```

## Cosa esegue il file di avvio

Il file `avvia.py` prepara automaticamente il progetto. In particolare:

1. controlla che sia disponibile Python 3.12;
2. verifica che le cartelle principali siano presenti;
3. crea l'ambiente virtuale `.venv`, se non esiste;
4. installa Django e le dipendenze locali;
5. applica le migrazioni del database;
6. carica i dati iniziali se il database è vuoto;
7. controlla la configurazione Django;
8. avvia il server locale sulla porta 8000.

Non è quindi necessario installare manualmente Django o altri pacchetti.

## Arresto del progetto

Per arrestare il server:

1. tornare alla finestra del Prompt dei comandi o del terminale;
2. premere `CTRL+C`;
3. attendere l'arresto del server;
4. chiudere la finestra.

## Database

Il progetto usa SQLite. Il file `db.sqlite3` è già presente e contiene:

- 75 ricette;
- 300 righe di ingredienti;
- 20 regioni;
- 5 libri;
- 79 collegamenti tra libri e ricette.

I dump SQL del primo progetto sono conservati nella cartella `dati_sql`.

## Ripristino dei dati iniziali

Per eliminare le modifiche effettuate durante le prove e ripristinare i dati originali, arrestare prima il server.

Su Windows eseguire:

```bat
.venv\Scripts\python.exe manage.py seed_data --reset
```

Su Linux o macOS eseguire:

```bash
.venv/bin/python manage.py seed_data --reset
```

## Controllo del progetto

Dopo il primo avvio è possibile verificare la configurazione e avviare i test automatici.

Su Windows:

```bat
.venv\Scripts\python.exe manage.py check
.venv\Scripts\python.exe manage.py test
```

Su Linux o macOS:

```bash
.venv/bin/python manage.py check
.venv/bin/python manage.py test
```

Il comando `check` controlla la configurazione del progetto Django.

Il comando `test` esegue i test previsti per le pagine principali, i filtri e le operazioni sugli ingredienti.

## Struttura principale

```text
config/                   configurazione del progetto Django
cucina/                   model, view, form, URL, test e file statici
templates/                template HTML
dati_sql/                 dump SQL del primo progetto
dipendenze/               pacchetti Python per l'installazione offline
licenze/                  licenze dei componenti utilizzati
avvia.py                  preparazione e avvio del progetto
avvia.bat                 avvio rapido su Windows
avvia.sh                  avvio rapido su Linux e macOS
manage.py                 comandi Django
db.sqlite3                database locale
manuale.pdf               manuale per installazione e utilizzo
scelte_progettuali.pdf    documento sulle scelte effettuate
```

## Problemi comuni

### Python 3.12 non viene trovato

Eseguire:

```bat
py -3.12 --version
```

Se il comando non restituisce una versione di Python 3.12, verificare che Python 3.12 sia installato e disponibile nel sistema.

### L'ambiente virtuale non funziona

1. Arrestare il server.
2. Eliminare soltanto la cartella `.venv`.
3. Avviare nuovamente `avvia.bat`.

La cartella verrà ricreata automaticamente.

### La porta 8000 è occupata

Chiudere eventuali server Django già aperti.

In alternativa, dopo avere eseguito almeno una volta `avvia.bat`, avviare il progetto sulla porta 8001:

```bat
.venv\Scripts\python.exe manage.py runserver 127.0.0.1:8001
```

Aprire quindi:

```text
http://127.0.0.1:8001/
```

### Compare l'errore `No module named config`

Lo ZIP potrebbe non essere stato estratto completamente.

Verificare che le cartelle `config`, `cucina` e `templates` si trovino nella stessa cartella di `manage.py`.

Se necessario, eliminare la cartella estratta e ripetere l'estrazione dello ZIP.

### L'installazione delle dipendenze non parte

Verificare che la cartella `dipendenze` sia presente accanto a `avvia.py`.

Se la cartella è presente:

1. eliminare `.venv`;
2. avviare nuovamente `avvia.bat`.

### La pagina non si apre nel browser

Verificare che il terminale mostri il server in esecuzione e che non siano presenti messaggi di errore.

Aprire manualmente:

```text
http://127.0.0.1:8000/
```

Non chiudere il terminale mentre il sito è in uso.
=======
>>>>>>> 5df8f3ac832b6d9f2552a4a761eba4f407b44aba
