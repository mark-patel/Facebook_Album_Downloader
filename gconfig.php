<?php
    
    session_start();
    spl_autoload_register();
    require_once('config.php');

    // Connect With Google API
    $url_array = explode('?', 'http://'.$_SERVER ['HTTP_HOST'].$_SERVER['REQUEST_URI']);
    $url = $url_array[0];

    require_once 'Google/Google_Client.php';
    require_once 'Google/contrib/Google_DriveService.php';

    $client = new Google_Client();
    $client->setClientId('Your Google Cliednt Id');
    $client->setClientSecret('Your Google Client Secret');
    $client->setRedirectUri($url);
    $client->setScopes(array('https://www.googleapis.com/auth/drive'));
    
    if (isset($_GET['code']))
    {
        $_SESSION['accessToken'] = $client->authenticate($_GET['code']);
        header('location:album.php');
        exit;
    }
    elseif (!isset($_SESSION['accessToken'])) 
    {
        $client->authenticate();
    }

?>
