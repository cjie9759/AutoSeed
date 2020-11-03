<?php
namespace app\index\controller;
use think\Controller;
use app\index\model\Base as BaseM;

class Base extends Controller
{
	public function initialize()
	{
		$data1=[
			// 'urls'        => $url,
			'savepath'       => '/var/qb/dow/',//保存路径
			'category1'      => 'fa2_1',//分类标识，源站点
			'category2'      => 'fa2_2',//分类标识，to
			// 'root_folder' => 'false',
			// 'paused'      => 'false'//测试
        ];
		defined('CONFIG1') || define('CONFIG1', $data1);
		// defined('DATA2') || define('DATA2', $data2);

		$a = BaseM::get(1)->toarray();
		// dump($a);
		/*$a=[
			'id'          => 1,
			'status'      => 1,
			'is_free'     => 0,
			'limitSize'   => 3,
			'timelim'     => 1,
			'totlim'      => 50,
			// 'deptit'      => "发种机v2.0",
			'deptit'      => "测试用种，请勿下载",
			'create_time' => 0,
			'update_time' => 0,
		];*/
		// dump($a);
		// exit();
		defined('BASE') || define('BASE', $a);
		BASE['status'] ===0 && exit('发种机已关闭');
		// echo "string";
	}
}