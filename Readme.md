# Simpl-ES : an ElasticSearch PHP API. Simpler. Really.

[![Build Status](https://secure.travis-ci.org/v-technologies/simpl-es.png)](http://travis-ci.org/v-technologies/simpl-es)

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

	// Search
	$response = $client->search()
		->should()
			->match('Morrison')->in('lastname')
			->match('Jim')
		->not()
			->match('inspiration')->in(array('type','status'))
		->facets(array('type','status'))
		->size(5)
		->execute() ;

	// Print your results
	echo 'Search tooked ' . $response->took . 'ms. ' . $response->hits->total . ' results ! ' ;

Documentation
-------------

Doc is available in the wiki pages. I have juste started writing it, so you maybe won't find what you are looking for. But
be sure it will evolve quickly, in the next days/weeks !

Compatibility
-------------

Simpl-ES is continuously tested on PHP 5.2.x, 5.3.x, 5.4.x and 5.5.x . It's actually developped for a 5.2 usage, but we will certainly create a 5.3 branche in order to use namespaces.

We have implemented PSR-0 guidelines, so you can use your generic autoload method to work with it.

Installation
------------

The simplest way to install and use Simpl-ES is to use Composer, as there is a package on Packagist. Just add this to your project composer.json file :

	{
		"minimum-stability": "dev",
	    "require": {
	        "v-technologies/simpl-es": "*"
	    }
	}

As there isn't yet a stable version, you have to add the "minimum-stability" clause to your file. If you don't do that, Composer won't be able to see Simpl-ES.

Help us
-------

You can help us by sending feedback on the issues page, and hey ... fork it, share it and use it !

Contact
-------

Sébastien Charrier : sebastien@vtech.fr
