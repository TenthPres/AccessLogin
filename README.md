Access Login
============

This library was originally created for integration into our public WiFi system, allowing credentialed members and regular attenders to gain WiFi Access.

This library has none of the WiFi connecting features, but is simply a wrapper for interfacing with ACS Church software.  By passing the library a user email and password, the library then authenticates against the ACS web server, and returns user information through the library. 

The use of this library assumes that your organization is using ACS Church Management software.  This library won't work without it.  

Other than our customer relationship with ACS, we are not affiliated, and this library is not endorsed or supported by them in any way.  

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


## Detailed Requests

In the event that you need a unique identifier or more detailed user information, you may want to use a Detailed Request, instead of the Basic Request.  

Detailed Requests do everything in a Basic Request, but then also load the profile landing page that the user would see when logging in themselves.  

**Detailed Requests are dramatically more resource-intensive to process and take dramatically longer to load** than Basic Requests.  Thus, use Basic Requests when possible.  See explanation below for a more thorough explanation. 

To perform a Detailed Request, add the `detailed` parameter to the `\Tenth\AccessLogin::login()` call, like this:

	$users = \Tenth\AccessLogin::login($userEmail, $userPassword, true);
	
With that, `$users` contains an array of AccessPersonDetailed objects, corresponding to people in that family who share the email address.  These are the same as the AccessPerson objects returned from Basic Requests, with the addition of an `IndividualId` attribute corresponding to the individual's unique ID in the ACS database. 

For the above example, the value of `$users` might be something like:

	array (size=2)
	  0 => 
	    object(Tenth\AccessLogin\AccessPersonDetailed)[39]
	      public 'IndividualId' => int 17022
	      public 'FullName' => string 'John Doe' (length=8)
	      public 'UserName' => string 'JohnDoe' (length=7)
	      public 'SiteNumber' => string '91460' (length=5)
	      public 'SiteName' => string 'Tenth Presbyterian Church' (length=25)
	  1 => 
	    object(Tenth\AccessLogin\AccessPersonDetailed)[40]
	      public 'IndividualId' => int 17023
	      public 'FullName' => string 'Jane Doe' (length=8)
	      public 'UserName' => string 'JaneDoe' (length=7)
	      public 'SiteNumber' => string '91460' (length=5)
	      public 'SiteName' => string 'Tenth Presbyterian Church' (length=25)

Additionally, other fields are available on demand, queried from the user's profile page.  These additional fields are:  
 - PhotoUrl
 - Title
 - FirstName
 - MiddleName
 - GoesByName
 - LastName
 - Suffix
 - Gender
 - FamilyPosition
 - DateAdded
 - DateLastChanged
 - MaritalStatus
 - MemberStatus
 - DateJoined
 - Active
 - (Site-Defined Fields)

Each of these fields comes from the labels on the user's profile page, with the spaces removed.  Thus, if you have a site-defined field called "Parish Number", the resulting field name would be `ParishNumber`.  All values returned from these on-demand fields will be in string format, even if the value is numeric or boolean. 

These fields are accessed the same way as the static fields.  For instance, with the same hypothetical users are the other examples:

	$users = \Tenth\AccessLogin::login($userEmail, $userPassword, true);
	var_dump($users[0]->Gender);
	
Produces the output:

	string 'Male' (length=4)
	


### Why You Should Use Basic Requests Instead of Detailed Requests

Data is sexy.  We get it.  We tend to crave maximal data.  However, in some cases, those data come at a cost.  

For Basic Requests, this library uses a particular query to ACS servers which is very quick and efficient, but only provides the limited information provided in AccessPerson objects.  

In some cases, though, a unique identifier or other profile information may be necessary.  This is why Detailed Requests exist.  However, they should be used as sparingly as possible, and the application designer *must* ensure that the end user is not kept waiting without any indications of progress while this data is loaded. 


## Info for Contributors

If you wish to contribute to this project, thank you!  Be sure to add yourself to the `authors` section of composer.json in your first pull request. 


### Test Coverage
Unit tests cover 100% of lines in src *IF* you have compliant data.  

In order to have full coverage, you'll need:
 - a set of valid credentials for an ACS user.
 - credentials for an ACS user account who is part of a family where multiple people have the same name.  e.g. John Smith and John Smith Jr.  Ideally, that 'Jr.' part would be omitted such that a true ambiguity clearly exists. 
