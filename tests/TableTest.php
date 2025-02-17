<?php

namespace Tests\kbATeam\MarkdownTable;

use kbATeam\MarkdownTable\Column;
use kbATeam\MarkdownTable\Table;
use PHPUnit\Framework\TestCase;

class TableTest extends TestCase
{
    /**
     * Test a simple one-column table with a data array.
     */
    public function testSimpleTable(): void
    {
        $t = new Table();
        $t->addColumn(0, new Column('Col.A'));
        $this->assertInstanceOf(Column::class, $t->getColumn(0));
        $expect = 'Col.A'.PHP_EOL
            .'-----'.PHP_EOL
            .'a    '.PHP_EOL
            .'b    '.PHP_EOL
            .'c    '.PHP_EOL;
        $this->assertEquals($expect, $t->getString([
            ['a', 'z'],
            ['b'],
            ['c','y','x']
        ]));
    }

    /**
     * Test a two-column table creation.
     * The names have been generated using fakenamegenerator.com. Any similarities to
     * existing people is not intended.
     */
    public function testConstructorWithColumns(): void
    {
        $t = new Table(['first_name', 'last_name']);
        $expect =  'first_name | last_name   '.PHP_EOL
                  .'---------- | ------------'.PHP_EOL
                  .'Sven       | Frey        '.PHP_EOL
                  .'Clemencia  | Tijerina    '.PHP_EOL
                  .'           | Shervashidze'.PHP_EOL;
        $this->assertEquals($expect, $t->getString([
            ['first_name' => 'Sven', 'last_name' => 'Frey'],
            ['first_name' => 'Clemencia', 'last_name' => 'Tijerina'],
            ['last_name' => 'Shervashidze']
        ]));
    }

    /**
     * Test what happens in case a column is dropped.
     */
    public function testDroppingColumn(): void
    {
        $t = new Table(['first_name', 'last_name']);
        $expect =  'last_name   '.PHP_EOL
                   .'------------'.PHP_EOL
                   .'Frey        '.PHP_EOL
                   .'Tijerina    '.PHP_EOL
                   .'Shervashidze'.PHP_EOL;
        $t->dropColumn('first_name');
        $this->assertEquals($expect, $t->getString([
            ['first_name' => 'Sven', 'last_name' => 'Frey'],
            ['first_name' => 'Clemencia', 'last_name' => 'Tijerina'],
            ['last_name' => 'Shervashidze']
        ]));
    }

    /**
     * Test changing a column title.
     */
    public function testChangeColumnTitle(): void
    {
        $t = new Table(['first_name', 'last_name']);
        $expect =   'first_name | surname     '.PHP_EOL
                   .'---------- | ------------'.PHP_EOL
                   .'Sven       | Frey        '.PHP_EOL
                   .'Clemencia  | Tijerina    '.PHP_EOL
                   .'           | Shervashidze'.PHP_EOL;
        $t->getColumn('last_name')->setTitle('surname');
        $this->assertEquals($expect, $t->getString([
            ['first_name' => 'Sven', 'last_name' => 'Frey'],
            ['first_name' => 'Clemencia', 'last_name' => 'Tijerina'],
            ['last_name' => 'Shervashidze']
        ]));
    }

    /**
     * Test exception when requesting a non existent column.
     */
    public function testExceptionNonExistentColumnPosition(): void
    {
        $t = new Table(['first_name']);
        $this->setExpectedException(\RuntimeException::class, 'Column position last_name does not exist!');
        $t->getColumn('last_name');
    }

    /**
     * Test for the exception thrown in case no columns have been defined.
     */
    public function testExceptionWithNoColumnsDefined(): void
    {
        $t = new Table();
        $this->setExpectedException(\RuntimeException::class, 'No columns defined.');
        $t->getString([['first_name' => 'Sven', 'last_name' => 'Frey']]);
    }

    /**
     * Test for the exception thrown in case a one-dimensional array is provided.
     */
    public function testExceptionWithOneDimensionalArray(): void
    {
        $t = new Table(['first_name', 'last_name']);
        $this->setExpectedException(\RuntimeException::class, 'Rows need to be an array of arrays.');
        $t->getString(['first_name' => 'Sven', 'last_name' => 'Frey']);
    }
}
