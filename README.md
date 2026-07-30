# Cucinati Ma Non Fritti 95

## Secondo progetto di Programmazione Web

Il progetto applica il **Caso A - Ristrutturazione del primo progetto** con:

- **Python 3.12**;
- **Django**;
- **Bootstrap**, incluso localmente;
- esecuzione in locale da riga di comando.

L'applicazione riorganizza il primo progetto in una struttura Django e permette di consultare ricette italiane, ingredienti, regioni e libri di cucina.

## Avvio rapido

### Windows

1. Estrarre completamente il file ZIP.
2. Aprire il Prompt dei comandi nella cartella che contiene `manage.py`.
3. Eseguire:

```bat
avvia.bat
```

4. Aprire `http://127.0.0.1:8000/` nel browser.
5. Premere `CTRL+C` nel Prompt dei comandi per arrestare il server.

### Linux o macOS

```bash
chmod +x avvia.sh
./avvia.sh
```

Lo script crea l'ambiente virtuale, installa le dipendenze presenti nella cartella `dipendenze`, prepara il database ed esegue il server. Non serve un IDE e non serve una connessione Internet per installare i pacchetti Python inclusi nella consegna.

Le istruzioni complete di installazione e avvio sono disponibili in **`MANUALE_UTENTE.pdf`**.

## Funzioni disponibili

- elenco e dettaglio delle ricette;
- ricerca, filtri avanzati e ordinamento;
- consultazione di ingredienti, regioni e libri;
- collegamenti tra le entità correlate;
- inserimento, modifica ed eliminazione degli ingredienti di una ricetta;
- paginazione con conservazione dei filtri;
- ritorno alla reale pagina interna di provenienza;
- interfaccia responsive per desktop, tablet e smartphone.

## Scelte tecniche principali

- architettura Django con model, view, template, URL e form separati;
- database locale SQLite, senza server aggiuntivi;
- Bootstrap distribuito nel progetto, senza CDN;
- CSS e JavaScript separati dai template HTML;
- filtri gestiti tramite POST e sessione;
- dati iniziali ricostruibili dai file SQL con il comando `seed_data`;
- dipendenze Python incluse come file `.whl` per l'installazione offline.

Il documento **`SCELTE_PROGETTUALI.pdf`** descrive in modo sintetico le motivazioni delle scelte adottate.

## Struttura della consegna

```text
CucinatiMaNonFritti95/
├── config/                     configurazione del progetto Django
├── cucina/                     applicazione principale
│   ├── management/commands/    caricamento dei dati iniziali
│   ├── migrations/             struttura del database
│   └── static/cucina/          CSS, JavaScript, Bootstrap e immagini
├── dati_sql/                   dati del primo progetto usati da seed_data
├── dipendenze/                 pacchetti Python per installazione offline
├── licenze/                    licenza della libreria Bootstrap
├── templates/                  template HTML
├── MANUALE_UTENTE.pdf          manuale per l'utente generico
├── SCELTE_PROGETTUALI.pdf      documento sintetico delle scelte
├── MODELLO_EMAIL_CONSEGNA.txt  traccia per l'email di consegna
├── avvia.bat                   avvio da riga di comando su Windows
├── avvia.py                    installazione e avvio automatici
├── avvia.sh                    avvio da riga di comando su Linux/macOS
├── db.sqlite3                  database locale già popolato
├── manage.py                   comando Django
└── requirements.txt            versioni delle dipendenze
```

Le cartelle elencate sono necessarie al funzionamento, alla ricostruzione dei dati, all'installazione offline o alla documentazione delle librerie impiegate.

## Avvio manuale e verifica

Dopo la creazione dell'ambiente virtuale e l'installazione delle dipendenze:

```bash
python manage.py migrate
python manage.py seed_data
python manage.py check
python manage.py test
python manage.py runserver 127.0.0.1:8000
```

La versione consegnata supera **19 test automatici**. I test verificano pagine, filtri, ordinamenti, paginazione, collegamenti, navigazione di ritorno, operazioni sugli ingredienti, caricamento locale di Bootstrap e ricostruzione dei dati.

## Compatibilità

Il progetto è destinato a **Python 3.12**, come richiesto dalla consegna. Le dipendenze sono fissate in `requirements.txt` e sono incluse nella cartella `dipendenze`.

La verifica finale non richiede Eclipse, Visual Studio, Visual Studio Code o altri ambienti di sviluppo.
