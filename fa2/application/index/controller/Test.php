<?php

namespace app\index\controller;
// use app\index\model\Get;
// use app\index\model\Dow;
// use app\index\model\Pos;
use app\index\model\Pt;
use app\index\model\Base;

class Test
{

	public function index()
	{
		$this-> editpt();
		$this-> editbase();
	}
    public function editpt()
    {
        $a = new Pt;

        //各站点登录信息
        $logindatatj = [
            'username' => '',
            'password' => '',
            'logout' => '1day',
        ];
        $logindataqb = [
            'username' => '',
            'password' => '',
            // 'logout'=>'1day',
        ];
        $logindatayx = [
            'username' => '',
            'password' => '',
            'logout' => '1day',
            'loginmethod' => 'username',
        ];
        //站点信息
        $data = [
            [
                'status' => '1',
                'name' => 'tjpt',
                'mod' => 1,
                'is_get' => 1,
                'is_dow' => 0,
                'is_pos' => 0,
                'host' => 'https://www.tjupt.org/',
                'passkey' => '',
                'logindata' => json_encode($logindatayx),
                // 'cookie'=>'',
            ],
            [
                'status' => '1',
                'name' => 'qBtorr',
                'mod' => 1,
                'is_get' => 0,
                'is_dow' => 1,
                'is_pos' => 0,
                'host' => 'http://xxxxxxxxxx',
                'passkey' => '',
                'logindata' => json_encode($logindatayx),
                // 'cookie'=>'',
            ],
            [
                'status' => '1',
                'name' => '银杏pt',
                'mod' => 1,
                'is_get' => 0,
                'is_dow' => 0,
                'is_pos' => 1,
                'host' => 'http://pt.syau.edu.cn',
                'passkey' => '',
                'logindata' => json_encode($logindatayx),
                // 'cookie'=>'',
            ]
        ];
        $b = $a->saveAll($data);
        dump($b);
    }
    public function editbase()
    {
        $a = new Base;

        $data = [
            // 'id'=>,
            'status' => 1,
            'is_free' => 1,
            'limitSize' => 15,
            'timelim' => 1,
            'totlim' => 100,
            'deptit' => '发种机v2.0',
        ];
        // echo json_encode($logindata);
        // exit();
        $b = $a->save($data, ['id' => 1]);
        dump($b);
    }
}
