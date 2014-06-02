<?php

/**
 * A aggregate definition
 *
 * @author Chrisophe Sicard <sicard.christophe@gmail.com>
 * @package	Simples
 * @subpackage Request
 */
class Simples_Request_Search_Criteria_Agg extends Simples_Base {

	const
		TYPE_TERM	= 'terms',
		TYPE_AVG	= 'avg'
		;

	protected static $_availableTypes = array(
		self::TYPE_TERM,
		self::TYPE_AVG
	);

	protected static $_availableTypesForAggregation = array(
		self::TYPE_TERM
	);

	/**
	 * Criteria type.
	 *
	 * @var string
	 */
	protected $_type = '';


	/**
	 * Default type.
	 *
	 * @var string
	 */
	protected $_defaultType = self::TYPE_TERM;

	/**
	 * Aggregate normalized data.
	 *
	 * @var array
	 */
	protected $_data = array();

	/**
	 * Options .
	 *
	 * @var array
	 */
	protected $_options = array();

	/**
	 * Fluid return.
	 *
	 * @var mixed
	 */
	protected $_fluid;

	/**
	 * Aggregates builder.
	 *
	 * @var Simples_Request_Search_Builder_Aggs
	 */
	protected $_aggs;

	/**
	 * Constructor.
	 *
	 * @param mixed		$definition		Aggregate definition. String or array.
	 * @param array		$options		Array of options.
	 * @param mixed		$fluid			Fluid object instance.
	 */
	public function __construct($definition = null, array $options = null, $fluid = null) {
		if (isset($definition) || isset($options)) {
			$this->_data = $this->_normalize($definition, $options);
		}

		if (isset($definition)) {
			$this->_type = $this->_type($this->_data, $options);
		}

		if (isset($options)) {
			$this->_options = $options;
		}

		if (isset($fluid)) {
			$this->_fluid = $fluid;
		}

		$this->_aggs = new Simples_Request_Search_Builder_Aggs(null, $this);

		if (isset($this->_options['aggs'])) {
			$this->aggs($this->_options['aggs']);
		}
	}

	/**
	 * Returns the current aggregate type.
	 *
	 * @return string
	 */
	public function type() {
		return $this->_type;
	}

	/**
	 * Get the calculated aggregate name.
	 *
	 * @return string
	 */
	public function name() {
		if (!empty($this->_data['name'])) {
			return $this->_data['name'];
		}
		if (!empty($this->_data['in'])) {
			return $this->_data['in'];
		}
		return null;
	}

	/**
	 * Returns all the normalized data, or only for a key if $key is given.
	 *
	 * @param string	$key	[optionnal]	Key to return.
	 * @return mixed			Normalized data
	 */
	public function get($key = null) {
		if (isset($key)) {
			return isset($this->_data[$key]) ? $this->_data[$key] : null;
		}
		return array_filter($this->_data);
	}

	/**
	 * Detect the aggregate type. Try to detect it if not explicitly defined.
	 *
	 * @param array		$definition		Criteria definition
	 * @param array		$options		Criteria options
	 * @return string					Type.
	 */
	protected function _type(array $definition, array $options = null) {
		if (isset($options['type'])) {
			if (!in_array($options['type'], self::$_availableTypes)) {
				throw new Simples_Request_Exception('Aggregate type not supported');
			}

			return $options['type'];
		}

		return $this->_defaultType;
	}

	/**
	 * Normalize $definition
	 *
	 * @param mixed		$definition		Criteria definition (string/array)
	 * @return array					Normalized definition
	 */
	protected function _normalize($definition) {
		if (is_string($definition)) {
			$definition = array('in' => $definition);
		} else {
                        $in = $this->_in($definition);
                        if (isset($in)) {
                            $definition['in'] = $in;
                            if (isset($definition['field'])) {
                                    unset($definition['field']);
                            }
                            if (isset($definition['fields'])) {
                                    unset($definition['fields']);
                            }
                        }
		}
		return $definition;
	}

	/**
	 * Normalize the search scope (fields/field/in).
	 *
	 * @param array		$definition		Aggregate definition
	 * @return mixed					Scope (string or array)
	 */
	protected function _in($definition) {
		if (isset($definition['in'])) {
			if (is_array($definition['in'])) {
				if (count($definition['in']) === 1) {
					return $definition['in'][0];
				}
			}
			return $definition['in'];
		}
		if (isset($definition['field'])) {
			return $definition['field'];
		}
		if (isset($definition['fields'])) {
			return $definition['fields'];
		}
		return null;
	}

	/**
	 * Prepare data for transformation.
	 *
	 * @return array
	 * @throws Simples_Request_Exception
	 */
	protected function _data(array $options = array()) {
		$data = $this->_data;
		if (empty($data['in']) && empty($data['value_field'])) {
			throw new Simples_Request_Exception('Aggregate error : no scope (keys "field","fields","value_field" and "in" are empty)');
		}

		// Name
		if (empty($data['name']) && !empty($data['in'])) {
			$name = $data['in'];
		} elseif (!empty($data['name'])) {
			$name = $data['name'];
			unset($data['name']);
		} else {
			throw new Simples_Request_Exception('Aggregate error : the aggregate\'s name cannot be determined');
		}

		// Scope
		if (isset($data['in'])) {
			if (is_array($data['in'])) {
				$data['fields'] = $data['in'];
			} else {
				$data['field'] = $data['in'];
			}
			unset($data['in']);
		}

		$return = array($this->type() => $data);

		if ($this->_aggs->count()) {
			$return['aggs'] = $this->_aggs->to('array') ;
		}

		return  array($name => $return );
	}

	/**
	 * Test if a criteria is mergeable with the current criteria.
	 *
	 * @param Simples_Request_Search_Criteria $aggregate		Criteria to test.
	 * @return boolean										Yes/no ?
	 */
	public function mergeable(Simples_Request_Search_Criteria_Agg $aggregate) {
		$data =	$aggregate->get();
		foreach($data as $key => $value) {
			if (isset($this->_data[$key])) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Merge a criteria with current.
	 *
	 * @param Simples_Request_Search_Criteria $aggregate		Criteria to merge.
	 * @return \Simples_Request_Search_Criteria				This instance (fluid interface).
	 */
	public function merge(Simples_Request_Search_Criteria_Agg $aggregate) {
		$this->_data = array_merge($this->_data, $aggregate->get());
		unset($aggregate);
		$this->_type = $this->_type($this->_data, $this->_options);
		return $this;
	}

	/**
	 * Add an aggregate.
	 *
	 * @param mixed		$aggregate			Setter : Query definition.
	 * @param array		$options
	 * @return \Simples_Request_Search	This instance
	 */
	public function agg($aggregate = null, array $options = array()) {
		if (!in_array($this->type(), self::$_availableTypesForAggregation)) {
			throw new Simples_Request_Exception(sprintf('Aggregator [%s] of type [%s] cannot accept sub-aggregations', $this->name(), $this->type()));
		}

		if (isset($aggregate)) {
			$this->_aggs->add($aggregate, $options);
		}

		return $this;
	}

	/**
	 * Add aggregates.
	 *
	 * @param array		$aggregates
	 * @return \Simples_Request_Search	This instance
	 */
	public function aggs(array $aggregates) {
		foreach($aggregates as $aggregate) {
			$options = array();
			if (is_array($aggregate) && isset($aggregate[1])) {
				$options = $aggregate[1];
			}

			if (is_array($aggregate) && isset($aggregate[0])) {
				$aggregate = $aggregate[0];
			}

			$this->agg($aggregate, $options);
		}
	}
}
