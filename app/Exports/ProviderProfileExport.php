<?php

namespace App\Exports;

use App\Models\ProviderProfile;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class ProviderProfileExport implements FromCollection, WithHeadings, ShouldAutoSize, WithTitle
{
    use Exportable;

    public function headings(): array
    {
        return DB::getSchemaBuilder()->getColumnListing('provider_profiles');
    }

    /** 
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return ProviderProfile::get();
    }

    /**
     * Title of the Provider Profile
     * @return string
     */
    public function title(): string
    {
        return 'Provider Profiles';
    }
}
