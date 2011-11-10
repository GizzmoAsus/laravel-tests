<?php use Laravel\Inflector;

class InflectorTest extends PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider singularToPluralProvider
	 */
	public function test_singluar_to_plural($singular, $plural)
	{
		$this->assertEquals($plural, Inflector::plural($singular));
	}

	// /**
	//  * @dataProvider pluralProvider
	//  */
	// public function test_plural_of_a_plural($plural)
	// {
	// 	$this->assertEquals($plural, Inflector::plural($plural));
	// }

	/**
	 * @dataProvider singularToPluralProvider
	 */
	public function test_plural_to_singular($singular, $plural)
	{
		$this->assertEquals($singular, Inflector::singular($plural));
	}

	// /**
	//  * @dataProvider singularProvider
	//  */
	// public function test_singular_of_a_singular($singular)
	// {
	// 	$this->assertEquals($singular, Inflector::singular($singular));
	// }

	/**
	 * @dataProvider uncountableProvider
	 */
	public function test_plural_of_an_uncountable($noun)
	{
		$this->assertEquals($noun, Inflector::plural($noun));
	}
	
	// /**
	//  * @dataProvider uncountableProvider
	//  */
	// public function test_singular_of_an_uncountable($noun)
	// {
	// 	$this->assertEquals($noun, Inflector::singular($noun));
	// }

	// /**
	//  * @dataProvider compoundNounProvider
	//  */
	// public function test_plural_of_a_compound_noun($singular, $plural)
	// {
	// 	$this->assertEquals($plural, Inflector::plural($singular));
	// }
	// 
	// /**
	//  * @dataProvider compoundNounProvider
	//  */
	// public function test_singular_of_a_compound_noun($singular, $plural)
	// {
	// 	$this->assertEquals($singular, Inflector::singular($plural));
	// }
	
	/**
	 * @dataProvider pluralIfProvider
	 */
	public function test_plural_if($singular, $plural, $count)
	{
		$this->assertEquals($plural, Inflector::plural($singular, $count));
	}



	public function singularToPluralProvider()
	{
		return array(
			array('apple', 'apples'),
			array('cat', 'cats'),
			array('movie', 'movies'),
			array('search', 'searches'),
			array('fix', 'fixes'),
			array('process', 'processes'),
			array('dish', 'dishes'),
			array('status', 'statuses'),
			array('quiz', 'quizzes'),
			array('query', 'queries'),
			array('half', 'halves'),
			array('wife', 'wives'),
			array('diagnosis', 'diagnoses'),
			array('axis', 'axes'),
			array('alias', 'aliases'),
			array('militia', 'militias'),
			array('tomato', 'tomatoes'),
			array('index', 'indices'),
			array('matrix', 'matrices'),
			array('medium', 'media'),
			array('datum', 'data'),
			array('woman', 'women'),
			array('spokesman', 'spokesmen'),
			array('person', 'people'),
			array('spokesperson', 'spokespeople'),
			array('child', 'children'),
			array('foot', 'feet'),
			array('louse', 'lice'),
			array('goose', 'geese'),
			array('tooth', 'teeth'),
			array('mouse', 'mice'),
			array('ox', 'oxen'),
			// array('stimulus', 'stimuli'),
			// array('crisis', 'crises'),
			// array('criterion', 'criteria'),
			// array('phenomenon', 'phenomena'),
		);
	}

	public function pluralProvider()
	{
		return array_map(function($pair)
		{
			return array($pair[1]);
		}, $this->singularToPluralProvider());
	}

	public function singularProvider()
	{
		return array_map(function($pair)
		{
			return array($pair[0]);
		}, $this->singularToPluralProvider());
	}

	public function uncountableProvider()
	{
		return array(
			array('audio'),
			array('deer'),
			array('equipment'),
			array('fish'),
			array('information'),
			array('money'),
			array('rice'),
			array('series'),
			array('sheep'),
			array('species'),
			array('news'),
			array('gold'),
			array('police'),
		);
	}

	public function compoundNounProvider()
	{
		return array(
			array('runner–up', 'runners–up'),
			array('passer–by', 'passers–by'),
			array('man–of–war', 'men–of–war'),
		);
	}

	public function pluralIfProvider()
	{
		return array(
			array('cat', 'cats', 0),
			array('cat', 'cat',  1),
			array('cat', 'cats', 2),
		);
	}

}
