<?php

class InflectorTest extends PHPUnit_Framework_TestCase {

	public function testPluralMethodReturnsPluralFormOfWord()
	{
		$this->assertEquals(Inflector::plural('friend'), 'friends');
		$this->assertEquals(Inflector::plural('campus'), 'campuses');
		$this->assertEquals(Inflector::plural('sheep'), 'sheep');
		$this->assertEquals(Inflector::plural('city'), 'cities');
		$this->assertEquals(Inflector::plural('child'), 'children');
	}

	public function testSingularMethodReturnsSingularFormOfWords()
	{
		$this->assertEquals(Inflector::singular('friends'), 'friend');
		$this->assertEquals(Inflector::singular('campuses'), 'campus');
		$this->assertEquals(Inflector::singular('sheep'), 'sheep');
		$this->assertEquals(Inflector::singular('cities'), 'city');
		$this->assertEquals(Inflector::singular('children'), 'child');
	}

	public function testPluralIfMethodReturnsPluralFormWhenCountIsGreaterThanZero()
	{
		$this->assertEquals(Inflector::plural_if('friend', 2), 'friends');
	}

	public function testPluralIfMethodReturnsSingularFormWhenCountIsLessThanOrEqualToOne()
	{
		$this->assertEquals(Inflector::plural_if('friend', 1), 'friend');
		$this->assertEquals(Inflector::plural_if('friend', 0), 'friend');
		$this->assertEquals(Inflector::plural_if('friend', -1), 'friend');
	}

}