<?php

use Phalcon\Mvc\Controller;


class LoginController extends Controller
{
    public function indexAction() 
    {
        $this->view->title = "Login";
        $error = $this->request->get('error');
        $this->view->error = $error;
    }

    public function submitAction() 
    {
        // get data from the URL
        $email = $this->request->get('email');
        $password = md5($this->request->get('password'));
        
        // connect to the databas and pull the user
        $user = User::findFirst("email='$email' AND password='$password'");

        // check if data is ok
        if($user) {
            // create the session
            $obj = new stdClass();
            $obj->id = $user->id;
            $obj->name = $user->name;
            $obj->email = $user->email;
            $obj->picture = $user->picture;
            $this->session->set('user', $obj);

            // redirect to the admin
            $this->response->redirect('./admin');
        } else {
            // redirect to login with error
            $this->response->redirect('/login/index?error=1');
        }
    }

    public function signupSubmitAction() 
    {
        $this->view->disable();

        if($this->request->isPost()) {
            $dataSent = $this->request->getPost();
            $password = md5($dataSent["password"]);
            $confirmpassword = md5($dataSent["confirmpassword"]);

            if($confirmpassword !== $password){
                $message = "Please check that the passwords match.";

                // redirect to login page
                $this->response->redirect('/login/signup&message=' . $message);
            }
            else{
                $auser = new User();
                $auser->name = $dataSent["name"];
                $auser->email = $dataSent["email"];
                $auser->password = $password;
                
                // save the image into the img directory
                $picture = "no_image.jpg";
                
                if ($this->request->hasFiles('picture')) {
                    // save file to the directory and database
                    foreach ($this->request->getUploadedFiles() as $file) {
                        
                        $picture = md5($file->getName() . time()) . "." . $file->getExtension ();
                        $file->moveTo($_SERVER['DOCUMENT_ROOT']  . "/img/" . $picture);
                    }
                }

                $auser->picture = $picture;

                $savedSuccessfully = $auser->save();

                if($savedSuccessfully) {
                    // redirect to the admin
                    $this->response->redirect('/admin');
                } else {
                    $messages = $auser->getMessages();
                    echo "Sorry, the following problems were generated: ";
                    foreach ($messages as $message) {
                        echo "$message <br/>";
                    }
                }
            }
        } else {
            echo "The request method should be POST!";  
        }
    }

    public function logoutAction()
    {
        // close session
        $this->session->destroy();

        // redirect to login page
        $this->response->redirect('/');
    }

    public function signupAction() 
    {
        $this->view->title = "Sign Up";
    }
}
