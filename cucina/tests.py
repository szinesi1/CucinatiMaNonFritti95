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
        Ingredient.objects.create(
            recipe=cls.second_recipe,
            name="Pomodoro",
            quantity="50 g",
            number=1,
        )
        Ingredient.objects.create(
            recipe=cls.recipe,
            name="Sale",
            quantity="q.b.",
            number=2,
        )
        cls.region = Region.objects.create(
            code="TST",
            name="Regione prova",
            zone="Centro",
        )
        RegionalRecipe.objects.create(region=cls.region, recipe=cls.recipe)
        cls.second_region = Region.objects.create(
            code="DUE",
            name="Regione con due ricette",
            zone="Nord",
        )
        RegionalRecipe.objects.create(region=cls.second_region, recipe=cls.recipe)
        RegionalRecipe.objects.create(region=cls.second_region, recipe=cls.second_recipe)
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
        Publication.objects.create(
            book=cls.book,
            page_number=30,
            recipe=cls.second_recipe,
        )
        cls.second_book = Book.objects.create(
            isbn="456",
            title="Manuale dolci",
            year=2024,
        )
        Publication.objects.create(
            book=cls.second_book,
            page_number=5,
            recipe=cls.second_recipe,
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
        response = self.client.post(
            reverse("recipe_list"),
            {"search": "Ricetta", "tipo": "primo"},
        )
        self.assertContains(response, "Ricetta prova")
        self.assertNotContains(response, "Dolce prova")

    def test_recipe_antipasto_filter_and_clear_stay_on_recipe_page(self):
        antipasto = Recipe.objects.create(
            number=3,
            title="Antipasto prova",
            recipe_type="antipasto",
            image="",
        )
        response = self.client.post(
            reverse("recipe_list"),
            {"form_action": "filter", "tipo": ["antipasto"]},
        )
        self.assertEqual(response.status_code, 200)
        self.assertContains(response, antipasto.title)
        self.assertNotContains(response, self.recipe.title)

        response = self.client.post(
            reverse("recipe_list"),
            {"form_action": "clear"},
        )
        self.assertRedirects(response, reverse("recipe_list"))
        response = self.client.get(reverse("recipe_list"))
        self.assertContains(response, antipasto.title)
        self.assertContains(response, self.recipe.title)

    def test_ingredient_search(self):
        response = self.client.post(reverse("ingredient_list"), {"search": "Pomo", "ordina": "nome", "direzione": "asc"})
        self.assertContains(response, "Pomodoro")

    def test_region_filters_sorting_and_open_panel(self):
        response = self.client.post(
            reverse("region_list"),
            {
                "zona": "Nord",
                "ricette_min": "2",
                "ordina": "ricette",
                "direzione": "desc",
            },
        )
        self.assertContains(response, "Regione con due ricette")
        self.assertNotContains(response, "Regione prova")
        self.assertContains(response, "Filtri applicati:")
        self.assertContains(response, "region-filters open")
        self.assertEqual(response.context["filtered_count"], 1)

    def test_book_filters_by_recipe_pages_and_recipe_count(self):
        response = self.client.post(reverse("book_list"), {"ricetta": "Ricetta prova", "ordina": "titolo", "direzione": "asc"})
        self.assertContains(response, "Libro prova")
        self.assertNotContains(response, "Manuale dolci")

        response = self.client.post(
            reverse("book_list"),
            {"pagine_max": "10", "ricette_max": "1"},
        )
        self.assertContains(response, "Manuale dolci")
        self.assertNotContains(response, "Libro prova")

        response = self.client.post(
            reverse("book_list"),
            {"ordina": "ricette", "direzione": "desc"},
        )
        books = list(response.context["books"])
        self.assertEqual(books[0], self.book)
        self.assertEqual(books[0].recipe_count, 2)

    def test_ingredient_table_counts_filter_and_sorting(self):
        response = self.client.post(
            reverse("ingredient_list"),
            {"ricette_min": "2", "ordina": "ricette", "direzione": "desc"},
        )
        self.assertContains(response, "Pomodoro")
        self.assertNotContains(response, ">Sale<")
        self.assertContains(response, "Risultati: <strong>1</strong> su")
        self.assertContains(response, "table-ingredienti")
        self.assertEqual(response.context["ingredients"][0]["recipe_count"], 2)

    def test_navigation_links_do_not_expose_search_parameters(self):
        response = self.client.get(reverse("region_detail", args=[self.region.code]))
        self.assertEqual(response.context["back_url"], reverse("region_list"))

        response = self.client.get(reverse("recipe_detail", args=[self.recipe.number]))
        self.assertEqual(response.context["back_url"], reverse("recipe_list"))
        self.assertNotContains(response, "?next=")

    def test_region_recipes_are_clickable(self):
        response = self.client.get(reverse("region_detail", args=[self.region.code]))
        self.assertContains(
            response,
            reverse("recipe_detail", args=[self.recipe.number]),
        )
        self.assertContains(response, "Ricetta prova")

    def test_detail_back_link_uses_internal_referer(self):
        recipe_url = reverse("recipe_detail", args=[self.recipe.number])
        region_url = reverse("region_detail", args=[self.region.code])
        response = self.client.get(region_url, HTTP_REFERER=f"http://testserver{recipe_url}")
        self.assertContains(response, f'href="{recipe_url}"')


    def test_detail_back_link_survives_page_refresh(self):
        recipe_url = reverse("recipe_detail", args=[self.recipe.number])
        region_url = reverse("region_detail", args=[self.region.code])

        first_response = self.client.get(
            region_url,
            HTTP_REFERER=f"http://testserver{recipe_url}",
        )
        self.assertContains(first_response, f'href="{recipe_url}"')

        refreshed_response = self.client.get(
            region_url,
            HTTP_REFERER=f"http://testserver{region_url}",
        )
        self.assertContains(refreshed_response, f'href="{recipe_url}"')

    def test_invalid_book_year_is_ignored(self):
        response = self.client.post(reverse("book_list"), {"anno": "non-numerico"})
        self.assertEqual(response.status_code, 200)
        self.assertContains(response, self.book.title)

    def test_detail_back_link_rejects_external_referer(self):
        region_url = reverse("region_detail", args=[self.region.code])
        response = self.client.get(region_url, HTTP_REFERER="https://example.com/phishing")
        self.assertContains(response, f'href="{reverse("region_list")}"')

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

        self.assertEqual(Recipe.objects.count(), 300)
        self.assertEqual(Ingredient.objects.count(), 1608)
        self.assertEqual(Region.objects.count(), 20)
        self.assertEqual(Book.objects.count(), 15)
        self.assertEqual(Publication.objects.count(), 1045)
        self.assertIn("Caricati 300 ricette", output.getvalue())

    def test_seeded_books_have_continuous_pages(self):
        call_command("seed_data")
        for book in Book.objects.all():
            pages = list(
                Publication.objects.filter(book=book)
                .order_by("page_number")
                .values_list("page_number", flat=True)
            )
            self.assertEqual(pages, list(range(1, len(pages) + 1)))

    def test_seeded_recipe_images_are_unique_and_present(self):
        from django.conf import settings
        call_command("seed_data")
        images = list(Recipe.objects.values_list("image", flat=True))
        self.assertEqual(len(images), len(set(images)))
        image_dir = settings.BASE_DIR / "cucina" / "static" / "cucina" / "img" / "ricette"
        for image in images:
            self.assertTrue((image_dir / image).is_file(), image)
