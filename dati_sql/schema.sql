CREATE TABLE Ricette (
    numero INT PRIMARY KEY,
    titolo VARCHAR(255),
    tipo VARCHAR(50),
    immagine TEXT
);

CREATE TABLE Ingredienti (
    idIngrediente INT AUTO_INCREMENT PRIMARY KEY,
    numeroRicetta INT,
    ingrediente VARCHAR(255),
    quantita VARCHAR(50),
    numero INT
);

CREATE TABLE Libri (
    codISBN VARCHAR(20) PRIMARY KEY,
    titolo VARCHAR(255),
    anno INT
);

CREATE TABLE Pagine (
    libro VARCHAR(20),
    numeroPagina INT,
    numeroRicetta INT,
    PRIMARY KEY (libro, numeroPagina)
);

CREATE TABLE Regioni (
    cod VARCHAR(3) PRIMARY KEY,
    nome VARCHAR(100),
    zona VARCHAR(20)
);

CREATE TABLE RicettaRegionale (
    cod VARCHAR(3),
    numeroRicetta INT,
    PRIMARY KEY (cod, numeroRicetta)
);