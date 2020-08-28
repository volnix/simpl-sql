<?php
namespace Tests;

use PHPUnit\Framework\TestCase;

abstract class SQLTest extends TestCase
{
	public function connect()
	{
		$db = new \Simpl\SQL('sqlite', ':memory:');
		$db->pdo->exec('create table foo(id INTEGER PRIMARY KEY AUTOINCREMENT, mytext varchar, mydate datetime)');
		return $db;
	}
}