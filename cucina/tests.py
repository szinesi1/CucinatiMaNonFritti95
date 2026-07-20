"""Test delle pagine, del CRUD e del caricamento dei dati."""

from io import StringIO

from django.core.management import call_command
from django.test import TestCase
from django.urls import reverse

from .models import Book, Ingredient, Publication, Recipe, Region, RegionalRecipe


class PublicPagesTests(TestCase):
    """Controlla le pagine pubbliche e le operazioni sugli ingredienti."""

    @classmethod
    def setUpTestData(cls):
        cls.recipe = Recipe.objects.create(
            number=1,
            title="Ricetta prova",
            recipe_type="primo",
            image="Spaghetti_alla_chitarra.jpg",
        )
        cls.second_recipe = Recipe.objects.create(
            number=2,
            title="Dolce prova",
            recipe_type="dolce",
            image="",
        )
        cls.ingredient = Ingredient.objects.create(
            recipe=cls.recipe,
            name="Pomodoro",
            quantity="100 g",
            number=1,
        )
        cls.region = Region.objects.create(
            code="TST",
            name="Regione prova",
            zone="Centro",
        )
        RegionalRecipe.objects.create(region=cls.region, recipe=cls.recipe)
        cls.book = Book.objects.create(
            isbn="123",
            title="Libro prova",
            year=2026,
        )
        Publication.objects.create(
            book=cls.book,
            page_number=10,
            recipe=cls.recipe,
        )

    def test_main_pages_render(self):
        urls = [
            reverse("home"),
            reverse("recipe_list"),
            reverse("recipe_detail", args=[self.recipe.number]),
            reverse("ingredient_list"),
            reverse("ingredient_detail", args=[self.ingredient.name]),
            reverse("region_list"),
            reverse("region_detail", args=[self.region.code]),
            reverse("book_list"),
            reverse("book_detail", args=[self.book.isbn]),
        ]
        for url in urls:
            with self.subTest(url=url):
                self.assertEqual(self.client.get(url).status_code, 200)

    def test_recipe_search_and_type_filter(self):
        response = self.client.get(
            reverse("recipe_list"),
            {"search": "Ricetta", "tipo": "primo"},
        )
        self.assertContains(response, "Ricetta prova")
        self.assertNotContains(response, "Dolce prova")

    def test_ingredient_search(self):
        response = self.client.get(reverse("ingredient_list"), {"search": "Pomo"})
        self.assertContains(response, "Pomodoro")

    def test_create_edit_delete_ingredient(self):
        create_url = reverse("ingredient_create", args=[self.recipe.number])
        response = self.client.post(
            create_url,
            {"name": "Basilico", "quantity": "q.b."},
        )
        self.assertEqual(response.status_code, 302)
        basil = Ingredient.objects.get(name="Basilico")

        edit_url = reverse(
            "ingredient_edit",
            args=[self.recipe.number, basil.id],
        )
        response = self.client.post(
            edit_url,
            {"name": "Basilico fresco", "quantity": "2 foglie"},
        )
        self.assertEqual(response.status_code, 302)
        basil.refresh_from_db()
        self.assertEqual(basil.name, "Basilico fresco")

        response = self.client.post(
            reverse("ingredient_delete"),
            {"idIngrediente": basil.id, "numero": self.recipe.number},
        )
        self.assertEqual(response.status_code, 302)
        self.assertFalse(Ingredient.objects.filter(id=basil.id).exists())

    def test_delete_requires_post(self):
        response = self.client.get(reverse("ingredient_delete"))
        self.assertRedirects(response, reverse("recipe_list"))
        self.assertTrue(Ingredient.objects.filter(id=self.ingredient.id).exists())

    def test_bootstrap_is_served_locally(self):
        response = self.client.get(reverse("home"))
        self.assertContains(
            response,
            "cucina/librerie/bootstrap/css/bootstrap.min.css",
        )
        self.assertContains(
            response,
            "cucina/librerie/bootstrap/js/bootstrap.bundle.min.js",
        )
        self.assertNotContains(response, "cdn.jsdelivr.net")


class InitialDataTests(TestCase):
    """Verifica che i dump SQL possano ricreare il database locale."""

    def test_seed_data_command(self):
        output = StringIO()
        call_command("seed_data", stdout=output)

        self.assertEqual(Recipe.objects.count(), 75)
        self.assertEqual(Ingredient.objects.count(), 300)
        self.assertEqual(Region.objects.count(), 20)
        self.assertEqual(Book.objects.count(), 5)
        self.assertEqual(Publication.objects.count(), 79)
        self.assertIn("Caricati 75 ricette", output.getvalue())
