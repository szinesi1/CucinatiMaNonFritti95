"""Form usati per aggiungere e modificare gli ingredienti."""

from django import forms

from .models import Ingredient


class IngredientCreateForm(forms.ModelForm):
    class Meta:
        model = Ingredient
        fields = ["name", "quantity"]
        widgets = {
            "name": forms.TextInput(
                attrs={
                    "class": "text-input",
                    "id": "ingrediente",
                    "required": True,
                }
            ),
            "quantity": forms.TextInput(
                attrs={
                    "class": "text-input",
                    "id": "quantita",
                    "required": True,
                }
            ),
        }


class IngredientEditForm(forms.ModelForm):
    name = forms.CharField(required=False, widget=forms.TextInput(attrs={"class": "text-input"}))
    propagate = forms.BooleanField(required=False)

    class Meta:
        model = Ingredient
        fields = ["name", "quantity"]
        widgets = {
            "name": forms.TextInput(attrs={"class": "text-input"}),
            "quantity": forms.TextInput(
                attrs={"class": "text-input", "required": True}
            ),
        }
