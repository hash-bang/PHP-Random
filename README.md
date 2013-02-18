PHP-Random
==========
Various handy PHP random number generation functions all in one class.

	// Initalize the class
	$r = new Random();

	// Figure out what is the best method to use given a list
	echo $r->GetPrefered(array('openssl', 'mt_rand', 'rand'));

	// Is the OpenSSL method available?
	echo $r->IsAvailable('openssl');

	// Set OpenSSL as the default
	echo $r->Method('openssl');

	// Geneate a random float using the default method (see above)
	echo $r->Rand();

	// Generate a random number between 0 and 100 using the default method
	echo $r->Rand(0, 100);

	// Specify a specific range and method
	echo $r->Rand(0, 100, 'mt_rand');
