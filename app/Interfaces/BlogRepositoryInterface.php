<?php

namespace App\Interfaces;

use App\Http\Requests\StoreBlogRequest;
use Illuminate\Http\Request;

interface BlogRepositoryInterface
{
    public function apiData();
    public function index();
    public function published();
    public function create();
    public function store(StoreBlogRequest $request);
    public function show($id);
    public function edit($id);
    public function update(Request $request, $id);
    public function destroy($id);
    public function forceDestroy($id);
    public function trashBin();
    public function restore($id);
    public function restoreAll();
    public function blogStatus($blog);
    public function updateStatus(Request $request,$id);
    public function currentlyViewByUsers($id);
}