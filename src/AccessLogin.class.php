<?php

namespace Tenth {

	use GuzzleHttp\Client;
	use GuzzleHttp\Cookie\CookieJar;
	use GuzzleHttp\Cookie\SetCookie;
	use Tenth\AccessLogin\AccessPerson;
	use Tenth\AccessLogin\AccessPersonDetailed;
	use Tenth\AccessLogin\AccessException;

	class AccessLogin {
		/**
		 * The login function.  Essentially wraps the web client's request to https://secure.accessacs.com/access/login.ashx
		 *
		 * @param string $email The user's email address.
		 * @param string $password The user's password.
		 * @param bool $detailed Whether the result set should contain detailed results, in the form of AccessPersonDetailed objects.
		 * @param int|string $site The site ID to use for ACS.  Defaults to Tenth's site ID, which is 91460.
		 * @return false|AccessPerson[]|AccessPersonDetailed[] An array of AccessPerson or AccessPersonDetailed objects, or false if the password is wrong.
		 *
		 * @throws AccessException Thrown if detailed results are requested but individuals can't be disambiguated to align information properly.
		 */
		public static function login($email, $password, $detailed = false, $site = 91460)
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

			$loginPage = $client->request('GET', 'https://secure.accessacs.com/access/memberlogin.aspx?sn=' . $site, [
				'cookies' => $jar
			]);

			$jsonLogin = $client->request('POST', 'https://secure.accessacs.com/access/login.ashx', [
				'form_params' => [
					'email' => $email,
					'pwd' => $password,
					'site' => $site
				],
				'delay' => 100,
				'cookies' => $jar
			]);
			$body = $jsonLogin->getBody();

			// if password is wrong, return false.
			if ($body == '') {
				return false;
			}
			$json = json_decode($body, true);



			if ($detailed !== true) {
				/** @var AccessPerson[] $r */
				$r = [];

				foreach ($json as $j) {
					$r[] = new AccessPerson($j);
				}
			} else {
				/** @var AccessPersonDetailed[] $r */
				$r = [];

				foreach ($json as $j) {
					$r[] = new AccessPersonDetailed($j);
				}

				// parse out needed values from response
				$loginPageBody = $loginPage->getBody();
				$matches = [];

				preg_match('/"__EVENTVALIDATION" value="(\S+)"/', $loginPageBody, $matches);
				$eventValidation = $matches[1];
				preg_match('/"__VIEWSTATE" value="(\S+)"/', $loginPageBody, $matches);
				$viewState = $matches[1];
				preg_match('/"__VIEWSTATEGENERATOR" value="(\S+)"/', $loginPageBody, $matches);
				$viewStateGenerator = $matches[1];

				$params = [
					'__LASTFOCUS' => '',
					'__EVENTTARGET' => '',
					'__EVENTARGUMENT' => '',
					'__VIEWSTATE' => $viewState,
					'__VIEWSTATEGENERATOR' => $viewStateGenerator,
					'__EVENTVALIDATION' => $eventValidation,
					'ctl00$cphMain$Login1$loginType' => 'email',
					'ctl00$cphMain$Login1$isMemberLogin' => 'true',
					'ctl00$cphMain$Login1$loginControlState' => 'login',
					'ctl00$cphMain$Login1$siteNumber' => $site,
					'ctl00$cphMain$Login1$unifiedLoginId' => '',
					'ctl00$cphMain$Login1$txtUserName' => $r[0]->UserName,
					'ctl00$cphMain$Login1$txtEmail' => $email,
					'ctl00$cphMain$Login1$txtPassword' => $password,
					'ctl00$cphMain$Login1$btnSignIn' => 'Sign In',
					'ctl00$cphMain$dfEmail' => '',
					'ctl00$cphMain$dfFirstName' => '',
					'ctl00$cphMain$dfLastName' => '',
					'ctl00$cphMain$ddSuffix' => '',
					'ctl00$cphMain$pageSiteNumber' => $site
				];

				// process second login page (the one after the XHR request)
				$client->request('POST', 'https://secure.accessacs.com/access/memberlogin.aspx?sn=' . $site, [
					'form_params' => $params,
					'delay' => 100,
					'cookies' => $jar
				]);

				// load default profile page
				$profilePage = $client->request('GET', 'https://secure.accessacs.com/access/people/viewperson.aspx?src=menu', [
					'delay' => 200,
					'cookies' => $jar
				]);

				$profileBody = $profilePage->getBody();

				// get id numbers for each member of the family.
				preg_match_all('/viewperson.aspx\?indvid=(\d+)">([\sA-z\.]+)<\/a>/', $profileBody, $matches, PREG_PATTERN_ORDER);
				// $matches[1] is an array of the IDs
				// $matches[2] is an array of the full names of the individuals.

				if (count($matches[2]) !== count(array_unique($matches[2]))) {
					throw new AccessException("Individual names are not unique, and cannot currently be disambiguated.");
				}

				// match ID numbers to individuals.
				foreach ($r as &$person) {
					foreach ($matches[2] as $k => $name) {
						if ($name === $person->FullName) {
							$person->IndividualId = $matches[1][$k];
						}
					}
				}

			}

			return $r;
		}
	}
}