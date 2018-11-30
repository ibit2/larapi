<?php

namespace App\Http\Controllers\Native;

use App\Foundations\SnowFlake;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BaseController extends Controller
{

    protected function success($data=null,$msg="success"){

        $rs=[
            'code'=>0,
            'msg'=>$msg,
        ];
        if($data!==null){
            $rs['data']=$data;
        }
        return response()->json($rs);
    }

    protected function fail($msg="error",$code=-1){
        $rs=[
            'code'=>$code,
            'msg'=>$msg,
        ];
        return response()->json($rs);

    }

    public function getSnowFlakeId(){
        return (new  SnowFlake())->snowId();
    }
}
