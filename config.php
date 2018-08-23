<?php
	
	spl_autoload_register();


	//Facbook API Configuration
    $fb = new Facebook\Facebook([
  				'app_id' => 'Your Facebook App Id', 
	  			'app_secret' => 'Your Facebook App Secret',
	  			'default_graph_version' => 'v2.11',
  			]);
    $helper = $fb->getRedirectLoginHelper();
	$loginUrl = $helper->getLoginUrl('Yoour Facebook App Redirect Url');
	$logoutURL = $helper->getLogoutUrl($_SESSION['fb_access_token'], 'Your Facebook App Redircet Domain'.'/logout.php');


	//USER DETAIL + AVTAR
    try {
	 	$response = $fb->get('/me?fields=id,name,cover,gender,email,picture,link', $_SESSION['fb_access_token']);
	  	//$responseImg = $fb->get('/me/picture', $_SESSION['fb_access_token']);
	  	$responseImg = $fb->get('/me/picture?redirect=false', $_SESSION['fb_access_token']);
	} catch(Facebook\Exceptions\FacebookResponseException $e) {
	  	echo 'Graph returned an error111: ' . $e->getMessage();
	  	exit;
	} catch(Facebook\Exceptions\FacebookSDKException $e) {
	  	echo 'Facebook SDK returned an error: ' . $e->getMessage();
	  	exit;
	}
	$user = $response->getGraphUser();
	$img = $responseImg->getGraphUser();

	// Albums Detail
	try {
  	// Returns a `FacebookFacebookResponse` object
  	$response = $fb->get('/me/albums?fields=count,name,link,cover_photo{id}',$_SESSION['fb_access_token']);
	} catch(FacebookExceptionsFacebookResponseException $e) {
  	echo 'Graph returned an error: ' . $e->getMessage();
  	exit;
	} catch(FacebookExceptionsFacebookSDKException $e) {
  	echo 'Facebook SDK returned an error: ' . $e->getMessage();
  	exit;
	}
	$graphNode = $response->getGraphEdge()->asArray();

	// Coverphoto of album
	function getCover($albumId)
	{
		$fb = new Facebook\Facebook([
  				'app_id' => 'Your Facebook App Id',
	  			'app_secret' => 'Your Facebook App Secret',
	  			'default_graph_version' => 'v2.11',
  			]);
		try {
        	$response = $fb->get('/'.$albumId.'?fields=images',$_SESSION['fb_access_token']);
            return end($response->getGraphNode()->asArray()['images'])['source'];
        } catch (Facebook\Exceptions\FacebookResponseException $e) {
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }
        return false;
	}
	
?>
