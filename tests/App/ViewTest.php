<?php

declare(strict_types=1);

namespace Unimarc2Marc21\Tests\App;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Unimarc2Marc21\App\View;
use Unimarc2Marc21\Models\Response;

final class ViewTest extends TestCase
{
    #[DataProvider('visibilityProvider')]
    public function testObjectVisibility(int $status, bool $hidden): void
    {
        $response = new Response($status, '');

        $this->assertSame($hidden, View::mustHideObjects($response));
        $this->assertSame($hidden ? 'd-none' : '', View::getHideClassOrEmpty($response));
    }

    public static function visibilityProvider(): array
    {
        return [
            'wait'          => [STATUS_WAIT, true],
            'search ok'     => [STATUS_SEARCH_OK, false],
            'search not ok' => [STATUS_SEARCH_NOT_OK, true],
            'import ok'     => [STATUS_IMPORT_OK, false],
            'import not ok' => [STATUS_IMPORT_NOT_OK, false],
        ];
    }

    public function testEscapesHtml(): void
    {
        $this->assertSame('&lt;/textarea&gt;&lt;script&gt;', View::e('</textarea><script>'));
    }
}
