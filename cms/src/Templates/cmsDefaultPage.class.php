<?php
//check login
//basic class for building default cms page

session_start();
if (!isset($_SESSION["user"])) {
    header("Location:login.php");
}

//IDK how is this working but is working
require_once __DIR__ . "/../../config.php";
require_once ABS_PATH . "/config.php";

class cmsDefaultPage
{
    private string $out;

    public function __construct($out)
    {

        $this->out = $out;
    }

    private function buildHead(): string
    {
        return '<head>
   <meta charset="UTF-8">
   <meta name="viewport"
         content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
   <meta http-equiv="X-UA-Compatible" content="ie=edge">
   <title>Dashborard</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
   </head>';
    }

    private function getAlerts()
    {
        $out = '';
        if (isset($_SESSION['cms_message'])) {
            $out .= '<div id="cmsAlert" class="alert alert-success alert-dismissible fade show" role="alert">'
                . $_SESSION['cms_message'] .
                '</div>';
            unset($_SESSION['cms_message']);
        }
        if (isset($_SESSION['cms_message_error'])) {
            $out .= '<div id="cmsAlert" class="alert alert-danger alert-dismissible fade show" role="alert">'
                . $_SESSION['cms_message_error'] .
                '</div>';
            unset($_SESSION['cms_message_error']);
        }

        return $out;
    }

    private function buildNavbar(): string
    {
        return '<nav class="navbar bg-body-tertiary">
  <div class="container">
    <a class="navbar-brand" href="' . ABS_URL . '/modules/index.php">Administrace</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    </div>
</nav>
<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title m-3" id="offcanvasExampleLabel ms-3">Administrace</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body d-flex flex-column">
   
   <a href="' . ABS_URL . '/modules/create.php" type="button" class="btn btn-outline-light m-3">Přidat nový modul</a>
   
   <a href="' . ABS_URL . '//modules/index.php" type="button" class="btn btn-outline-light m-3">Seznam modulů</a>
  
 <a href="' . ABS_URL . '/logout.php" type="button" class="btn btn-outline-light m-3">Odhlásit se</a>
    </div>
  </div>
</div>';
    }

    public function buildLayout()
    {
        echo '<!doctype html>
              <html data-bs-theme="dark" lang="en">';
        echo $this->buildHead();
        echo $this->getAlerts();
        echo $this->buildNavbar();
        echo '<div class="container my-5">';

        echo $this->out;

        echo '</div>';
        echo $this->buildFooter();
        echo '</html>';
    }

    //build boostrap nav tab menu
    //for module page
    //has urls with module actions
    //takes module name as argument
    public static function buildNavTabs($moduleName): string
    {
        $tabs = [
            'Data' => '/modules/module/data.php',
            'Komponenty' => '/modules/module/components.php',
            'Konfigurace modulu' => '/modules/module/config.php'
        ];
        // Get the current URL path (without the domain)
        $currentUrl = $_SERVER['REQUEST_URI'];

        // Start building the HTML for the tabs
        $navHtml = '<ul class="nav nav-tabs mb-5">';

        // Loop through the tabs to generate each tab's HTML
        foreach ($tabs as $label => $url) {
            // Append the module name to each URL
            $fullUrl = ABS_URL . $url . '?module_name=' . $moduleName;

            // Determine the active class by comparing the current URL
            $activeClass = (strpos($currentUrl, $url) !== false) ? 'active' : '';

            // Add the tab's HTML
            $navHtml .= '
            <li class="nav-item">
                <a class="nav-link ' . $activeClass . '" href="' . $fullUrl . '">' . $label . '</a>
            </li>';
        }

        // Close the <ul> element
        $navHtml .= '</ul>';

        return $navHtml;
    }

    private function buildFooter()
    {

        //get scripts
//        return '<script src="' . ABS_URL . '/src/Templates/defaultPage.js"></script>';
       return "";
    }
}