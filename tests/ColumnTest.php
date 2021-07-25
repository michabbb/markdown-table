<?php

namespace Tests\kbATeam\MarkdownTable;

use kbATeam\MarkdownTable\Column;
use PHPUnit\Framework\TestCase;

class ColumnTest extends TestCase
{
    /**
     * Test creating a left aligned column.
     */
    public function testLeftAlignmentColumn(): void
    {
        $l = new Column('My Column');
        $this->assertEquals('My Column', $l->createHeader());
        $this->assertEquals('---------', $l->createHeaderSeparator());
        $this->assertEquals('a        ', $l->createCell('a'));
    }

    /**
     * Test creating a right aligned column.
     */
    public function testRightAlignmentColumn(): void
    {
        $r = new Column('My Column', Column::ALIGN_RIGHT);
        $this->assertEquals('My Column', $r->createHeader());
        $this->assertEquals('--------:', $r->createHeaderSeparator());
        $this->assertEquals('        a', $r->createCell('a'));
    }

    /**
     * Test creating a centered column.
     */
    public function testCenterAlignmentColumn(): void
    {
        $c = new Column('My Column');
        $c->setAlignment(Column::ALIGN_CENTER);
        $this->assertEquals('My Column', $c->createHeader());
        $this->assertEquals(':-------:', $c->createHeaderSeparator());
        $this->assertEquals('    a    ', $c->createCell('a'));
    }

    /**
     * Test that an overwriting shorter title, which is not the default use-case,
     * results in longer columns.
     */
    public function testOverwritingWithShorterTitle(): void
    {
        $a = new Column('You should not be able to read this.');
        $a->setTitle('My Column');
        $this->assertEquals('My Column                           ', $a->createHeader());
        $this->assertEquals('------------------------------------', $a->createHeaderSeparator());
        $this->assertEquals('a                                   ', $a->createCell('a'));
    }

    /**
     * Test whether a minimum length of three is maintained even if title and content
     * are shorter.
     */
    public function testDefaultLengthOfThree(): void
    {
        $l = new Column('A');
        $l->setMaxLength(mb_strlen('ab'));
        $this->assertEquals('A  ', $l->createHeader());
        $this->assertEquals('---', $l->createHeaderSeparator());
        $this->assertEquals('ab ', $l->createCell('ab'));
    }

    /**
     * Exception test in case something else than a string is used as title.
     */
    public function testNonStringAsTitle(): void
    {
        $this->setExpectedException(\RuntimeException::class, 'Column title is no string.');
        new Column(new \stdClass());
    }

    /**
     * Exception test in case the title is empty.
     */
    public function testShortTitle(): void
    {
        $this->setExpectedException(\RuntimeException::class, 'Column title is too short.');
        new Column('`');
    }

    /**
     * Exception test in case an invalid alignment constant is used.
     */
    public function testInvalidAlignment(): void
    {
        $this->setExpectedException(\RuntimeException::class, 'Invalid alignment constant.');
        new Column('My Column', 1000);
    }

    /**
     * Exception test in case the content to be rendered is unexpectedly long.
     */
    public function testUnexpectedlyLongContent(): void
    {
        $a = new Column('AAA');
        $this->setExpectedException(\RuntimeException::class, 'Content length too long.');
        $a->createCell('aaaa');
    }

    /**
     * Exception test for invalid string lengths.
     */
    public function testNonIntegerMaxLength(): void
    {
        $a = new Column('AAA');
        $this->setExpectedException(\RuntimeException::class, 'Column length needs to be a positive integer.');
        $a->setMaxLength('a');
    }
}
