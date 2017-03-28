Access Login
============

This library was originally created for integration into our public WiFi system, allowing credentialed members and regular attenders to gain WiFi Access.

This library has none of the WiFi connecting features, but is simply a wrapped for interfacing with ACS Church software.  By passing the library a user email and password, the library then authenticates against the ACS web server, and returns basic personal information through the library. 

The use of this library assumes that your organization is also using ACS Church Management software.  This library won't work without it.  

Other than our customer relationship with ACS, we are not affiliated, and this library is not endorsed by them in any way.  

## Basic Requests

All classes are in the `\Tenth\` namespace. 

Once you include the libraries, call `\Tenth\AccessLogin::login()` like this:

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
	    object(Tenth\AccessLogin\AccessPerson)[26]
	      public 'FullName' => string 'Jane Doe' (length=8)
	      public 'UserName' => string 'JaneDoe' (length=7)
	      public 'SiteNumber' => string '91460' (length=5)
	      public 'SiteName' => string 'Tenth Presbyterian Church' (length=25)


### Why You Should Use Basic Requests Instead of Detailed Requests

For Basic Requests, this library uses a particular query to ACS servers which is very quick and efficient, but only provides the limited information seen above.  However, considering that the application designer will already have the user email address, this information should be sufficient to disambiguate between most (admittedly, not all) individuals. 

If, however, this information is insufficient, you may find that Detailed Requests, described below, better meet your needs.

## Detailed Requests

In the event that you need a unique identifier, you may want to use a Detailed Request, instead of the Basic Request.  

Detailed Requests do everything in a Basic Request, but then also load the profile landing page that the user would see when logging in themselves.  

**Detailed Requests are dramatically more resource-intensive to process and take dramatically longer to load** than Basic Requests.  Thus, use Basic Requests when possible. 

To perform a Detailed Request, add the `detailed` parameter to the `\Tenth\AccessLogin::login()` call, like this:

	$users = \Tenth\AccessLogin::login($userEmail, $userPassword, true);
	
With that, `$users` contains an array of AccessPersonDetailed objects, corresponding to people in that family who share the email address.  These are the same as the AccessPerson objects returned from Basic Requests, with the addition of an `IndividualId` attribute corresponding to the individual's unique ID in the ACS database, as well as a private `_hasLoadedProfile` attribute indicating whether the user's profile page has been loaded and parsed.  There is not yet support for the loading and parsing of individual profile pages. 

For the above example, the value of `$users` might be something like:

	array (size=2)
	  0 => 
	    object(Tenth\AccessLogin\AccessPersonDetailed)[39]
	      protected '_hasLoadedProfile' => boolean false
          public 'IndividualId' => int 17022
	      public 'FullName' => string 'John Doe' (length=8)
	      public 'UserName' => string 'JohnDoe' (length=7)
	      public 'SiteNumber' => string '91460' (length=5)
	      public 'SiteName' => string 'Tenth Presbyterian Church' (length=25)
	  1 => 
	    object(Tenth\AccessLogin\AccessPerson)[40]
	      protected '_hasLoadedProfile' => boolean false
          public 'IndividualId' => int 17023
	      public 'FullName' => string 'Jane Doe' (length=8)
	      public 'UserName' => string 'JaneDoe' (length=7)
	      public 'SiteNumber' => string '91460' (length=5)
	      public 'SiteName' => string 'Tenth Presbyterian Church' (length=25)


