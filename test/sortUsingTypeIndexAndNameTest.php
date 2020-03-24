<?php // Copyright (c) 2019 Geoffroy Arnoud, Guillaume Rousse, and SWITCHwayf contributors

use PHPUnit\Framework\TestCase;

require("../lib//functions.php");


final class SortTest extends TestCase
{
    public function testRemoveAccents()
    {
        $string  = "Foo";
        $this->assertEquals(
          removeAccents($string),
          "Foo"
        );

        $string  = "Fôö";
        $this->assertEquals(
          removeAccents($string),
          "Foo"
        );

        $this->assertLessThan(
          0,
          strcasecmp(removeAccents("École"), removeAccents("Foo"))
      );
    }

    public function testSortAccents()
    {
        $first{"Name"} = "Bar";
        $last{"Name"} = "Foo";

        $this->assertLessThan(
          0,
          sortUsingTypeIndexAndName($first, $last)
      );

        $first{"Name"} = "École";
        $last{"Name"} = "Foo";

        $this->assertLessThan(
          0,
          sortUsingTypeIndexAndName($first, $last)
      );
    }
}
