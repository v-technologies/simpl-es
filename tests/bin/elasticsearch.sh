#!/bin/bash

# From Elastica unit tests. https://github.com/ruflin/Elastica/tree/master/test/bin

wget http://download.elasticsearch.org/elasticsearch/elasticsearch/elasticsearch-1.2.1.tar.gz
tar -xzf elasticsearch-1.2.1.tar.gz
sed 's/# index.number_of_shards: 1/index.number_of_shards: 1/' elasticsearch-1.2.1/config/elasticsearch.yml > elasticsearch-1.2.1/config/elasticsearch.yml
sed 's/# index.number_of_replicas: 0/index.number_of_replicas: 0/' elasticsearch-1.2.1/config/elasticsearch.yml > elasticsearch-1.2.1/config/elasticsearch.yml
sed 's/# discovery.zen.ping.multicast.enabled: false/discovery.zen.ping.multicast.enabled: false/' elasticsearch-1.2.1/config/elasticsearch.yml > elasticsearch-1.2.1/config/elasticsearch.yml

export JAVA_OPTS="-server"
elasticsearch-1.2.1/bin/elasticsearch &

echo "Waiting until elasticsearch is ready on port 9200"
while [[ -z `curl -s 'http://localhost:9200' ` ]]
do
	echo -n "."
	sleep 2s
done

echo "Elasticsearch is up"
