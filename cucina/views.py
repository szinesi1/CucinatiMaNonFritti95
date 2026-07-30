"""View dell'applicazione: ricerca, filtri e operazioni CRUD sugli ingredienti."""

from collections import OrderedDict
from urllib.parse import urlsplit

from django.db import transaction
from django.core.paginator import Paginator
from django.db.models import Count, Exists, Max, OuterRef, Q
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

def _safe_url(request: HttpRequest, candidate: str, fallback: str) -> str:
    """Accetta un URL di ritorno solo quando appartiene allo stesso sito."""
    if candidate and url_has_allowed_host_and_scheme(
        candidate,
        allowed_hosts={request.get_host()},
        require_https=request.is_secure(),
    ):
        return candidate
    return fallback

def _detail_back_url(request: HttpRequest, fallback: str) -> str:
    """Conserva la reale pagina interna di provenienza anche dopo un aggiornamento."""
    session_key = f"detail_back_url:{request.path}"
    referer = _safe_url(request, request.META.get("HTTP_REFERER", ""), "")

    if referer and urlsplit(referer).path != request.path:
        request.session[session_key] = referer
        return referer

    return _safe_url(request, request.session.get(session_key, ""), fallback)

def _non_negative_int(value: str) -> int | None:
    """Converte un valore numerico del filtro; i valori vuoti o errati vengono ignorati."""
    try:
        number = int(value)
    except (TypeError, ValueError):
        return None
    return number if number >= 0 else None

def home(request: HttpRequest) -> HttpResponse:
    return render(request, "cucina/home.html", {"active_section": "home"})

def _session_filters(request: HttpRequest, key: str, defaults: dict) -> dict:
    """Memorizza i filtri inviati via POST senza inserirli nella URL."""
    if request.method == "POST" and request.POST.get("form_action") not in {"clear", "paginate"}:
        values = {}
        for name, default in defaults.items():
            if isinstance(default, list):
                values[name] = [value.strip() for value in request.POST.getlist(name) if value.strip()]
            else:
                values[name] = request.POST.get(name, default).strip()
        request.session[key] = values
    stored = request.session.get(key, {})
    return {name: stored.get(name, default) for name, default in defaults.items()}

def _clear_filters(request: HttpRequest, key: str):
    """Cancella i filtri della sola pagina corrente e torna alla stessa pagina."""
    if request.method == "POST" and request.POST.get("form_action") == "clear":
        request.session.pop(key, None)
        return redirect(request.path)
    return None

def recipe_list(request: HttpRequest) -> HttpResponse:
    clear_response = _clear_filters(request, "recipe_filters")
    if clear_response:
        return clear_response
    filters = _session_filters(
        request,
        "recipe_filters",
        {
            "search": "",
            "tipo": [],
            "ingrediente": "",
            "regione": "",
            "pubblicazione": "tutte",
            "ordina": "titolo",
            "direzione": "asc",
        },
    )
    search = filters["search"]
    selected_types = [value.lower() for value in filters["tipo"] if value.lower() in RECIPE_TYPES]
    ingredient = filters["ingrediente"]
    region_code = filters["regione"]
    publication_status = filters["pubblicazione"]
    sort_field = filters["ordina"]
    sort_direction = filters["direzione"]

    if publication_status not in {"tutte", "pubblicate", "non_pubblicate"}:
        publication_status = "tutte"
    sort_fields = {"titolo": "title", "tipologia": "recipe_type", "regione": "region_name"}
    if sort_field not in sort_fields:
        sort_field = "titolo"
    if sort_direction not in {"asc", "desc"}:
        sort_direction = "asc"

    publication_exists = Publication.objects.filter(recipe_id=OuterRef("pk"))
    recipes = (
        Recipe.objects.annotate(
            is_published=Exists(publication_exists),
            region_name=Max("regional_links__region__name"),
        )
        .prefetch_related("ingredients", "regional_links__region", "publications")
    )
    if search:
        recipes = recipes.filter(title__icontains=search)
    if selected_types:
        recipes = recipes.filter(recipe_type__in=selected_types)
    if ingredient:
        recipes = recipes.filter(ingredients__name__icontains=ingredient)
    if region_code:
        recipes = recipes.filter(regional_links__region_id=region_code)
    if publication_status == "pubblicate":
        recipes = recipes.filter(is_published=True)
    elif publication_status == "non_pubblicate":
        recipes = recipes.filter(is_published=False)

    recipes = recipes.distinct()
    total_count = Recipe.objects.count()
    filtered_count = recipes.count()
    type_counts = list(
        Recipe.objects.values("recipe_type")
        .annotate(total=Count("number"))
        .order_by("recipe_type")
    )
    counts_by_type = {item["recipe_type"]: item["total"] for item in type_counts}
    type_stats = [
        {"label": label, "count": counts_by_type.get(key, 0)}
        for key, label in RECIPE_TYPES.items()
    ]

    order_field = sort_fields[sort_field]
    if sort_direction == "desc":
        order_field = f"-{order_field}"
    recipes = recipes.order_by(order_field, "title")
    page_number = request.POST.get("page", 1) if request.POST.get("form_action") == "paginate" else 1
    page_obj = Paginator(recipes, 12).get_page(page_number)

    active_filters = []
    if search:
        active_filters.append(f'Titolo: "{search}"')
    if selected_types:
        active_filters.append("Tipologie: " + ", ".join(RECIPE_TYPES[t] for t in selected_types))
    if ingredient:
        active_filters.append(f'Ingrediente: "{ingredient}"')
    if region_code:
        active_filters.append("Regione selezionata")
    if publication_status != "tutte":
        active_filters.append("Solo pubblicate" if publication_status == "pubblicate" else "Solo non pubblicate")

    return render(
        request,
        "cucina/recipe_list.html",
        {
            "active_section": "ricette",
            "search": search,
            "selected_types": selected_types,
            "ingredient_filter": ingredient,
            "region_filter": region_code,
            "publication_status": publication_status,
            "sort_field": sort_field,
            "sort_direction": sort_direction,
            "recipe_types": RECIPE_TYPES.items(),
            "regions": Region.objects.order_by("name"),
            "page_obj": page_obj,
            "recipes": page_obj.object_list,
            "show_filters": bool(active_filters or sort_field != "titolo" or sort_direction != "asc"),
            "active_filters": active_filters,
            "total_count": total_count,
            "filtered_count": filtered_count,
            "type_stats": type_stats,
        },
    )

def recipe_detail(request: HttpRequest, number: int) -> HttpResponse:
    recipe = get_object_or_404(Recipe, number=number)

    back_url = _detail_back_url(request, reverse("recipe_list"))

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
            "ingredient_count": recipe.ingredients.count(),
            "publication_count": publications.count(),
        },
    )

def ingredient_list(request: HttpRequest) -> HttpResponse:
    clear_response = _clear_filters(request, "ingredient_filters")
    if clear_response:
        return clear_response
    data = _session_filters(request, "ingredient_filters", {"search": "", "lettera": [], "ricette_min": "", "ricette_max": "", "ordina": "nome", "direzione": "asc"})
    search = data["search"]
    selected_letters = [
        letter.upper()
        for letter in data["lettera"]
        if letter.upper() in ALPHABET
    ]
    min_recipes_raw = data["ricette_min"]
    max_recipes_raw = data["ricette_max"]
    min_recipes = _non_negative_int(min_recipes_raw)
    max_recipes = _non_negative_int(max_recipes_raw)
    sort_field = data["ordina"]
    sort_direction = data["direzione"]

    sort_fields = {
        "nome": "name",
        "ricette": "recipe_count",
    }
    if sort_field not in sort_fields:
        sort_field = "nome"
    if sort_direction not in {"asc", "desc"}:
        sort_direction = "asc"

    ingredients = Ingredient.objects.values("name").annotate(
        recipe_count=Count("recipe", distinct=True)
    )
    if search:
        ingredients = ingredients.filter(name__icontains=search)
    if selected_letters:
        letter_query = Q()
        for letter in selected_letters:
            letter_query |= Q(name__istartswith=letter)
        ingredients = ingredients.filter(letter_query)
    if min_recipes is not None:
        ingredients = ingredients.filter(recipe_count__gte=min_recipes)
    if max_recipes is not None:
        ingredients = ingredients.filter(recipe_count__lte=max_recipes)

    total_count = Ingredient.objects.values("name").distinct().count()
    filtered_count = ingredients.count()

    order_field = sort_fields[sort_field]
    if sort_direction == "desc":
        order_field = f"-{order_field}"
    ingredients = ingredients.order_by(order_field, "name")
    page_number = request.POST.get("page", 1) if request.POST.get("form_action") == "paginate" else 1
    page_obj = Paginator(ingredients, 20).get_page(page_number)

    active_filters = []
    if search:
        active_filters.append(f'Nome: "{search}"')
    if selected_letters:
        active_filters.append(f"Iniziali: {', '.join(selected_letters)}")
    if min_recipes is not None:
        active_filters.append(f"Almeno {min_recipes} ricette")
    if max_recipes is not None:
        active_filters.append(f"Al massimo {max_recipes} ricette")

    return render(
        request,
        "cucina/ingredient_list.html",
        {
            "active_section": "ingredienti",
            "search": search,
            "selected_letters": selected_letters,
            "alphabet": ALPHABET,
            "ingredients": page_obj.object_list,
            "page_obj": page_obj,
            "min_recipes": min_recipes_raw,
            "max_recipes": max_recipes_raw,
            "sort_field": sort_field,
            "sort_direction": sort_direction,
            "show_filters": bool(
                selected_letters
                or min_recipes_raw
                or max_recipes_raw
                or sort_field != "nome"
                or sort_direction != "asc"
            ),
            "active_filters": active_filters,
            "total_count": total_count,
            "filtered_count": filtered_count,
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

    back_url = _detail_back_url(request, reverse("ingredient_list"))

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

    ingredient_id = _non_negative_int(request.POST.get("idIngrediente", ""))
    recipe_number = _non_negative_int(request.POST.get("numero", ""))
    if not ingredient_id or not recipe_number:
        return HttpResponseBadRequest("Dati mancanti o non validi.")

    Ingredient.objects.filter(id=ingredient_id, recipe_id=recipe_number).delete()
    return redirect("recipe_detail", number=recipe_number)

def region_list(request: HttpRequest) -> HttpResponse:
    clear_response = _clear_filters(request, "region_filters")
    if clear_response:
        return clear_response
    data = _session_filters(request, "region_filters", {"search": "", "zona": [], "ricette_min": "", "ricette_max": "", "ordina": "nome", "direzione": "asc"})
    search = data["search"]
    selected_zones = [zone for zone in data["zona"] if zone in ZONES]
    min_recipes_raw = data["ricette_min"]
    max_recipes_raw = data["ricette_max"]
    min_recipes = _non_negative_int(min_recipes_raw)
    max_recipes = _non_negative_int(max_recipes_raw)
    sort_field = data["ordina"]
    sort_direction = data["direzione"]

    sort_fields = {
        "nome": "name",
        "zona": "zone",
        "ricette": "recipe_count",
    }
    if sort_field not in sort_fields:
        sort_field = "nome"
    if sort_direction not in {"asc", "desc"}:
        sort_direction = "asc"

    regions = Region.objects.annotate(
        recipe_count=Count("regional_recipes__recipe", distinct=True)
    )
    if search:
        regions = regions.filter(name__icontains=search)
    if selected_zones:
        regions = regions.filter(zone__in=selected_zones)
    if min_recipes is not None:
        regions = regions.filter(recipe_count__gte=min_recipes)
    if max_recipes is not None:
        regions = regions.filter(recipe_count__lte=max_recipes)

    total_count = Region.objects.count()
    filtered_count = regions.count()

    order_field = sort_fields[sort_field]
    if sort_direction == "desc":
        order_field = f"-{order_field}"
    regions = regions.order_by(order_field, "name")
    page_number = request.POST.get("page", 1) if request.POST.get("form_action") == "paginate" else 1
    page_obj = Paginator(regions, 12).get_page(page_number)

    active_filters = []
    if search:
        active_filters.append(f'Nome: "{search}"')
    if selected_zones:
        active_filters.append(f"Zone: {', '.join(selected_zones)}")
    if min_recipes is not None:
        active_filters.append(f"Almeno {min_recipes} ricette")
    if max_recipes is not None:
        active_filters.append(f"Al massimo {max_recipes} ricette")

    return render(
        request,
        "cucina/region_list.html",
        {
            "active_section": "regioni",
            "search": search,
            "selected_zones": selected_zones,
            "zones": ZONES,
            "regions": page_obj.object_list,
            "page_obj": page_obj,
            "min_recipes": min_recipes_raw,
            "max_recipes": max_recipes_raw,
            "sort_field": sort_field,
            "sort_direction": sort_direction,
            "show_filters": bool(
                selected_zones
                or min_recipes_raw
                or max_recipes_raw
                or sort_field != "nome"
                or sort_direction != "asc"
            ),
            "active_filters": active_filters,
            "total_count": total_count,
            "filtered_count": filtered_count,
        },
    )

def region_detail(request: HttpRequest, code: str) -> HttpResponse:
    region = get_object_or_404(Region, code=code)
    links = (
        RegionalRecipe.objects.select_related("recipe")
        .filter(region=region)
        .order_by("recipe__title")
    )
    back_url = _detail_back_url(request, reverse("region_list"))

    return render(
        request,
        "cucina/region_detail.html",
        {
            "active_section": "regioni",
            "region": region,
            "links": links,
            "recipe_count": links.count(),
            "back_url": back_url,
        },
    )

def book_list(request: HttpRequest) -> HttpResponse:
    clear_response = _clear_filters(request, "book_filters")
    if clear_response:
        return clear_response
    data = _session_filters(request, "book_filters", {"titolo": "", "ricetta": "", "anno": "", "isbn": "", "pagine_min": "", "pagine_max": "", "ricette_min": "", "ricette_max": "", "ordina": "titolo", "direzione": "asc"})
    title = data["titolo"]
    recipe = data["ricetta"]
    year_raw = data["anno"]
    year = _non_negative_int(year_raw)
    isbn = data["isbn"]
    min_pages_raw = data["pagine_min"]
    max_pages_raw = data["pagine_max"]
    min_recipes_raw = data["ricette_min"]
    max_recipes_raw = data["ricette_max"]
    min_pages = _non_negative_int(min_pages_raw)
    max_pages = _non_negative_int(max_pages_raw)
    min_recipes = _non_negative_int(min_recipes_raw)
    max_recipes = _non_negative_int(max_recipes_raw)
    sort_field = data["ordina"]
    sort_direction = data["direzione"]

    sort_fields = {
        "titolo": "title",
        "anno": "year",
        "pagine": "page_count",
        "ricette": "recipe_count",
    }
    if sort_field not in sort_fields:
        sort_field = "titolo"
    if sort_direction not in {"asc", "desc"}:
        sort_direction = "asc"

    books = Book.objects.annotate(
        page_count=Max("publications__page_number"),
        recipe_count=Count("publications__recipe", distinct=True),
    )
    if title:
        books = books.filter(title__icontains=title)
    if recipe:
        books = books.filter(publications__recipe__title__icontains=recipe)
    if year is not None:
        books = books.filter(year=year)
    if isbn:
        books = books.filter(isbn__icontains=isbn)
    if min_pages is not None:
        books = books.filter(page_count__gte=min_pages)
    if max_pages is not None:
        books = books.filter(page_count__lte=max_pages)
    if min_recipes is not None:
        books = books.filter(recipe_count__gte=min_recipes)
    if max_recipes is not None:
        books = books.filter(recipe_count__lte=max_recipes)

    total_count = Book.objects.count()
    filtered_count = books.count()

    order_field = sort_fields[sort_field]
    if sort_direction == "desc":
        order_field = f"-{order_field}"
    books = books.order_by(order_field, "title").distinct()
    page_number = request.POST.get("page", 1) if request.POST.get("form_action") == "paginate" else 1
    page_obj = Paginator(books, 15).get_page(page_number)

    active_filters = []
    if title:
        active_filters.append(f'Titolo: "{title}"')
    if recipe:
        active_filters.append(f'Ricetta: "{recipe}"')
    if year is not None:
        active_filters.append(f"Anno: {year}")
    if isbn:
        active_filters.append(f"ISBN: {isbn}")
    if min_pages is not None:
        active_filters.append(f"Almeno {min_pages} pagine")
    if max_pages is not None:
        active_filters.append(f"Al massimo {max_pages} pagine")
    if min_recipes is not None:
        active_filters.append(f"Almeno {min_recipes} ricette")
    if max_recipes is not None:
        active_filters.append(f"Al massimo {max_recipes} ricette")

    return render(
        request,
        "cucina/book_list.html",
        {
            "active_section": "libri",
            "title_filter": title,
            "recipe_filter": recipe,
            "year_filter": year_raw,
            "isbn_filter": isbn,
            "min_pages": min_pages_raw,
            "max_pages": max_pages_raw,
            "min_recipes": min_recipes_raw,
            "max_recipes": max_recipes_raw,
            "books": page_obj.object_list,
            "page_obj": page_obj,
            "sort_field": sort_field,
            "sort_direction": sort_direction,
            "active_filters": active_filters,
            "total_count": total_count,
            "filtered_count": filtered_count,
        },
    )

def book_detail(request: HttpRequest, isbn: str) -> HttpResponse:
    book = get_object_or_404(Book, isbn=isbn)
    publications = (
        Publication.objects.select_related("recipe")
        .filter(book=book)
        .order_by("page_number")
    )
    recipe_count = publications.count()
    page_count = publications.aggregate(total=Max("page_number"))["total"] or 0
    page_number = request.POST.get("page", 1) if request.POST.get("form_action") == "paginate" else 1
    page_obj = Paginator(publications, 30).get_page(page_number)

    back_url = _detail_back_url(request, reverse("book_list"))

    return render(
        request,
        "cucina/book_detail.html",
        {
            "active_section": "libri",
            "book": book,
            "publications": page_obj.object_list,
            "page_obj": page_obj,
            "recipe_count": recipe_count,
            "page_count": page_count,
            "back_url": back_url,
        },
    )
