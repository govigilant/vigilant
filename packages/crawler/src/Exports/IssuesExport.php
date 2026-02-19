<?php

namespace Vigilant\Crawler\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Vigilant\Crawler\Enums\Status;
use Vigilant\Crawler\Models\CrawledUrl;

class IssuesExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(
        protected Collection $collection
    ) {}

    public function collection(): Collection
    {
        return $this->collection;
    }

    public function headings(): array
    {
        return [
            'URL',
            'Status Code',
            'Status',
            'Found On',
            'Ignored',
        ];
    }

    public function map(mixed $row): array
    {
        /** @var CrawledUrl $row */
        return [
            $row->url,
            $row->status,
            Status::tryFrom($row->status)?->label() ?? (string) $row->status,
            $row->foundOn->url ?? '',
            $row->ignored ? 'Yes' : 'No',
        ];
    }
}
