<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Contact;

class UsersImport implements ToModel
{

    
    /**
     * @param array $row
     *
     * @return User|null
     */

    public function model(array $row)
    {
        return new Contact([
           'full_name'=> $row[0],
           'phone_number' => $row[1], 
           'email'=> $row[2], 
           'acadamic_dep'=> $row[3], 
           'fellow_dep' => $row[4], 
           'gender'=> $row[5], 
           'graduate_year'=>$row[6], 
           'phone_number'=>$row[7], 
           'is_under_graduate'=> $row[8], 
        ]);
    }
}
