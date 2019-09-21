<?php
if (!isset($_SESSION)) { // A mettre dans le menu pour l'echo en html et isset dans l'index.
    session_start();
}
// Autoloader
spl_autoload_register(function (string $className) {
    if (strpos($className, 'Blog\\') === 0) {
        $className = str_replace('Blog\\', '', $className);
        $className = str_replace('\\', '/', $className);

        require __DIR__ . "/{$className}.php";
    }
});


use Blog\Models\Connection;
use Blog\Models\BilletRepository;
use Blog\Controllers\frontEnd;
use Blog\Controllers\backEnd;
use Blog\Models\CommentRepository;
use Blog\Models\AdminRepository;

$connection = new Connection('mysql:host=localhost;dbname=blog;charset=utf8', 'root', '');

$controller = $_REQUEST['controller'] ?? 'billet';
$action = $_REQUEST['action'] ?? 'indexBillets';

if ($controller == "billet") {
    $billetRepository = new BilletRepository($connection);
    $commentRepository = new CommentRepository($connection);
    $frontEnd = new frontEnd($billetRepository, $commentRepository);

    switch ($action) {
        case 'indexBillets':
            render($frontEnd->indexBillets());
            break;

        case 'accueilLogin':
            render($frontEnd->accueilLogin());
            break;

        case 'afficheBilletSimple':
            if (empty($_GET["idBillet"])) {
                render($frontEnd->error404());
            } else {
                render($frontEnd->afficheBilletSimple());
            }
            break;

        case 'ajouteCommentaire':
            render($frontEnd->ajouteCommentaire());
            break;
        case 'commentReported':
            render($frontEnd->commentReported());
            break;
        default:
            render($frontEnd->error404());
            break;
    }
} elseif ($controller == "backEnd") {
    if (empty($_SESSION['pseudoAdmin'])) {
        header('Location: index.php?controller=billet&action=accueilLogin');
    }
    $billetRepository = new BilletRepository($connection);
    $adminRepository = new AdminRepository($connection);
    $commentRepository = new CommentRepository($connection);

    $backEnd = new backEnd($adminRepository, $billetRepository, $commentRepository);
    switch ($action) {
        case 'checkLogin':
            render($backEnd->checkLogin());

            break;

        case 'logout':
            render($backEnd->logout());
            break;
        case 'ajouteBillet':
            render($backEnd->ajouteBillet());
            break;
        case 'effaceBillet':
            render($backEnd->effaceBillet());
            break;

        case 'billetModifier':
            render($backEnd->billetModifier());
            break;

        case 'changeBillet':
            render($backEnd->changeBillet());
            break;

        case 'createBillet':
            render($backEnd->createBillet());
            break;
        case 'getCommentReported':
            render($backEnd->getCommentReported());
            break;
        case 'deleteCommentReported':
            render($backEnd->deleteCommentReported());
            break;

        case 'commentIgnored':
            render($backEnd->commentIgnored());
            break;


        default:
            render($backEnd->error404());
            break;
    }
} else {
    require("Views/404.php");
}

// Pour éviter tous soucis avec extract sur de potentiel variable ecrasé
function render(array $data = [])
{
    $views = $data['views'] ?? '';
    if (file_exists($views)) {
        unset($data['views']);
        extract($data);

        require $views;
    }
}
