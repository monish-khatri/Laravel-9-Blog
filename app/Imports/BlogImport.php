<?php

namespace App\Imports;

use App\Models\Blog;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;

class BlogImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Blog([
            'title' => 'Import Title',
            'slug' => 'import-title',
            'description' => 'Import Description',
            'is_published' => true,
            'user_id' => 1,
            'status' => Blog::STATUS_PENDING,
        ]);
    }
}
