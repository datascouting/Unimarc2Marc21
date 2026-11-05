<?php

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class QueryTypeTest extends TestCase
{
    #[DataProvider('typeProvider')]
    public function testToQuery(string $type, string $expected): void
    {
        $this->assertSame($expected, QueryType::toQuery($type));
    }

    public static function typeProvider(): array
    {
        return [
            'isbn'  => ['ISBN (Preferred)', '1=7'],
            'issn'  => ['ISSN', '1=8'],
            'title' => ['Title', '1=4'],
        ];
    }

    public function testDefaultZ3950TypeIsSupported(): void
    {
        $this->assertArrayHasKey(getDefaultZ3950()['z3950_type'], QueryType::getTypes());
    }
}
