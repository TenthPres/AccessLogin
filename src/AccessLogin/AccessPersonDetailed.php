<?php

namespace Tenth\AccessLogin {

	/**
	 * Used as a container for the return objects of the login function.
	 *
	 * @class AccessPerson
	 * @package Tenth\AccessLogin
	 *
	 * @property-read string PhotoUrl
	 * @property-read string Title
	 * @property-read string FirstName
	 * @property-read string MiddleName
	 * @property-read string GoesByName
	 * @property-read string LastName
	 * @property-read string Suffix
	 * @property-read string Gender
	 * @property-read string FamilyPosition
	 * @property-read string DateAdded
	 * @property-read string DateLastChanged
	 * @property-read string MaritalStatus
	 * @property-read string MemberStatus
	 * @property-read string DateJoined
	 * @property-read string Active
	 */
	class AccessPersonDetailed extends AccessPerson
	{
		/** @var bool Indicates whether the profile page for the individual has been loaded.  */
		protected $_hasLoadedProfile = false;

		/** @var string[] parsed profile info, stored in an associative array.  */
		protected $_profileData = [];

		/** @var \GuzzleHttp\Client The Guzzle Client from the original request, for use in loading profiles.  */
		protected $_client;

		/** @var int|false A unique number representing the individual in the database. False if loading failed.  */
		public $IndividualId = false;

		/**
		 * AccessPersonDetailed constructor.  Same as the AccessPerson constructor, but adding the client parameter.
		 * @param string[] $arr
		 * @param /GuzzleHttp/Client $client
		 */
		public function __construct($arr, &$client)
		{
			$this->_client = &$client;
			parent::__construct($arr);
		}

		/**
		 * @param string $what The name of the field to get
		 * @return string The value of the requested field.
		 * @throws AccessException Thrown if the particular person doesn't have the requested data field.
		 */
		public function __get($what)
		{
			$this->_loadProfile();
			if (isset($this->_profileData[$what]))
				return $this->_profileData[$what];

			$adjustedFieldName = trim(preg_replace("/[A-Z]/", " $0",$what));
			if (isset($this->_profileData[$adjustedFieldName]))
				return $this->_profileData[$adjustedFieldName];

			throw new AccessException("This AccessPersonDetailed object does not have a " . $what . " data field.");
		}

		/**
		 * Returns a list (array) of the field names that can be accessed through the default getter.
		 */
		public function getAvailableFields()
		{
			$this->_loadProfile();
			return preg_replace("/ ([A-Z])/", "$1", array_keys($this->_profileData));
		}

		/**
		 * Loads data from the user profile into _profileData array if it hasn't been loaded before.
		 * Does not return anything.
		 *
		 */
		protected function _loadProfile()
		{
			if (!$this->_hasLoadedProfile)
			{
				$profilePage = $this->_client->request('GET', 'https://secure.accessacs.com/access/people/viewperson.aspx\?indvid=' . $this->IndividualId)->getBody();

//				echo $profilePage;

				$r = [];
				$r['PhotoUrl'] = preg_match("/<img id=\"ctl00_ctl00_cphMain_cphSubMenu_ucIndvInfo_imgIndvPict\" src=\"([\\w:\\/.]*)/", $profilePage, $matches) ? $matches[1] : null;
				$r['PhotoUrl'] = str_replace("../../", "https://secure.accessacs.com/access/", $r['PhotoUrl']); // Returns default image when no user image is available.

				preg_match_all("/<label>[\\W]*([\\w\\d ]+)[\\W]*:<\\/label>\\W*(<span[\\w\"= ]+>)?([\\w\\d \\/\\.]*)\\W*(<\\/span|\\&nbsp;)/mU", $profilePage, $matches);

				foreach ($matches[1] as $i => $k)
					$r[ucwords($k)] = $matches[3][$i];

				$this->_profileData = $r;
				$this->_hasLoadedProfile = true;
			}
		}
	}
}