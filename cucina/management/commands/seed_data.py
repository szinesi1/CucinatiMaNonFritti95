"""Carica nel database locale i dump SQL del primo progetto."""

from pathlib import Path

from django.core.management.base import BaseCommand, CommandError
from django.db import transaction

from cucina.models import Book, Ingredient, Publication, Recipe, Region, RegionalRecipe


def parse_insert_values(path: Path) -> list[list[object]]:
    """Parse the VALUES tuples used by the original SQL dump."""
    text = path.read_text(encoding="utf-8")
    marker = text.upper().find("VALUES")
    if marker < 0:
        raise CommandError(f"VALUES non trovato in {path.name}")

    text = text[marker + len("VALUES"):]
    rows: list[list[object]] = []
    row: list[object] | None = None
    token = ""
    in_string = False
    index = 0

    def push_token() -> None:
        nonlocal token
        assert row is not None
        raw = token.strip()
        if raw.upper() == "NULL":
            row.append(None)
        elif raw == "":
            row.append("")
        else:
            try:
                row.append(int(raw))
            except ValueError:
                row.append(raw)
        token = ""

    while index < len(text):
        char = text[index]

        if not in_string and char == "-" and index + 1 < len(text) and text[index + 1] == "-":
            newline = text.find("\n", index)
            index = len(text) if newline < 0 else newline + 1
            continue

        if in_string:
            if char == "'":
                if index + 1 < len(text) and text[index + 1] == "'":
                    token += "'"
                    index += 2
                    continue
                in_string = False
            else:
                token += char
            index += 1
            continue

        if char == "'":
            in_string = True
        elif char == "(":
            row = []
            token = ""
        elif char == "," and row is not None:
            push_token()
        elif char == ")" and row is not None:
            push_token()
            rows.append(row)
            row = None
            token = ""
        elif row is not None:
            token += char

        index += 1

    return rows


class Command(BaseCommand):
    help = "Carica nel database Django i dati SQL del progetto PHP originale."

    def add_arguments(self, parser):
        parser.add_argument(
            "--reset",
            action="store_true",
            help="Elimina i dati esistenti prima di ricaricarli.",
        )

    @transaction.atomic
    def handle(self, *args, **options):
        seed_dir = Path(__file__).resolve().parents[3] / "dati_sql"

        if options["reset"]:
            Publication.objects.all().delete()
            RegionalRecipe.objects.all().delete()
            Ingredient.objects.all().delete()
            Recipe.objects.all().delete()
            Region.objects.all().delete()
            Book.objects.all().delete()

        if Recipe.objects.exists() and not options["reset"]:
            self.stdout.write(self.style.WARNING("Dati già presenti: caricamento saltato."))
            return

        recipe_rows = parse_insert_values(seed_dir / "ricette.sql")
        ingredient_rows = parse_insert_values(seed_dir / "ingredienti.sql")
        book_rows = parse_insert_values(seed_dir / "libri.sql")
        page_rows = parse_insert_values(seed_dir / "pagine.sql")
        region_rows = parse_insert_values(seed_dir / "regioni.sql")
        regional_rows = parse_insert_values(seed_dir / "ricettaRegionale.sql")

        Recipe.objects.bulk_create(
            [
                Recipe(number=row[0], title=row[1], recipe_type=row[2], image=row[3])
                for row in recipe_rows
            ]
        )
        Region.objects.bulk_create(
            [Region(code=row[0], name=row[1], zone=row[2]) for row in region_rows]
        )
        Book.objects.bulk_create(
            [Book(isbn=row[0], title=row[1], year=row[2]) for row in book_rows]
        )
        Ingredient.objects.bulk_create(
            [
                Ingredient(
                    recipe_id=row[0],
                    number=row[1],
                    name=row[2],
                    quantity=row[3],
                )
                for row in ingredient_rows
            ]
        )
        RegionalRecipe.objects.bulk_create(
            [RegionalRecipe(region_id=row[0], recipe_id=row[1]) for row in regional_rows]
        )
        Publication.objects.bulk_create(
            [
                Publication(book_id=row[0], page_number=row[1], recipe_id=row[2])
                for row in page_rows
            ]
        )

        self.stdout.write(
            self.style.SUCCESS(
                "Caricati "
                f"{len(recipe_rows)} ricette, "
                f"{len(ingredient_rows)} ingredienti, "
                f"{len(region_rows)} regioni, "
                f"{len(book_rows)} libri e "
                f"{len(page_rows)} collegamenti libro/ricetta."
            )
        )
