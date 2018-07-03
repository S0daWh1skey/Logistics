<?php
/**
 * Created by PhpStorm.
 * User: albertshepherd
 * Date: 2018/4/2
 * Time: 3:09 PM
 */
namespace app\admin\validate;
use think\Validate;


class Stuff extends Validate{
    protected $rule=[
        'pwd'  =>  'require|min:1',
        'email' =>  'email',
    ];


}
