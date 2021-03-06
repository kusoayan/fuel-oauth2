<?php
/**
 * Facebook OAuth2 Provider
 *
 * @package    FuelPHP/OAuth2
 * @category   Provider
 * @author     Phil Sturgeon
 * @copyright  (c) 2012 HappyNinjas Ltd
 * @license    http://philsturgeon.co.uk/code/dbad-license
 */

namespace OAuth2;

class Provider_Facebook extends Provider
{  
	public $scope = array('offline_access', 'email', 'read_stream');

	public function url_authorize()
	{
		return 'https://www.facebook.com/dialog/oauth';
	}

	public function url_access_token()
	{
		return 'https://graph.facebook.com/oauth/access_token';
	}

	public function get_user_info(Token_Access $token)
	{
		$url = 'https://graph.facebook.com/me?'.http_build_query(array(
			'access_token' => $token->access_token,
		));

		$user = json_decode(file_get_contents($url));

        // if user has no username, then use email.
        if (isset($user->email))
        {
            $username_from_email = explode('@',$user->email);
            $username_from_email = $username_from_email[0];
        }
        
		// Create a response from the request
		return array(
			'uid' => $user->id,
			'full_name' => $user->name,
			'username' => isset($user->username) ? $user->username : $username_from_email,
			'email' => isset($user->email) ? $user->email : null,
			'image' => 'https://graph.facebook.com/me/picture?type=normal&access_token='.$token->access_token,
			'urls' => array(
			  'Facebook' => $user->link,
			),
		);
	}
}
