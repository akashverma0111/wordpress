<?php
/**
 *
 * @wordpress-plugin
 * Plugin Name:       Custom Rest Api
 * Description:       This Plugin contain all rest api.
 * Version:           1.0
 * Author:            NM
 */
 
header( 'Access-Control-Allow-Origin: *' );
header( 'Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE' );
header( 'Access-Control-Allow-Credentials: true' );

use Firebase\JWT\JWT;
class MY_RESTAPI extends WP_REST_Controller{
 private $api_namespace;
 private $api_version;
 private $required_capability;
 public  $user_token;
 public  $user_id;

 /*public function __construct(){
 	$this->api_namespace="api";
 	$this->api_version="v1";
 	$this->init();
 }*/
 
 public function __construct() {
		$this->api_namespace       = 'api/v';
		$this->api_version         = '1';
		$this->required_capability = 'read';  // Minimum capability to use the endpoint
		$this->init();

		/*------- Start: Validate Token Section -------*/
		$headers = getallheaders();
		if (isset($headers['Authorization'])) { 
        	if (preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) { 
            	$this->user_token =  $matches[1]; 
        	    //$this->user_id  = $this->getUserIdByToken($token);
        	} 
        }
        /*------- End: Validate Token Section -------*/
	}
	 private function successResponse($message = '',$data = array()){
        $response            = array();
        $response['status']  = "success";
        $response['message'] = $message;
        $response['data']    = $data;
        return new WP_REST_Response($response, 200); 
    }
    private function errorResponse($message = '', $type = 'ERROR', $statusCode = 200){
        $response               = array();
        $response['status']     = "error";
        $response['error_type'] = $type;
        $response['message']    = $message;
        return new WP_REST_Response($response, $statusCode); 
    }
	
	public function register_routes() {
		$namespace    = $this->api_namespace . $this->api_version;
        // Api End Points Name 
	    $publicItems  = array('register');
	     $privateItems = array('getUser');
	     foreach($privateItems as $Item){
		  	register_rest_route( $namespace, '/'.$Item, array(
			   array( 
			       'methods'  => 'POST',
			       'callback' => array( $this, $Item), 
			       'permission_callback' => !empty($this->user_token)?'__return_true':'__return_false' 
			       ),
	    	    )  
	    	);  
		}
	
	   foreach($publicItems as $Items){
		  	register_rest_route( $namespace, '/'.$Items, array(
			   array( 
			       'methods'  => 'POST', 
			       'callback' => array( $this, $Items )
			       ),
	    	    )  
	    	);  
		}
	}
	
	
	public function init(){
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
		add_action( 'rest_api_init', function() {
			remove_filter( 'rest_pre_serve_request', 'rest_send_cors_headers' );
			add_filter( 'rest_pre_serve_request', function( $value ) {
				header( 'Access-Control-Allow-Origin: *' );
				header( 'Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE' );
				header( 'Access-Control-Allow-Credentials: true' );
				return $value;
			});
		}, 15 );
	}	
	public function isUserExists($user)
    {
        global $wpdb;
        $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->users WHERE ID = %d", $user));
        if ($count == 1) {return true;} else {return false;}
    }
        
	public function getUserIdByToken($token)
    {
        $decoded_array = array();
        $user_id       = 0;
        if ($token) {
            try{
                $decoded       = JWT::decode($token, JWT_AUTH_SECRET_KEY, array('HS256'));
                $decoded_array = (array) $decoded;
            }
            catch(\Firebase\JWT\ExpiredException $e){

                return false;
            }
        }
        if (count($decoded_array) > 0) {
            $user_id = $decoded_array['data']->user->id;
        }
        if ($this->isUserExists($user_id)) {
            return $user_id;
        } else {
            return false;
        }
    }
    
    function jwt_auth($data, $user) {
        unset($data['user_nicename']);
        unset($data['user_display_name']); 
        $site_url = site_url();
        update_user_meta( $user->ID, 'login_status', 'active');
        //$result = $this->getProfile( $user->ID );
        $result['token'] =  $data['token'];
        return $this->successResponse('User Logged in successfully',$result);
    }

    private function isValidToken(){
    	$this->user_id  = $this->getUserIdByToken($this->user_token);
    }
 
    public function getUser($request) {
        $this->isValidToken();
        $param    = $request->get_params();
        echo $user_id  = !empty($this->user_id)?$this->user_id:$param['user_id'];
        //$userInfo = $this->getProfile( $user_id );

        if(!empty($user_id)){
            return $this->successResponse('User info fetched successfully', $user_id);
        } else {
            return $this->errorResponse('User not exixts.');
        }
    }

	
    public function changePassword($request)
    {
    	$param = $request->get_params();
    	$this->isValidToken();
    	$user_id= !empty($this->user_id)?$this->user_id:$param['user_id'];
    	$current_password = $param['current_password'];
    	$new_password = $param['new_password'];
    	$confirm_password = $param['confirm_password'];
    	$user = get_userdata($user_id);
    	if(!empty($user_id))
    	{
    		if(!wp_check_password( $current_password, $user->data->user_password, $user->ID))
    		{
    			  return $this->errorResponse('Please enter correct current password');
    		}
    		else if($new_password!= $confirm_password)
    		{
    			return $this->errorResponse('New and confirm_password are not same');	
    		}
    		else
    		{
    			$update_pass= update_user_meta($user_id, 'new_password', $new_password);
    			return $this->successResponse('password change successfully');
    		}
    	}
    	else
    	{
    		return $this->errorResponse('Please enter correct user_id');
    	}
    	
    }
	
 	 public function register($request)
 	 {
		$param    = $request->get_params();
        $token = $param['token'];
        $user_id = $this->getUserIdByToken($token);
		$username = $param['username'];
		$useremail = $param['useremail'];
		$userpassword = $param['userpassword'];
		$usermob = $param['usermob'];
		$useraddress = $param['useraddress'];
		$user_id = wp_create_user($username, $userpassword, $useremail);
		if(!empty($user_id))
		{
			$user_data1= update_user_meta($user_id, 'usermob', $usermob);
			 update_user_mata($user_id,'useraddress',$useraddress);
			//$userdata =update_user_mata($userid,'usermob',$usermob);
			$dataVal = $this->fetch($user_Id);
			$this->successResponse("user inserted successfully",$dataVal);			
		}
		else
		{
			$this->errorResponse("Please try again");
		}
 	 
 	 } 	 
}
$object=new MY_RESTAPI();
$object->init();
add_filter('jwt_auth_token_before_dispatch', array($object,'jwt_auth'),10,2);
?>