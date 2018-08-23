<?php

	session_start();
	require_once('config.php');
    spl_autoload_register();

    // Check requsetd album id is array or not if it is not then make it array type    
    if(gettype($_REQUEST['album'])!="array")
    {
        $albums= array($_REQUEST['album'],);
        // $albums = implode(",",$str);
    }
    else
    {
        $albums = $_REQUEST["album"];
    } 

    foreach ($albums as $ID)
    {
        try {
            // Returns a `FacebookFacebookResponse` object
            $a=$ID;
            $response = $fb->get('/'.$a.'/photos?fields=picture,name,images&limit=100',$_SESSION['fb_access_token']);
            $getAlbum = $fb->get('/'.$a.'?fields=name,photos.limit(100){images,name,created_time}',$_SESSION['fb_access_token']);
        }catch(FacebookExceptionsFacebookResponseException $e){
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        }catch(FacebookExceptionsFacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }
        $graphNode = $response->getGraphEdge()->asArray();  

        // Make Download folder on server for saving images
        $tmp_dir = __DIR__.'/DOWNLOAD/';

        // Check Download folder is already exist or not if it is not then create new folder
        if (!is_dir($tmp_dir)) {
            mkdir($tmp_dir, 0777);
        }
    
        // Pick a album and fetch detail of it
        $albumId = $a;
        $album = $getAlbum->getGraphNode()->asArray();
        $albumName = $album['name'];
        $albumName = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $albumName);
        $albumName = mb_ereg_replace("([\.]{2,})", '', $albumName);
    
        // Move inside the folder and chaeck album name folder is exist or not id it is not then create new folder
        $path = $tmp_dir.$albumName.'/';
        if (!is_dir($path)) {
            mkdir($path, 0777);
        }

        // Fetch photos from album and copy to servers temporary(Download) Folder
        foreach ($album['photos'] as $photo)
        {
            $file = $photo['id'].'.jpg';
            copy($photo['images'][0]['source'], $path.$file);
        }
    
    }
    
    // Create Zip of temporary(Download) Folder
    $zip_name = 'album.zip';
    $zip_directory = '/';
    $zip = new zip( $zip_name, $zip_directory );
    $dir = 'DOWNLOAD';
    $zip->add_directory($dir);
    $zip->save();
    $zip_path = $zip->get_zip_path();
    header( "Pragma: public" );
    header( "Expires: 0" );
    header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
    header( "Cache-Control: public" );
    header( "Content-Description: File Transfer" );
    header( "Content-type: application/zip" );
    header( "Content-Disposition: attachment; filename=\"" . $zip_name . "\"" );
    header( "Content-Transfer-Encoding: binary" );
    header( "Content-Length: " . filesize( $zip_path ) );
    readfile( $zip_path );

    // Remove the temporary(Download) folder from the server
    $zip->removeRecursive($dir.'/');
?>