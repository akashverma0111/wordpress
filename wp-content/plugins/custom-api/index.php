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
	
	public function register_routes() {
		$namespace    = $this->api_namespace . $this->api_version;
        // Api End Points Name 
	    $publicItems  = array('register','finduser','alluser','changepassword','deluser','addreg','updateuser','getUserIdByToken','add_movie_review','addnote','shownote');
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
        add_action('init','create_movie_review');
		add_action( 'rest_api_init', function() {
			remove_filter( 'rest_pre_serve_request', 'rest_send_cors_headers' );
			add_filter( 'rest_pre_serve_request', function( $value ) {
				header( 'Access-Control-Allow-Origin: *' );
				header( 'Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE' );
				header( 'Access-Control-Allow-Credentials: true' );
				return $value;
			});
		}, 15 );
	}/*	
    
    function jwt_auth($data, $user) {
        $result['token'] =  $data['token'];
        return $result;
    }*/
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
     function jwt_auth($data, $user) {
        unset($data['user_nicename']);
        unset($data['user_display_name']); 
        $site_url = site_url();
        update_user_meta( $user->ID, 'login_status', 'active');
        //$result = $this->getProfile( $user->ID );
        $result['token'] =  $data['token'];
        return $this->successResponse('User Logged in successfully',$result);
    }
    /*
   "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvd29yZHByZXNzXC93b3JkcHJlc3MiLCJpYXQiOjE2MjAzNzg0NjUsIm5iZiI6MTYyMDM3ODQ2NSwiZXhwIjoxNjIwOTgzMjY1LCJkYXRhIjp7InVzZXIiOnsiaWQiOiIxIn19fQ.lxGhHaGAR8EVfxolZo23izuSPVgn78jfCJ8AdyjJ38k"

    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvd29yZHByZXNzXC93b3JkcHJlc3MiLCJpYXQiOjE2MjAzNzg0OTksIm5iZiI6MTYyMDM3ODQ5OSwiZXhwIjoxNjIwOTgzMjk5LCJkYXRhIjp7InVzZXIiOnsiaWQiOiIxIn19fQ.ZCO_A59tYu8E1bK_iRp8lX3Gg-HxUjO-_6xE58yz7Yo"
    */
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
            return $user_id;
        }else {
            return false;
        }
    }



 	 public function register($request){
		$param    = $request->get_params();
        //$file     = $_FILES['image'];
		$username = $param['username'];
		$useremail = $param['useremail'];
		$userpassword = $param['userpassword'];
        if(!email_exists($useremail)){
            $user_id = wp_create_user($username, $userpassword, $useremail);
            if(!empty($user_id)){
                return json_encode(array('message'=>"User created successfully!","status"=>"success","code"=>200));
            }
            else{
                return json_encode(array('message'=>"User mot created","status"=>"failed","code"=>401));
            }
        }
 	 } 

     

     public function addreg($request){
        $param=$request->get_params();
		
        $token = $param['token'];
        $id=$this->getUserIdByToken($token);
        $fullname=$param['fullname'];
        $mobile=$param['mobile'];
        $password=$param['password'];
        $quallification=$param['quallification'];
        $address=$param['address'];
            update_user_meta( $id, 'fullname', $fullname);
            update_user_meta( $id, 'mobile', $mobile);
            update_user_meta( $id, 'quallification', $quallification);
            update_user_meta( $id, 'password', $password);
            $result=update_user_meta( $id, 'address', $address);
            if($result==true){
                return $this->successResponse('signup successfully');
            }else{
                return $this->errorResponse('signup error');
            }
     }

      public function addnote($request){
        $param=$request->get_params();
        
        $token = $param['token'];
        $user_id=$this->getUserIdByToken($token);
        $title=$param['title'];
        $note=$param['note'];
        global $wpdb;
        $table = $wpdb->prefix.'notes';
        $data = array('user_id' => $user_id, 'title'=>$title, 'note' => $note);
        $format = array('%d','%s','%s');
        $wpdb->insert($table,$data,$format);
        $my_id = $wpdb->insert_id;
        if ($my_id>0) {
            
        return $this->successResponse('note added successfully',$my_id);
        }else{
            
        return $this->errorResponse('error',$my_id);
        }
     }

     public function shownote($request){
        $param=$request->get_params();
        
        $token = $param['token'];
        $user_id=$this->getUserIdByToken($token);

        global $wpdb;
        $data = $wpdb-> get_results( "SELECT * FROM wp_notes where user_id= $user_id " );
        if (!empty($data)){
            return $this->successResponse('note show successfully', $data);
        }else{
            return $this->errorResponse('note show error', $data);
        }
     }

     
     public function finduser($request) {

       $param    = $request->get_params();
        $token = $param['token'];
        $id=$this->getUserIdByToken($token);
        $result=get_userdata($id);

        return $result;
     }
     public function alluser(){
        $result=get_users();
        return $result;
     }
     public function deluser($request){
        $param=$request->get_params();
        $id=$param['id'];
        $result=wp_delete_user( $id );
        return $result;
     }
     public function changepassword($request){
        $param= $request->get_params();
        $id=$param['id'];
        $oldpass=$param['oldpass'];
        $newpass=$param['newpass'];
        $conpass=$param['conpass'];
        $user = get_userdata($id);
        $dbpass=$user->data->user_pass;
        if(wp_check_password( $oldpass, $dbpass, $id)){
            if($newpass==$conpass){
                if(!wp_check_password($newpass, $dbpass, $id)){
                    wp_set_password($newpass, $id);
                    return "changepassword successfully";

                }else{
                    return "your new password not be same old password";
                }
            }else{
                return "your confirm password is wrong";
            }
        }else{
            return "your old password is wrong";
        }

     }

     public function updateuser($request){
        $param=$request->get_params();
        $token=$param['token'];
        $mobile=$param['mobile'];
        $quallification=$param['quallification'];
        $address=$param['address'];
        $id=$this->getUserIdByToken($token);
        if(!empty($id))
        {

            update_user_meta( $id, 'mobile', $mobile);
            update_user_meta( $id, 'quallification', $quallification);
            update_user_meta( $id, 'address', $address);
            return json_encode(array('message'=>"User update successfully","status"=>"success","code"=>401));
        }
        else{
            return json_encode(array('message'=>"User mot updateed","status"=>"failed","code"=>401));
        }
    }

function create_movie_review(){
    register_post_type('movie_review',array(
        'lables' => array(
            'name'=>'movie review',
            'singular_name'=>'movie review',
            'add_new'=>'add new',
            'add_new_item'=>'add new movie review',
            'eidt'=>'edit',
            'eidt_item'=>'edit movie review',
            'view'=>'view',
            'view_item'=>'view movie review',
            'search_item'=>'search movie review',
            'not_found'=>'movie review not found',
            'not_found_in_trash'=>'movie review found in trash',
            'parent'=>'parent movie review'

        ), 
        'public'=>true,
        'menu_position'=>15,
        'supports'=>array('title','editor','comments','thumbnail','custom-fields'),
        'taxonomies'=>array(''),
        'menu_icon'=>plugins_url('images/akash.jpg',__FILE__),
        'has_archive'=>true
    )
    
    );
}


    public function add_movie_review($request){
             $param=$request->get_params();
             $token=$param['token'];
             $id=$this->getUserIdByToken($token);
             $movie_review = array(
            'post_title'    => $param['title'],
            'post_content'  => $param['content'],
            'post_status'   => 'publish',
            'post_author'   => $id,
            'post_category' => array( 8,39 )
            );
        $id=wp_insert_post( $movie_review);
        $user = get_postdata($id);
        $response= array('message'=>"movie review added successfully!","status"=>"success","code"=>200,'data'=>$user);
        return new WP_REST_Response($response, 200); 

    }

}
$object=new MY_RESTAPI();
$object->init();
add_filter('jwt_auth_token_before_dispatch', array($object,'jwt_auth'),10,2);

?>