# CucinatiMaNonFritti 95
Progetto realizzato per il corso di **Programmazione Web – A.A. 2025/2026**.  
L’applicazione permette di consultare, ricercare e gestire ricette, ingredienti, libri di cucina e regioni tramite un’interfaccia web sviluppata in **PHP, MySQL, HTML, CSS e JavaScript**.

---

## 📂 Struttura del progetto
`struttura teorica, non definitiva`

```
/database/     → database con schema e singole tabelle
/css/          → fogli di stile (palette verde)
/js/           → script JavaScript
/includes/     → connessione al db
/interface/    → funzioni comuni (header, navbar e footer)
/pages/        → pagine di ricerca (Ricette, Regioni, Libri, Pagine, Ingredienti)
/img/          → immagini
index.php      → homepage con template Interfaccia 3
```
---

## 🗄️ Database: DB4 – Ricettario

Il database è composto dalle seguenti tabelle:

- **Regione**
- **Ricetta**
- **RicettaRegionale**
- **Ingrediente** *(tabella con CRUD)*
- **Libro**
- **Pagina**

Lo schema è definito nel file `schema.sql` presente nella cartella dataset.

### Modifiche rispetto allo schema iniziale:
Sono stati aggiunti dati come:
- Zona dell'Italia associata alle Regioni;
- Immagini associate a Ricette.
---

## 🔍 Funzionalità principali

### ✔ Ricerca
Per ogni entità (Ricetta, Regione, Libro, Ingrediente) è disponibile una pagina di ricerca con:

- filtri multipli  
- risultati tabellari  
- link alle entità collegate  
- conteggi (es. numero ricette per libro)

### ✔ CRUD Ingrediente
La tabella **Ingrediente** include:

- **Create** – aggiunta di un nuovo ingrediente  
- **Read** – lista ingredienti con ricerca  
- **Update** – modifica ingrediente  
- **Delete** – eliminazione ingrediente  

---

## 🎨 Interfaccia

Il progetto utilizza **Interfaccia 3**, come richiesto dal docente:

- Header  
- Navbar  
- Filtro di ricerca  
- Contenuto  
- Footer  

La palette scelta è basata su tonalità di verde.

---

## 🛠️ Tecnologie utilizzate

- **PHP 8+** per la logica server-side
- **MySQL** come database
- **HTML5** per la struttura delle pagine
- **CSS3** per lo stile e il layout
- **JavaScript** per interattivita' (es. popup per l'eliminazione di ingredienti dal )
- **GitHub** per versionamento

---

## ▶️ Avviamento sito

Aprire il link seguente nel browser:
https://cucinatimanonfritti.site.je/index.php

In caso di non apertura del link provare a disattivare il wifi e collegarsi con i dati mobili (al momento non abbiamo la certificazione funzionante su tutti i browser) oppure cambiare browser

---

## 👩‍💻 Autori 

### Push&Pray
- *szinesi1*
- *gaiarota51*  
- *M4urixi0*

---

## 📄 Licenza

Questo progetto è distribuito sotto licenza **MIT**.

