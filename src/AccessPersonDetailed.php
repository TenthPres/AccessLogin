<?php

namespace Tenth\AccessLogin {

	/**
	 * Used as a container for the return objects of the login function.
	 *
	 * Class AccessPerson
	 * @package Tenth\AccessLogin
	 */
	class AccessPersonDetailed extends AccessPerson
	{
		/** @var bool Indicates whether the profile page for the individual has been loaded. */
		protected $_hasLoadedProfile = false;

		/** @var int|false A unique number representing the individual in the database. False if it could not be loaded.  */
		public $IndividualId = false;

		public function __construct($arr)
		{
			parent::__construct($arr);
		}
	}
}