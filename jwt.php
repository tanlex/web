<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/4
 * Time: 16:01
 */
require __DIR__.'/vendor/autoload.php';

use \Firebase\JWT\JWT;


class Myjwt{


    private $key = 'kyen.xjjglkgn';

    /**
     * 创建Token
     */
    public function getToken(){
        $key = $this->key; //key
        $time = time(); //当前时间
        $token = [
            'iss' => 'http://www.helloweba.net', //签发者 可选
            'aud' => 'http://www.helloweba.net', //接收该JWT的一方，可选
            'iat' => $time, //签发时间
            'nbf' => $time, //(Not Before)：某个时间点后才能访问，比如设置time+30，表示当前时间30秒后才能使用
            'exp' => $time+7200, //过期时间,这里设置2个小时
            'data' => [ //自定义信息，不要定义敏感信息
                'userid' => 1,
                'username' => '李小龙'
            ]
        ];
        echo JWT::encode($token, $key); //输出Token
    }

    /**
     * 解析Token
     */
    public function getInfoByToken(){
        $key = $this->key; //key要和签发的时候一样

        $jwt = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC93d3cuaGVsbG93ZWJhLm5ldCIsImF1ZCI6Imh0dHA6XC9cL3d3dy5oZWxsb3dlYmEubmV0IiwiaWF0IjoxNTY3NjczNjQ0LCJuYmYiOjE1Njc2NzM3MDQsImV4cCI6MTU2NzY4MDg0NCwiZGF0YSI6eyJ1c2VyaWQiOjEsInVzZXJuYW1lIjoiXHU2NzRlXHU1YzBmXHU5Zjk5In19.S0HniEpDComOo5oYEZlAMCpByyAYQ6x9yp3rPstvpM0"; //签发的Token
        try {
            JWT::$leeway = 60;//当前时间减去60，把时间留点余地
            $decoded = JWT::decode($jwt, $key, ['HS256']); //HS256方式，这里要和签发的时候对应
            $arr = (array)$decoded;
            print_r($arr);

        } catch(\Firebase\JWT\SignatureInvalidException $e) {  //签名不正确
            echo $e->getMessage();
        }catch(\Firebase\JWT\BeforeValidException $e) {  // 签名在某个时间点之后才能用
            echo $e->getMessage();
        }catch(\Firebase\JWT\ExpiredException $e) {  // token过期
            echo $e->getMessage();
        }catch(Exception $e) {  //其他错误
            echo $e->getMessage();
        }
    }


    /**
     * 实战创建Token
     */
    public function authorizations()
    {
        $key = $this->key; //key
        $time = time(); //当前时间

        //公用信息
        $token = [
            'iss' => 'http://www.helloweba.net', //签发者 可选
            'iat' => $time, //签发时间
            'data' => [ //自定义信息，不要定义敏感信息
                'userid' => 1,
            ]
        ];

        $access_token = $token;
        $access_token['scopes'] = 'role_access'; //token标识，请求接口的token
        $access_token['exp'] = $time+7200; //access_token过期时间,这里设置2个小时

        $refresh_token = $token;
        $refresh_token['scopes'] = 'role_refresh'; //token标识，刷新access_token
        $refresh_token['exp'] = $time+(86400 * 30); //access_token过期时间,这里设置30天

        $jsonList = [
            'access_token'=>JWT::encode($access_token,$key),
            'refresh_token'=>JWT::encode($refresh_token,$key),
            'token_type'=>'bearer' //token_type：表示令牌类型，该值大小写不敏感，这里用bearer
        ];
        Header("HTTP/1.1 201 Created");
        echo json_encode($jsonList); //返回给客户端token信息
    }



}

$myjwt = new Myjwt();

//$myjwt->getToken();

//$myjwt->getInfoByToken();

$myjwt->authorizations();
