<?php

use Phalcon\Mvc\Controller;

class AdminController extends Controller
{
    public function initialize()
    {
        // get the current page
        $this->view->page = basename($_SERVER['REQUEST_URI']);

        // do not allow anymimous users
        if ( !$this->session->has('user')) {
            $this->response->redirect('/login');
        }

        // get the info for the person logged
        $user = $this->session->get('user');
        $this->view->user = $user;
    }

    public function indexAction() 
    {
        $this->response->redirect('/admin/recipes');
    }

    public function recipesAction()
    {
        // get the ID of the current user
        $idUser = $this->session->get('user')->id;

        // get the recipes from the database
        $recipes = Recipe::find("id_user='$idUser'");

        // passing information to the view
        $this->view->recipes = $recipes;
        $this->view->title = "List of recipes";
    }

    public function addAction() 
    {
        $this->view->recipe = new Recipe();
        $this->view->submit = "addSubmit";
        $this->view->title = "Add a recipe";
    }

    public function addSubmitAction() 
    {
        // get params from the url
        $name = $this->request->get('name');
        $time = $this->request->get('time');
        $ingredients = $this->request->get('ingredients');
        $instructions = $this->request->get('instructions');

        // save the image into the img directory
        $picture = null;
        if ($this->request->hasFiles()) {
            // save file to the directory and database
            foreach ($this->request->getUploadedFiles() as $file) {
                $picture = md5($file->getName() . time()) . "." . $file->getExtension ();
                $file->moveTo($_SERVER['DOCUMENT_ROOT']  . "/img/" . $picture);
            }
        } 

        // add recipe to the database
        $recipe = new Recipe();
        $recipe->name = $name;
        $recipe->time = $time;
        $recipe->ingredients = $ingredients;
        $recipe->instructions = $instructions;
        $recipe->picture = $picture;
        $recipe->id_user = $this->session->get('user')->id;
        $recipe->create();

        // redirect to the list of recipes
        $this->response->redirect('/recipes');
		$this->view->disable();
    }

    public function editAction() 
    {
        // get params from the url
        $id = $this->request->get('id');

        // load the recipe from the database
        $recipe = Recipe::findFirst($id);

        $this->view->recipe = $recipe;
        $this->view->submit = "editSubmit";
        $this->view->title = "Edit a recipe";
        $this->view->pick('admin/add');
    }

    public function editSubmitAction() 
    {
        // get params from the url
        $id = $this->request->get('id');
        $name = $this->request->get('name');
        $time = $this->request->get('time');
        $ingredients = $this->request->get('ingredients');
        $instructions = $this->request->get('instructions');

        // load the recipe from the database
        $recipe = Recipe::findFirst($id);

        // save the image into the img directory
        if ($this->request->hasFiles()) {
            foreach ($this->request->getUploadedFiles() as $file) {
                // do not allow empty image submissions
                if(empty($file->getExtension())) break;

                // delete old image file
                unlink($_SERVER['DOCUMENT_ROOT']  . "/img/" . $recipe->picture);

                // add new image
                $recipe->picture = md5($file->getName() . time()) . "." . $file->getExtension();
                $file->moveTo($_SERVER['DOCUMENT_ROOT']  . "/img/" . $recipe->picture);
            }
        }

        // add recipe to the database
        $recipe->name = $name;
        $recipe->time = $time;
        $recipe->ingredients = $ingredients;
        $recipe->instructions = $instructions;
        $recipe->save();

        // redirect to the list of recipes
        $this->response->redirect('/recipes');
		$this->view->disable();
    }
}
