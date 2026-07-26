"""Modelli che rappresentano i dati del primo progetto."""

from django.db import models

class Recipe(models.Model):
    TYPE_CHOICES = [
        ("antipasto", "Antipasto"),
        ("primo", "Primo"),
        ("secondo", "Secondo"),
        ("contorno", "Contorno"),
        ("dolce", "Dolce"),
    ]

    number = models.PositiveIntegerField(primary_key=True, db_column="numero")
    title = models.CharField(max_length=255, db_column="titolo")
    recipe_type = models.CharField(max_length=50, choices=TYPE_CHOICES, db_column="tipo")
    image = models.CharField(max_length=255, blank=True, db_column="immagine")

    class Meta:
        db_table = "Ricette"
        ordering = ["title"]

    def __str__(self) -> str:
        return self.title

class Ingredient(models.Model):
    id = models.AutoField(primary_key=True, db_column="idIngrediente")
    recipe = models.ForeignKey(
        Recipe,
        on_delete=models.CASCADE,
        related_name="ingredients",
        db_column="numeroRicetta",
    )
    name = models.CharField(max_length=255, db_column="ingrediente")
    quantity = models.CharField(max_length=50, db_column="quantita")
    number = models.PositiveIntegerField(db_column="numero")

    class Meta:
        db_table = "Ingredienti"
        ordering = ["number", "id"]

    def __str__(self) -> str:
        return f"{self.name} ({self.recipe})"

class Book(models.Model):
    isbn = models.CharField(max_length=20, primary_key=True, db_column="codISBN")
    title = models.CharField(max_length=255, db_column="titolo")
    year = models.PositiveIntegerField(db_column="anno")

    class Meta:
        db_table = "Libri"
        ordering = ["title"]

    def __str__(self) -> str:
        return self.title

class Publication(models.Model):
    book = models.ForeignKey(
        Book,
        on_delete=models.CASCADE,
        related_name="publications",
        db_column="libro",
    )
    page_number = models.PositiveIntegerField(db_column="numeroPagina")
    recipe = models.ForeignKey(
        Recipe,
        on_delete=models.CASCADE,
        related_name="publications",
        db_column="numeroRicetta",
    )

    class Meta:
        db_table = "Pagine"
        constraints = [
            models.UniqueConstraint(
                fields=["book", "page_number"],
                name="unique_book_page",
            )
        ]
        ordering = ["page_number"]

    def __str__(self) -> str:
        return f"{self.book} - p. {self.page_number}"

class Region(models.Model):
    code = models.CharField(max_length=3, primary_key=True, db_column="cod")
    name = models.CharField(max_length=100, db_column="nome")
    zone = models.CharField(max_length=20, db_column="zona")

    class Meta:
        db_table = "Regioni"
        ordering = ["name"]

    def __str__(self) -> str:
        return self.name

class RegionalRecipe(models.Model):
    region = models.ForeignKey(
        Region,
        on_delete=models.CASCADE,
        related_name="regional_recipes",
        db_column="cod",
    )
    recipe = models.ForeignKey(
        Recipe,
        on_delete=models.CASCADE,
        related_name="regional_links",
        db_column="numeroRicetta",
    )

    class Meta:
        db_table = "RicettaRegionale"
        constraints = [
            models.UniqueConstraint(
                fields=["region", "recipe"],
                name="unique_region_recipe",
            )
        ]

    def __str__(self) -> str:
        return f"{self.region}: {self.recipe}"
