"""Prepara e avvia il progetto Django in locale.

Lo script usa solo i file presenti nella cartella del progetto: crea un ambiente
virtuale, installa le dipendenze locali, prepara il database e avvia il server.
"""

from __future__ import annotations

import shutil
import subprocess
import sys
from pathlib import Path

PROJECT_DIR = Path(__file__).resolve().parent
VENV_DIR = PROJECT_DIR / ".venv"
DEPENDENCIES_DIR = PROJECT_DIR / "dipendenze"
REQUIREMENTS_FILE = PROJECT_DIR / "requirements.txt"
MANAGE_FILE = PROJECT_DIR / "manage.py"

def run(command: list[str]) -> None:
    """Esegue un comando dalla cartella principale del progetto."""
    subprocess.run(command, cwd=PROJECT_DIR, check=True)

def venv_python() -> Path:
    """Restituisce il percorso di Python nell'ambiente virtuale."""
    if sys.platform == "win32":
        return VENV_DIR / "Scripts" / "python.exe"
    return VENV_DIR / "bin" / "python"

def python_version(python_executable: Path) -> str | None:
    """Legge la versione principale e secondaria di un interprete Python."""
    try:
        result = subprocess.run(
            [
                str(python_executable),
                "-c",
                "import sys; print(f'{sys.version_info.major}.{sys.version_info.minor}')",
            ],
            cwd=PROJECT_DIR,
            check=True,
            capture_output=True,
            text=True,
        )
    except (OSError, subprocess.CalledProcessError):
        return None
    return result.stdout.strip()

def check_project_files() -> bool:
    """Controlla che lo ZIP sia stato estratto completamente."""
    required_paths = [
        REQUIREMENTS_FILE,
        MANAGE_FILE,
        DEPENDENCIES_DIR,
        PROJECT_DIR / "config",
        PROJECT_DIR / "cucina",
        PROJECT_DIR / "templates",
    ]
    missing = [path.name for path in required_paths if not path.exists()]
    if not missing:
        return True

    print("ERRORE: mancano alcuni file del progetto:")
    for name in missing:
        print(f"- {name}")
    print("Estrarre nuovamente tutto il contenuto dello ZIP.")
    return False

def main() -> int:
    if sys.version_info[:2] != (3, 12):
        print("ERRORE: il progetto deve essere avviato con Python 3.12.")
        print(f"Versione rilevata: {sys.version_info.major}.{sys.version_info.minor}")
        print("Su Windows eseguire avvia.bat oppure: py -3.12 avvia.py")
        print("Su Linux/macOS usare un interprete python3.12.")
        return 1

    if not check_project_files():
        return 1

    python_in_venv = venv_python()

    if VENV_DIR.exists() and python_version(python_in_venv) != "3.12":
        print("Ricreazione dell'ambiente virtuale...")
        shutil.rmtree(VENV_DIR)

    if not python_in_venv.exists():
        print("Creazione dell'ambiente virtuale...")
        run([sys.executable, "-m", "venv", str(VENV_DIR)])

    try:
        print("Installazione delle dipendenze locali...")
        run(
            [
                str(python_in_venv),
                "-m",
                "pip",
                "install",
                "--disable-pip-version-check",
                "--no-index",
                "--find-links",
                str(DEPENDENCIES_DIR),
                "-r",
                str(REQUIREMENTS_FILE),
            ]
        )

        print("Preparazione del database...")
        run([str(python_in_venv), "manage.py", "migrate", "--noinput"])
        run([str(python_in_venv), "manage.py", "seed_data"])
        run([str(python_in_venv), "manage.py", "check"])

        print()
        print("Progetto pronto.")
        print("Aprire nel browser: http://127.0.0.1:8000/")
        print("Premere CTRL+C per arrestare il server.")
        print()
        run([str(python_in_venv), "manage.py", "runserver", "127.0.0.1:8000"])
    except subprocess.CalledProcessError as error:
        print()
        print(f"Avvio interrotto. Codice di errore: {error.returncode}.")
        print("Consultare la sezione 'Problemi comuni' nel README o nel manuale.")
        return error.returncode
    except KeyboardInterrupt:
        print("\nServer arrestato.")

    return 0

if __name__ == "__main__":
    raise SystemExit(main())
