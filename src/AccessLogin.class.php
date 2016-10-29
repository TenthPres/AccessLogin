<?php

namespace TenthPres {

	use GuzzleHttp\Client;
	use GuzzleHttp\Cookie\CookieJar;
	use GuzzleHttp\Cookie\SetCookie;
	use TenthPres\AccessLogin\AccessPerson;

	class AccessLogin {
		/**
		 * The login function.  Essentially wraps the web client's request to https://secure.accessacs.com/access/login.ashx
		 *
		 * @param string $email The user's email address.
		 * @param string $password The user's password.
		 * @param int|string $site The site ID to use for ACS.  Defaults to Tenth's site ID, which is 91460.
		 * @return false|array An array of AccessPerson objects, or false if the password is wrong.
		 */
		public static function login($email, $password, $site = 91460)
		{
			if (!filter_var($email, FILTER_VALIDATE_EMAIL))
				return false;

			$jar = new CookieJar(false, [
				new SetCookie([ // probably not necessary, but whatev.
					'Name' => 'SiteNumber',
					'Value' => 'SiteID=' . $site,
					'Domain' => 'secure.accessacs.com',
					'Path' => '/',
					'Max-Age' => null,
					'Expires' => null,
					'Secure' => false,
					'Discard' => false,
					'HttpOnly' => false])
			]);

			$client = new Client(['cookies' => $jar]);

			$client->request('GET', 'https://secure.accessacs.com/access/login.aspx', [
				'cookies' => $jar
			]);

			$res = $client->request('POST', 'https://secure.accessacs.com/access/login.ashx', [
				'form_params' => [
					'email' => $email,
					'pwd' => $password,
					'site' => '91460'
				],
				'delay' => 100,
				'cookies' => $jar
			]);
			$body = $res->getBody();

			// if password is wrong, return false.
			if ($body == '') {
				return false;
			}
			$json = json_decode($body, true);

			$r = [];

			foreach ($json as $j) {
				$r[] = new AccessPerson($j);
			}

			return $r;
		}
	}
}