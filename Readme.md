# Simpl-ES : an ElasticSearch PHP API. Simplier. Really.

[![Build Status](https://secure.travis-ci.org/scharrier/simpl-es.png)](http://travis-ci.org/scharrier/simpl-es) [![Maintained Status](http://stillmaintained.com/scharrier/simpl-es.png)](http://stillmaintained.com/scharrier/simpl-es)

What ?!
-------
Yep. It's another Elasticsearch PHP client. Everybody knows Elastica, wich is (was ? ;)) certainly the most advanced client in our PHP world. This is a great work (and a source of inspiration for me), but it's too complex in my opinion. I love fluid interfaces, magic deductions, and I really hate writing code when system can think for me.

So here we are : Simpl-ES (Simples, for intimate) is the ES  PHP client for lazy people, like me. It's actually in development, but as we (at V-Technologies) are using it in real projects, it will evolve quickly.

Teasing
-------
	
	// Connect
	$client = Simples::connect(array(
		'host' => 'my.es-server.net',
		'index' => 'directory',
		'type' => 'contact'
	)) ;

	// Index
	$client->index(array(
		'firstname' => 'Jim',
		'lastname' => 'Morrison',
		'type' => 'inspiration'
	))->execute() ;

	// Search
	$response = $client->search()
		->should()
			->match('Morrison')->in('lastname')
			->match('Jim')
		->not()
			->match('inspiration')->in(array('type','status'))
		->size(5)
		->execute() ;

	// Print your results
	echo 'Search tooked ' . $response->took . 'ms. ' . $response->hits->total . ' results ! ' ;

Compatibility
-------------

Simpl-ES is continuously tested on PHP 5.2.x, 5.3.x and 5.4.x. . It'as actually developped for a 5.2 usage, but we will certainly create a 5.3 branche in order to use namespaces.

We have implemented PSR-0 guidelines, so you can use your generic autoload method to work with it.

Help us
-------

You can help us by sending feedback on the issues page, and hey ... fork it, share it and use it !

Contact
-------

Sébastien Charrier : sebastien@vtech.fr