<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class UsersExport implements FromCollection, WithHeadings, ShouldAutoSize, WithTitle
{
    use Exportable;

    /**
     * private variable
     * @var string $_role
     */
    private $_role;

    public function __construct(string $role = null)
    {
        $this->_role = $role ? Str::upper($role) : null;
    }

    public function headings(): array
    {
        $columns = DB::getSchemaBuilder()->getColumnListing('users');
        return array_filter(
            $columns,
            fn ($column) =>
            $column != 'password' && $column != 'remember_token'
        );
    }

    /** 
     * @return \Illuminate\Support\Collection|null
     */
    public function collection()
    {
        if ($this->_role) return User::whereRole($this->_role)->get();
    }

    /**
     * Title of the Users
     * @return string
     */
    public function title(): string
    {
        return 'Users';
    }
}
