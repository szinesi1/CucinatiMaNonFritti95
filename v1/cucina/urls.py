from django.urls import path

from . import views

urlpatterns = [
    path("", views.home, name="home"),
    path("ricette/", views.recipe_list, name="recipe_list"),
    path("ricette/<int:number>/", views.recipe_detail, name="recipe_detail"),
    path("ingredienti/", views.ingredient_list, name="ingredient_list"),
    path("ingredienti/nuovo/<int:recipe_number>/", views.ingredient_create, name="ingredient_create"),
    path("ingredienti/modifica/<int:recipe_number>/<int:ingredient_id>/", views.ingredient_edit, name="ingredient_edit"),
    path("ingredienti/elimina/", views.ingredient_delete, name="ingredient_delete"),
    path("ingredienti/<path:name>/", views.ingredient_detail, name="ingredient_detail"),
    path("regioni/", views.region_list, name="region_list"),
    path("regioni/<str:code>/", views.region_detail, name="region_detail"),
    path("libri/", views.book_list, name="book_list"),
    path("libri/<str:isbn>/", views.book_detail, name="book_detail"),
]
