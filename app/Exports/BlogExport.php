<?php

namespace App\Exports;

use App\Models\Blog;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BlogExport implements FromCollection, WithHeadings
{
    public function headings(): array
    {
        return [
            '########',
            '########',
            '########',
            '########',
            '########',
            '########',
            '########',
            '########',
            '########',
            '########',
            '########',
            '########',
        ];
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Blog::all();
    }
    // public function view(): View
    // {
    //     $blogs = Blog::sortable()
    //         ->orderBy('id', 'desc')
    //         ->whereNot('status',Blog::STATUS_DRAFT);

    //     return view('blog.index', [
    //         'blogs' => $blogs,
    //         'published' => false,
    //     ]);
    // }
}
