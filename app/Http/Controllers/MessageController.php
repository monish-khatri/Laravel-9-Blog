<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use SebastianBergmann\Complexity\Complexity;
use Yajra\DataTables\DataTables;

class MessageController extends Controller
{
    public function index() {
        $api = Lang::get('api');
        $apiMessages = [];
        $i = 0;
        foreach ($api as $key => $value) {
            $apiMessages[$i]['name'] = $key;
            $apiMessages[$i]['messages'] = $value;
            $i++;
        }
        return view('viewname',compact('apiMessages'));
    }

    /**
     * Display the specified resource.
     *
     * @param \Illuminate\Http\Request
     * @param  string  $key
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$key) {
        try {
            $path = \App::langPath() . '/en/api.php';
            $api = Lang::get('api');
            if($key == $request->name && array_key_exists($key,$api)) {
                $api[$key] = $request->message;
            }
            $output = "<?php\n\nreturn " . var_export($api , true) . ";\n";
            $f = new Filesystem();
            $f->put($path, $output);
            return redirect()->route('ApiMessages')->with("success","Message updated successfully !");
        }catch(Exception $e){
            return redirect()->route('ApiMessages')->with("error","Something went wrong!");
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $key
     * @return \Illuminate\Http\Response
     */
    public function show($key)
    {
        $page = "API Messages Details";
        $api = Lang::get('api');
        $apiMessage = [];
        if(array_key_exists($key,$api)) {
            $apiMessage['name'] = $key;
            $apiMessage['message'] = $api[$key];
        }
        if(! empty($apiMessage)) {
            return view('admin.ApiMessages.view', compact('apiMessage','page'));
        }else{
            return view('admin.layouts.includes.modalError');
        }
    }
}
