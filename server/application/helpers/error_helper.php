<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class ErrorCode
{
    const ERR_OK =   '20000';                   //成功
    const ERR_FAIL = '20001';                   //失败
    const ERR_ACCOUNT = '20010';                //请输入正确的手机/邮箱
    const ERR_AUTH_CODE = '20011';              //输入正确的验证码
    const ERR_PASSWD = '20012';                 //请输入6-8位密码
    const ERR_REGISTER_FAIL = '20013';          //注册失败稍后重试
    const ERR_ACCOUNT_EXTST = '20014';          //账户存在请直接登录
    const ERR_CHANGE_PASSWD = '20015';          //密码修改失败联系客服
    //不需要提示

    const ERR_NO_USER = '50000';                //无用户
    const ERR_PASSWORD = '50001';               //密码错误
    const ERR_RULES = '50002';                  //获取权限错误
    const ERR_ADMIN = '50003';                  //超级用户不可删除
    const ERR_DATABASE = '50004';               //数据库操作失败
    const ERR_TOKEN =   '50008';                //非法的token 
    const ERR_PARAM_UNKNOWN =   '50012';        //其他客户端登录了
    const ERR_SEARCH = '50009';                 //查无数据
    const ERR_HAS_CHILDREN = '50013';          //存在节点不能删除


    public static $CodeMessage = array(
        '20000' => 'success', //成功
        '20001' => '系统错误',
        '20010' => '请输入正确的手机/邮箱',
        '20011' => '输入正确的验证码',
        '20012' => '请输入6-8位密码',
        '20013' => '注册失败稍后重试',
        '20014' => '账户存在请直接登录',
        '20015' => '密码修改失败联系客服',
        '50000' => '无用户',
        '50001' => '密码错误',
        '50002' => '获取权限错误',
        '50003' => '超级用户不可删除',
        '50004' => '数据库操作失败', //
        '50008' => '非法的token',
        '50009' => '查无数据',
        '50012' => '其他客户端登录了',
        '50013' => '存在子节点'


    );
    public static function error_msg($error_code = null)
    {
        $argv = func_get_args();
        if (!isset(ErrorCode::$CodeMessage[$error_code])) {
            $argv[0] = '未定义的错误码：[' . $error_code . ']';
        } else {
            $argv[0] = ErrorCode::$CodeMessage[$error_code];
        }
        return call_user_func_array('sprintf', $argv);
    }
}
