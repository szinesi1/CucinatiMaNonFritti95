# CucinatiMaNonFritti 95
Progetto realizzato per il corso di **Programmazione Web – A.A. 2025/2026**.  
L’applicazione permette di consultare, ricercare e gestire ricette, ingredienti, libri di cucina e regioni tramite un’interfaccia web sviluppata in **PHP, MySQL, HTML, CSS e JavaScript**.

---

## 📂 Struttura del progetto
`struttura teorica, non definitiva`

```
/css/          → fogli di stile (palette verde)
/js/           → script JavaScript
/php/          → connessione al DB e funzioni comuni
/ricerca/      → pagine di ricerca (Ricette, Regioni, Libri, Pagine, Ingredienti)
/crud/         → CRUD completo per la tabella Ingrediente
/img/          → immagini (se presenti)
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
- **RicettaPubblicata**

Lo schema è definito nel file `schema.sql` (non incluso nel repository se ignorato tramite `.gitignore`).

---

## 🔍 Funzionalità principali

### ✔ Ricerca
Per ogni entità (Ricetta, Regione, Libro, Pagina, Ingrediente) è disponibile una pagina di ricerca con:

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

- **PHP 8+**  
- **MySQL / MariaDB**  
- **HTML5**  
- **CSS3**  
- **JavaScript**  
- **GitHub** per versionamento  

---

## ▶️ Installazione

1. Clonare il repository:
```
git clone https://github.com/tuo-username/nome-progetto.git
```
2. Importare il database tramite `schema.sql` e `dati.sql` (se fornito).  
3. Configurare `php/connessione.php` con le proprie credenziali MySQL.  
4. Avviare il server locale (XAMPP, WAMP, ecc.).  
5. Aprire nel browser:
```
http://localhost/nome-progetto/
```

---

## 👩‍💻 Autori 

### Push&Pray
- *szinesi1*
- *gaiarota51*  
- *M4urixi0*

---

## 📄 Licenza

Questo progetto è distribuito sotto licenza **MIT**.

