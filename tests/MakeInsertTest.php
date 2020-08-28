<?php
use Tests\SQLTest;

class MakeInsertTest extends SQLTest
{
	public function testCanInsertNullValue()
	{
		$db = $this->connect();

		$data = [
			'mytext' => 'Bar',
			'mydate' => null
		];

		$expected = "insert into foo (mytext, mydate)values('Bar', NULL)";
		$actual = $db->makeInsert('foo', $data);
		$db->exec($actual);
		$row = $db->query("select * from foo")->fetch();

		$this->assertEquals($expected, $actual);
		$this->assertEquals(null, $row['mydate'], 'mydate should be null');
		$this->assertEquals('Bar', $row['mytext'], 'mytext should be Bar');
	}

	public function testCanInsertEmptyString()
	{
		$db = $this->connect();

		$data = [
			'mytext' => 'Bar',
			'mydate' => ''
		];

		$expected = "insert into foo (mytext, mydate)values('Bar', '')";
		$actual = $db->makeInsert('foo', $data);

		$db->exec($actual);
		$row = $db->query("select * from foo")->fetch();

		$this->assertEquals($expected, $actual);
		$this->assertEquals('', $row['mydate'], 'mydate should be null');
		$this->assertEquals('Bar', $row['mytext'], 'mytext should be Bar');
	}
}