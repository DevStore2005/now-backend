<?php

namespace App\Exports;

use App\Utils\UserType;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;

class ProfileExport implements WithMultipleSheets, WithTitle
{
    use Exportable;

    /**
     * Profile of sheets
     * @return array
     */
    public function sheets(): array
    {
        return [
            'Users' => new UsersExport,
            'Provider Profiles' => new ProviderProfileExport,
        ];
    }

    /**
     * Title of the sheet
     * @return string
     */
    public function title(): string
    {
        return 'Profiles';
    }
}
