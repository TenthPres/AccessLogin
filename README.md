Access Login
============

This library was originally created for integration into our public WiFi system, allowing credentialed members and regular attenders to gain WiFi Access.

This library has none of the WiFi connecting features, but is simply a wrapped for interfacing with ACS Church software.  By passing the library a user email and password, the library then authenticates against the ACS web server, and returns basic personal information through the library. 

The use of this library assumes that your organization is also using ACS Church Management software.  This library won't work without it.  

Other than our customer relationship with ACS, we are not affiliated, and this library is not endorsed by them in any way.  

## Basic Usage

All classes are in the `\Tenth\` namespace. 

Once you include the libraries, call `\Tenth\AccessLogin::login()`

	$users = \Tenth\AccessLogin::login($userEmail, $userPassword);
	
`$users` is now an array of AccessPerson objects, corresponding to people in that family who share the email address. 

So, for the above example, the value of `$users` might be something like:

	array (size=2)
	  0 => 
	    object(Tenth\AccessLogin\AccessPerson)[25]
	      public 'FullName' => string 'John Doe' (length=8)
	      public 'UserName' => string 'JohnDoe' (length=7)
	      public 'SiteNumber' => string '91460' (length=5)
	      public 'SiteName' => string 'Tenth Presbyterian Church' (length=25)
	  1 => 
	    object(Tenth\AccessLogin\AccessPerson)[25]
	      public 'FullName' => string 'Jane Doe' (length=8)
	      public 'UserName' => string 'JaneDoe' (length=7)
	      public 'SiteNumber' => string '91460' (length=5)
	      public 'SiteName' => string 'Tenth Presbyterian Church' (length=25)


## Why So Little Data?

This library uses a particular query to ACS servers which is very quick and efficient, but only provides the limited information seen above.  However, considering that the application designer will already have the user email address, this information should be sufficient to disambiguate between most (admittedly, not all) individuals. 

There are thoughts of expanding this library to make different requests to other endpoints.  However, at it stands, this library is already sufficient for our purposes. 
