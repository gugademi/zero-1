<?php 
namespace B7KP\Utils;

use B7KP\Model\Model;
use B7KP\Core\App;

class Login
{
	private $login;
	private $password;
	private $factory;
	private $cookie;
	
	function __construct($login, $password, Model $factory, $cookie = false)
	{
		$this->login 	= $login;
		$this->password = $password;
		$this->factory 	= $factory;
		$this->cookie 	= $cookie;
	}

	public function login($entity = "B7KP\Entity\User")
	{
		$user = $this->factory->findOneBy($entity, $this->login, "login");
		if($user instanceof $entity)
		{
			$login = Pass::check($this->password, $user->password);
			if($login)
			{
				$this->checkSettings($user);
				if($this->cookie)
				{

					setcookie(App::get("name").strtoupper($entity), $user->id, time()+31556926, "/");
					$_COOKIE[App::get("name")."B7KP\ENTITY\USER"] = $user->id;
				}
				else
				{
					$_SESSION[App::get("name")][strtoupper($entity)] = $user->id;
				}
			}
			return $login;
		}
		else
		{
			return false;
		}
	}

	static function logout($entity = "B7KP\Entity\User")
	{
		unset($_SESSION[App::get("name")][strtoupper($entity)]);
		setcookie(App::get("name").strtoupper($entity), false, time()-31556926, "/");
	}

	// k

	private function checkSettings($user)
	{
		$set = $this->factory->findOneBy("B7KP\Entity\Settings", $user->id, "iduser");
		if($set == false)
		{
			$data = \B7KP\Entity\Settings::getAllDefaults($user->login);
			$data->iduser = $user->id;
			$this->factory->add("B7KP\Entity\Settings", $data);
		}
	}
}
?>