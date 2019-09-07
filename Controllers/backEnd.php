<?php

namespace Blog\Controllers;

use Blog\Models\AdminRepository;
use Blog\Models\BilletRepository;

class backEnd
{

    private $adminRepository;
    private $billetRepository;


   


    public function __construct(AdminRepository $adminRepository, BilletRepository $billetRepository)
    {
        $this->adminRepository = $adminRepository;
        $this->billetRepository = $billetRepository;
    }



    public function checkLogin()
    {

        if ((isset($_POST['pseudoAdmin'])) and (isset($_POST['loginAdmin']))) {

            $admin = $this->adminRepository->checkAdmin($_POST['pseudoAdmin'], sha1($_POST["loginAdmin"]));

            if ($admin != null) {
                header('Cache-Control: no cache'); //no cache
                session_cache_limiter('private_no_expire'); // works
                session_start();
                $_SESSION['pseudoAdmin'] = $_POST['pseudoAdmin'];

                header('Location: index.php?controller=backEnd&action=checkLogin');
            } else {
                header('Location: index.php?controller=billet&action=accueilLogin');
            }
        }

        $billets = $this->billetRepository->getBillets();
        return [
            'views' => __DIR__ . '/../Views/Admincrudview.php',
            'billets' => $billets,
        ];
    }
    public function logout()
    {

        session_start();
        $_SESSION = array();

        session_unset(); // suppression des variables de sessions   
        session_destroy(); // destruction de la session   
        header("location: index.php?controller=billet&action=accueilLogin"); // redirection 
      
    }
    public function error404()
    {
        return [
            'views' => __DIR__ . '/../Views/404.php',
        ];
    }

    public function errorUpdateView()
    {
        return [
            'views' => __DIR__ . '/../Views/Adminupdateview.php',
        ];
    }

    

    public function createBillet()
    {
        return [
            'views' => __DIR__ . '/../Views/Admincreateview.php',
        ];
    }
    public function ajouteBillet():array
    {
        $titre = $_POST["titre"];
        $contenu = $_POST["chapitre"];
        if (empty($titre) || empty($contenu)) {

            return $this->checkLogin();
        } else {
            $this->billetRepository->addBillet($titre, $contenu);
            return $this->checkLogin();

        }
    }

    public function effaceBillet():array
    {
        $idBillet = $_GET['idBillet'];
        $deleteBillet=$this->billetRepository->deleteBillet($idBillet);
        $billets = $this->billetRepository->getBillets();

        return [
            'views' => __DIR__ . '/../Views/Admincrudview.php',
             'deleteBillet'=>$deleteBillet,
             'billets'=>$billets
        ];
    }
    public function changeBillet():array
    {
        $idBillet = $_GET['idBillet'];
        $titre = $_POST['change_titre'];
        $contenu = $_POST['change_chapitre'];

        if (empty($titre) || empty($contenu)) {

            return $this->checkLogin();
        } else {
            $updateBillet=$this->billetRepository->updateBillet($titre,$contenu,$idBillet);
            $billets = $this->billetRepository->getBillets();

        }
        

        return [
            'views' => __DIR__ . '/../Views/Admincrudview.php',
             'updateBillet'=>$updateBillet,
             'billets'=>$billets
        ];
    }

    public function billetModifier(): array
    {
        $idBillet = $_GET['idBillet'];
        $billet = $this->billetRepository->getBillet($idBillet);

        return [
            'views' => __DIR__ . '/../Views/Adminupdateview.php',
            'billet' => $billet,
        ];
    }
}
