from collections import OrderedDict
from urllib.parse import urlencode

from django.db import transaction
from django.db.models import Count, Max, Q
from django.http import HttpRequest, HttpResponse, HttpResponseBadRequest
from django.shortcuts import get_object_or_404, redirect, render
from django.urls import reverse
from django.utils.http import url_has_allowed_host_and_scheme

from .forms import IngredientCreateForm, IngredientEditForm
from .models import Book, Ingredient, Publication, Recipe, Region, RegionalRecipe


RECIPE_TYPES = OrderedDict(
    [
        ("antipasto", "Antipasti"),
        ("primo", "Primi"),
        ("secondo", "Secondi"),
        ("contorno", "Contorni"),
        ("dolce", "Dolci"),
    ]
)
ZONES = ["Nord", "Centro", "Sud", "Isole"]
ALPHABET = [chr(code) for code in range(ord("A"), ord("Z") + 1)]


def _safe_referer(request: HttpRequest, fallback: str) -> str:
    referer = request.META.get("HTTP_REFERER", "")
    if referer and url_has_allowed_host_and_scheme(
        referer,
        allowed_hosts={request.get_host()},
        require_https=request.is_secure(),
    ):
        return referer
    return fallback


def home(request: HttpRequest) -> HttpResponse:
    return render(request, "cucina/home.html", {"active_section": "home"})


def recipe_list(request: HttpRequest) -> HttpResponse:
    search = request.GET.get("search", "").strip()
    selected_types = request.GET.getlist("tipo")

    base_query = Recipe.objects.all()
    if search:
        base_query = base_query.filter(title__icontains=search)

    sections = []
    for key, label in RECIPE_TYPES.items():
        if selected_types and key not in selected_types:
            recipes = Recipe.objects.none()
        else:
            recipes = base_query.filter(recipe_type=key).order_by("title")
        sections.append({"key": key, "label": label, "recipes": recipes})

    return render(
        request,
        "cucina/recipe_list.html",
        {
            "active_section": "ricette",
            "search": search,
            "selected_types": selected_types,
            "recipe_types": RECIPE_TYPES.items(),
            "sections": sections,
        },
    )


def recipe_detail(request: HttpRequest, number: int) -> HttpResponse:
    recipe = get_object_or_404(Recipe, number=number)

    source = request.GET.get("from")
    if source == "regione" and request.GET.get("cod"):
        back_url = reverse("region_detail", args=[request.GET["cod"]])
    elif source == "ingrediente" and request.GET.get("ingrediente"):
        back_url = reverse("ingredient_detail", args=[request.GET["ingrediente"]])
    elif source == "libro" and request.GET.get("isbn"):
        back_url = reverse("book_detail", args=[request.GET["isbn"]])
    else:
        back_url = _safe_referer(request, reverse("recipe_list"))

    region_link = (
        RegionalRecipe.objects.select_related("region")
        .filter(recipe=recipe)
        .first()
    )
    publications = Publication.objects.select_related("book").filter(recipe=recipe)

    return render(
        request,
        "cucina/recipe_detail.html",
        {
            "active_section": "ricette",
            "recipe": recipe,
            "images": [recipe.image] if recipe.image else [],
            "ingredients": recipe.ingredients.all(),
            "region": region_link.region if region_link else None,
            "publications": publications,
            "back_url": back_url,
        },
    )


def ingredient_list(request: HttpRequest) -> HttpResponse:
    search = request.GET.get("search", "").strip()
    selected_letters = [letter.upper() for letter in request.GET.getlist("lettera")]

    query = Ingredient.objects.values("name").annotate(count=Count("id"))
    if search:
        query = query.filter(name__icontains=search)
    if selected_letters:
        letter_query = Q()
        for letter in selected_letters:
            letter_query |= Q(name__istartswith=letter)
        query = query.filter(letter_query)
    query = query.order_by("name")

    grouped: OrderedDict[str, list[dict]] = OrderedDict()
    for item in query:
        initial = item["name"][:1].upper()
        grouped.setdefault(initial, []).append(item)

    return render(
        request,
        "cucina/ingredient_list.html",
        {
            "active_section": "ingredienti",
            "search": search,
            "selected_letters": selected_letters,
            "alphabet": ALPHABET,
            "grouped": grouped.items(),
        },
    )


def ingredient_detail(request: HttpRequest, name: str) -> HttpResponse:
    uses = Ingredient.objects.select_related("recipe").filter(name=name)
    if not uses.exists():
        return render(
            request,
            "cucina/not_found.html",
            {
                "active_section": "ingredienti",
                "message": "Ingrediente non trovato.",
            },
            status=404,
        )

    if request.GET.get("from") == "ricetta" and request.GET.get("numero"):
        back_url = reverse("recipe_detail", args=[request.GET["numero"]])
    else:
        back_url = _safe_referer(request, reverse("ingredient_list"))

    return render(
        request,
        "cucina/ingredient_detail.html",
        {
            "active_section": "ingredienti",
            "ingredient_name": name,
            "uses": uses,
            "back_url": back_url,
        },
    )


def ingredient_create(request: HttpRequest, recipe_number: int) -> HttpResponse:
    recipe = get_object_or_404(Recipe, number=recipe_number)

    if request.method == "POST":
        form = IngredientCreateForm(request.POST)
        if form.is_valid():
            ingredient = form.save(commit=False)
            ingredient.recipe = recipe
            ingredient.number = (
                recipe.ingredients.aggregate(max_number=Max("number"))["max_number"]
                or 0
            ) + 1
            ingredient.save()
            return redirect("recipe_detail", number=recipe.number)
    else:
        form = IngredientCreateForm()

    return render(
        request,
        "cucina/ingredient_create.html",
        {
            "active_section": "ingredienti",
            "recipe": recipe,
            "form": form,
        },
    )


def ingredient_edit(
    request: HttpRequest,
    recipe_number: int,
    ingredient_id: int,
) -> HttpResponse:
    ingredient = get_object_or_404(
        Ingredient.objects.select_related("recipe"),
        id=ingredient_id,
        recipe_id=recipe_number,
    )
    original_name = ingredient.name
    other_uses = (
        Ingredient.objects.select_related("recipe")
        .filter(name=original_name)
        .exclude(id=ingredient.id)
    )

    if request.method == "POST":
        form = IngredientEditForm(request.POST, instance=ingredient)
        if form.is_valid():
            new_name = form.cleaned_data.get("name", "").strip() or original_name
            propagate = form.cleaned_data.get("propagate", False)
            with transaction.atomic():
                updated = form.save(commit=False)
                updated.name = new_name
                updated.save()
                if propagate and new_name != original_name:
                    Ingredient.objects.filter(name=original_name).exclude(
                        id=updated.id
                    ).update(name=new_name)
            return redirect("recipe_detail", number=recipe_number)
    else:
        form = IngredientEditForm(instance=ingredient)

    return render(
        request,
        "cucina/ingredient_edit.html",
        {
            "active_section": "ingredienti",
            "ingredient": ingredient,
            "recipe": ingredient.recipe,
            "other_uses": other_uses,
            "form": form,
        },
    )


def ingredient_delete(request: HttpRequest) -> HttpResponse:
    if request.method != "POST":
        return redirect("recipe_list")

    ingredient_id = request.POST.get("idIngrediente")
    recipe_number = request.POST.get("numero")
    if not ingredient_id or not recipe_number:
        return HttpResponseBadRequest("Dati mancanti.")

    Ingredient.objects.filter(id=ingredient_id, recipe_id=recipe_number).delete()
    return redirect("recipe_detail", number=recipe_number)


def region_list(request: HttpRequest) -> HttpResponse:
    search = request.GET.get("search", "").strip()
    selected_zones = request.GET.getlist("zona")

    regions = Region.objects.annotate(recipe_count=Count("regional_recipes"))
    if search:
        regions = regions.filter(name__icontains=search)
    if selected_zones:
        regions = regions.filter(zone__in=selected_zones)
    regions = regions.order_by("name")

    return render(
        request,
        "cucina/region_list.html",
        {
            "active_section": "regioni",
            "search": search,
            "selected_zones": selected_zones,
            "zones": ZONES,
            "regions": regions,
        },
    )


def region_detail(request: HttpRequest, code: str) -> HttpResponse:
    region = get_object_or_404(Region, code=code)
    links = RegionalRecipe.objects.select_related("recipe").filter(region=region)
    back_url = _safe_referer(request, reverse("region_list"))

    return render(
        request,
        "cucina/region_detail.html",
        {
            "active_section": "regioni",
            "region": region,
            "links": links,
            "back_url": back_url,
        },
    )


def book_list(request: HttpRequest) -> HttpResponse:
    title = request.GET.get("titolo", "").strip()
    year = request.GET.get("anno", "").strip()
    isbn = request.GET.get("isbn", "").strip()
    sort_field = request.GET.get("sort", "titolo")
    sort_direction = request.GET.get("dir", "asc")

    field_map = {"titolo": "title", "anno": "year"}
    if sort_field not in field_map:
        sort_field = "titolo"
    if sort_direction not in {"asc", "desc"}:
        sort_direction = "asc"

    books = Book.objects.annotate(
        page_count=Max("publications__page_number"),
        recipe_count=Count("publications__recipe", distinct=True),
    )
    if title:
        books = books.filter(title__icontains=title)
    if year:
        books = books.filter(year=year)
    if isbn:
        books = books.filter(isbn__icontains=isbn)

    order_field = field_map[sort_field]
    if sort_direction == "desc":
        order_field = f"-{order_field}"
    books = books.order_by(order_field)

    return render(
        request,
        "cucina/book_list.html",
        {
            "active_section": "libri",
            "title_filter": title,
            "year_filter": year,
            "isbn_filter": isbn,
            "show_filters": bool(title or year or isbn),
            "books": books,
            "sort_field": sort_field,
            "sort_direction": sort_direction,
        },
    )


def book_detail(request: HttpRequest, isbn: str) -> HttpResponse:
    book = get_object_or_404(Book, isbn=isbn)
    publications = Publication.objects.select_related("recipe").filter(book=book)

    source = request.GET.get("from")
    if source == "ricetta" and request.GET.get("numero"):
        back_url = reverse("recipe_detail", args=[request.GET["numero"]])
    elif source == "regione" and request.GET.get("cod"):
        back_url = reverse("region_detail", args=[request.GET["cod"]])
    elif source == "ingrediente" and request.GET.get("ingrediente"):
        back_url = reverse("ingredient_detail", args=[request.GET["ingrediente"]])
    else:
        back_url = _safe_referer(request, reverse("book_list"))

    return render(
        request,
        "cucina/book_detail.html",
        {
            "active_section": "libri",
            "book": book,
            "publications": publications,
            "back_url": back_url,
        },
    )
