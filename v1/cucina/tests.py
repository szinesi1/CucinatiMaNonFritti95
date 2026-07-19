from django.test import TestCase
from django.urls import reverse

from .models import Book, Ingredient, Recipe, Region


class PublicPagesTests(TestCase):
    @classmethod
    def setUpTestData(cls):
        cls.recipe = Recipe.objects.create(
            number=1,
            title="Ricetta prova",
            recipe_type="primo",
            image="Spaghetti_alla_chitarra.jpg",
        )
        cls.ingredient = Ingredient.objects.create(
            recipe=cls.recipe,
            name="Pomodoro",
            quantity="100 g",
            number=1,
        )
        cls.region = Region.objects.create(code="TST", name="Regione prova", zone="Centro")
        cls.book = Book.objects.create(isbn="123", title="Libro prova", year=2026)

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
