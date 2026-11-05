<?php

declare(strict_types=1);

namespace Unimarc2Marc21\Enums;

/**
 * Supported search types; the value is the label shown in the UI and posted by the form.
 */
enum QueryType: string
{
    case Isbn = 'ISBN (Preferred)';
    case Issn = 'ISSN';
    case Title = 'Title';

    /**
     * Bib-1 use attribute for the PQF query.
     */
    public function attribute(): string
    {
        return match ($this) {
            self::Isbn => '1=7',
            self::Issn => '1=8',
            self::Title => '1=4',
        };
    }
}
