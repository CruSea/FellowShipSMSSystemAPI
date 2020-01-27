<?php

namespace App\Exports;

use App\GroupContact;
use Maatwebsite\Excel\Concerns\FromCollection;

class GroupExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return GroupContact::all();
    }

}
