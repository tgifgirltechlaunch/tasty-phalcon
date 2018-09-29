<?php

use Phalcon\Mvc\Controller;

class RecipesController extends Controller
{
    public function indexAction() 
    {
        // get recipe ID from the URL
        $name = $this->request->get('name');
        $where = $name ? "name like '%$name%'" : "";

        // get the recipes from the database
        $recipes = Recipe::find($where);

        // passing information to the view
        $this->view->name = $name;
        $this->view->recipes = $recipes;
    }

    public function recipeAction()
    {
        // get recipe ID from the URL
        $id = $this->request->get('id');

        // get the recipes by ID
        $recipe = Recipe::findFirst($id);

        // add one to the view
        $recipe->views++;
        $recipe->save();

        // passing information to the view
        $this->view->recipe = $recipe;
    }
}
