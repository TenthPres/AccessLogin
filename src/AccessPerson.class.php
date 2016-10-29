<?php

namespace TenthPres\AccessLogin {

	/**
	 * Used as a container for the return objects of the login function.
	 *
	 * Class AccessPerson
	 * @package TenthPres\AccessLogin
	 */
	class AccessPerson
	{
		/** @var string $FullName The full name of the user, with spaces */
		public $FullName;

		/** @var string $UserName The user name of the user, which is most likely the full name without spaces. */
		public $UserName;

		/** @var string $SiteNumber A string of an int, representing the site number, which was probably passed as a parameter to the login function. */
		public $SiteNumber;

		/** @var string $SiteName The name of the site, which is probably the name of the church. */
		public $SiteName;

		public function __construct($arr)
		{
			foreach ($arr as $field => $value) {
				if (property_exists(__CLASS__, $field)) {
					$this->$field = $value;
				}
			}
		}
	}
}