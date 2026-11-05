<?php

declare(strict_types=1);

namespace Unimarc2Marc21\Tests\Enums;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Unimarc2Marc21\Enums\QueryType;

final class QueryTypeTest extends TestCase
{
    #[DataProvider('typeProvider')]
    public function testAttribute(string $label, string $attribute): void
    {
        $this->assertSame($attribute, QueryType::from($label)->attribute());
    }

    public static function typeProvider(): array
    {
        return [
            'isbn'  => ['ISBN (Preferred)', '1=7'],
            'issn'  => ['ISSN', '1=8'],
            'title' => ['Title', '1=4'],
        ];
    }

    public function testUnknownTypeIsRejected(): void
    {
        $this->assertNull(QueryType::tryFrom('Author'));
    }

    public function testDefaultZ3950TypeIsSupported(): void
    {
        $this->assertNotNull(QueryType::tryFrom(getDefaultZ3950()['z3950_type']));
    }
}
