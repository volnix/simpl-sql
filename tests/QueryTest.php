<?php
use Tests\SQLTest;

class QueryTest extends SQLTest
{
	public function testCanQuery()
	{
		$db = $this->connect();

		$data = [
			'mytext' => 'Foo',
			'mydate' => null
		];

		$db->pdo->exec("insert into foo (mytext, mydate) values ('bar', '2020-01-01')");
		$result = $db->query("select * from foo");

		$rows = $result->fetchAll();

		$this->assertCount(1, $rows, 'Should return 1 row.');
		$this->assertEquals('bar', $rows[0]['mytext'], 'mytext should equal bar');
		$this->assertEquals('2020-01-01', $rows[0]['mydate']);
	}
}