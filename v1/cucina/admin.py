from django.contrib import admin

from .models import Book, Ingredient, Publication, Recipe, Region, RegionalRecipe


class IngredientInline(admin.TabularInline):
    model = Ingredient
    extra = 0


@admin.register(Recipe)
class RecipeAdmin(admin.ModelAdmin):
    list_display = ("number", "title", "recipe_type")
    list_filter = ("recipe_type",)
    search_fields = ("title",)
    inlines = (IngredientInline,)


@admin.register(Ingredient)
class IngredientAdmin(admin.ModelAdmin):
    list_display = ("name", "quantity", "recipe", "number")
    search_fields = ("name", "recipe__title")


@admin.register(Region)
class RegionAdmin(admin.ModelAdmin):
    list_display = ("code", "name", "zone")
    list_filter = ("zone",)
    search_fields = ("name",)


@admin.register(Book)
class BookAdmin(admin.ModelAdmin):
    list_display = ("isbn", "title", "year")
    search_fields = ("title", "isbn")


admin.site.register(Publication)
admin.site.register(RegionalRecipe)
