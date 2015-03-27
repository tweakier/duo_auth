<?php
/**
 * Two Factor Authentication using Duo Security for RoundCube
 *
 * @version 1.01
 *
 * Author(s): Alexios Polychronopoulos <dev@pushret.co.uk>
 * Date: 27/03/2015
 */

require_once 'duo_web.php';

class duo_auth extends rcube_plugin 
{
	
	function init() 
	{
		$rcmail = rcmail::get_instance();
		
		$this->add_hook('login_after', array($this, 'login_after'));
		$this->add_hook('send_page', array($this, 'check_2FA'));
   	    	 
		$this->load_config();
	}

	//hook called after successful user/pass authentication.
	function login_after($args)
	{
		$rcmail = rcmail::get_instance();
		
		$this->register_handler('plugin.body', array($this, 'generate_html'));
		
		//indicates that user/pass authentication has succeeded.
		$_SESSION['_Duo_Auth'] = True;
    	
		$rcmail->output->send('plugin');
	}
    
	//intermediate page for Duo 2FA. Fetches the Duo javascript, initializes Duo and renders the Duo iframe.
	function generate_html() 
	{
		$rcmail = rcmail::get_instance();
		$rcmail->output->set_pagetitle('Duo Authentication');
		
		$this->include_script('Duo-Web-v1.bundled.min.js');
		
		$ikey = $this->get('IKEY');
		$skey = $this->get('SKEY');
        	$host = $this->get('HOST');
        	$akey = $this->get('AKEY');

        	$user = get_input_value('_user', RCUBE_INPUT_POST);
        	
		$sig_request = Duo::signRequest($ikey, $skey, $akey, $user);

		$content =	"<script>
						Duo.init({
							'host': '" . $host . "',
							'post_action': '.',
							'sig_request': '" . $sig_request . "'
						});
				</script>
				<center>	
					<iframe id=\"duo_iframe\" width=\"620\" height=\"500\" frameborder=\"0\" allowtransparency=\"true\" style=\"background: transparent;\"></iframe>
				</center>";	
		
		return($content);
	}
	
	
	//hook called on every roundcube page request. Makes sure that user is authenticated using 2 factors.
	function check_2FA($p)
	{
		$rcmail = rcmail::get_instance();
		
		//user has gone through 2FA
		if($_SESSION['_Duo_Auth'] && $_SESSION['_Duo_2FAuth']) 
		{
			return $p;
		}
		
		//login page has to allow requests that are not 2 factor authenticated.
		else if($rcmail->task == 'login')
		{
			return $p;
		}
		
		//checking 2nd factor of authentication.
		else if(isset($_POST['sig_response']))
		{
			$ikey = $this->get('IKEY');
			$skey = $this->get('SKEY');
			$akey = $this->get('AKEY');
			
			$resp = Duo::verifyResponse($ikey, $skey, $akey, $_POST['sig_response']);
			
			//successful 2FA login.
			if($resp != NULL)
			{
				//indicates successful Duo 2FA.
				$_SESSION['_Duo_2FAuth'] = True;
				
				//redirect to inbox.
				header('Location: ?_task=mail');
				return $p;
			}
			else {
				$this->fail();
			}
		}
		
		//in any other case, log the user out.
		$this->fail();
	}

	private function get($v)
	{
		return rcmail::get_instance()->config->get($v);
	}
	
	//unsets all the session variables used in the plugin, 
	//invalidates the user's session and redirects to the login page.
	private function fail() 
	{
		$rcmail = rcmail::get_instance();
		
		unset($_SESSION['_Duo_Auth']);
		unset($_SESSION['_Duo_2FAuth']);
		
		$rcmail->kill_session();
		header('Location: ?_task=login');
		
		exit;
	}	
	
}
