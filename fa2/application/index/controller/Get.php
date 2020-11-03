<?php
namespace app\index\controller;
// use QL\QueryList;
// use app\index\model\Get as GetM;
use app\index\model\Tor;
use app\index\model\Pt;

class Get extends Base
{
	public function index()
	{
		$this->main();
	}
	public function s()
    {
        sleep(random_int(0, 60*60*2));
        $this->main();
    }
	public function main()
	{
		$a=Pt::where('status',1)->where('is_get',1)->select();
		foreach ($a as $key => $a1) {
			$a1['mod'] === 1 && $b=$this->tjupt($a1);
		}

	}
	private function tjupt($a1)
	{
		$Curl=new Curl;
		$Tor=new Tor;
		$Pt=new Pt;
		$a1['login']='/takelogin.php';
		$count=0;
		//设置资源链接
		$souurl11=[
			'/torrents.php?inclbookmarked=0&incldead=0&picktype=0&spstate=2&page=0',
			// '/torrents.php?inclbookmarked=0&incldead=0&picktype=0&spstate=2&page=1',
			// '/torrents.php?inclbookmarked=0&incldead=0&picktype=0&spstate=2&page=2',
		];
		$souurl12=[
			'/torrents.php?page=0',
			'/torrents.php?page=1',
			'/torrents.php?page=2',
			// '/torrents.php?page=3',
		];
		BASE['is_free'] === 1 && ($souurl1=$souurl11) || $souurl1=$souurl12;
		foreach ($souurl1 as $key => $value) {
			$souurl[]=$a1['host'].$value;
		}

		// $cookie="__cfduid=d70b193772aadce48e0aa1f2a0ba883d81594363040;c_secure_uid=MTAxNDcw;c_secure_pass=3c98d30c6c2ecbb56aa15b90bb89ab9b;c_secure_ssl=eWVhaA%3D%3D;c_secure_tracker_ssl=eWVhaA%3D%3D;c_secure_login=bm9wZQ%3D%3D;";
		// $c=json_decode(file_get_contents("log/2020-07-10.log"));

		isset($cookie) || 
		isset($a1['cookie']) && $a1['cookietime'] > time() && ($cookie=$a1['cookie']) || 
		($cookie=$Curl->curllog($a1['host'].$a1['login'],json_decode($a1['logindata']))) && ($a1['cookie']=$a11['cookie']=$cookie) && ($a11['cookietime']=time()+60*60*23) && ($res = $Pt->save($a11,['id'=>$a1['id']]));
		// dump($souurl);
		// exit();
		isset($c) || $c=$Curl->mulGet($souurl,$cookie,30);
		foreach ($c as $key => $value) {
			$d=$this->tjuptsourceDel($value,$a1['id']);
			isset($d) && ($g=$Tor->saveAll($d)) && $count+=count($g);
		}
		dump($count);
	}
    private function tjuptSourceDel($data,$ref)
    {
        //(?s)<div id=\'kdescr\'>(.*?)<\/div><\/td><\/tr>
        $data1=explode('rowfollow nowrap" valign="middle',$data);
        $data3 =null;
        unset($data1[0]);
        // for ($i=1; $i < count($data1); $i++) { 
        foreach ($data1 as $key => $value) {
        	$time1=null;
            preg_match('/剩余时间：<span title="(\d{4}-\d{1,2}-\d{1,2} \d{2}:\d{2}:\d{2})/',$value,$time);
            preg_match('/id=(\d{6})/',$value,$id);
            preg_match('/ollow">(\d{1,3}\.\d{1,3})<br \/>(.{3})/',$value,$size);
            preg_match('/<a title="(.*?)"/',$value,$name);
            isset($time[1]) && $time1 = strtotime($time[1]);
            $size[2]==='MiB' && ($size[1]=$size[1] / 1024) || $size[2]==='TiB' && $size[1]=$size[1] * 1024;
          	$data2 =array(
				'id1' => $id[1] ?? null,
				'time' => $time1 ?? 9999999999,
				'size' => $size[1],
				'name' => mb_substr($name[1],0,250) ?? 'name异常',
				'ref'=>$ref,
				// 'i' => $i,
				'status' => 0
           	);           
			//判断数据是否已存在
			isset($id[1]) && $a=Tor::where('id1',$id[1])->find();
			is_null($a) && isset($id[1]) && $data3[] = $data2;
        }
        return $data3;
    }
}