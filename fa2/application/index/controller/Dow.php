<?php

namespace app\index\controller;
// use app\index\model\Get as GetM;
// use app\index\model\Dow as DowM;
use app\index\model\Tor;
use app\index\model\Pt;

class Dow extends Base
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
    private function main()
    {
        $totsize = 0;
        $a = Pt::where(['status' => 1, 'is_get' => 1])->select();
        isset($a[0]) || exit('无可用的get_pt');
        $b = Pt::where(['status' => 1, 'is_dow' => 1])->find();
        isset($b) || exit('无可用的dow_');
        foreach ($a as $key => $a1) {
            $tors=null;
            $a1['mod'] === 1 && ($tors = $this->tjupt($a1, $totsize));

            if (!isset($tors) || $tors === 0) {break;}

            $b['mod']===1 && $qb=$this->qb($b,$tors);
        }
        $this -> tordel();
        dump($totsize);
    }

    private function tjupt($a1, &$totsize)
    {
        // $Curl=new Curl;
        // $Tor = new Tor;
        $Pt = new Pt;
        $a1['torurl'] = "/download.php?passkey=" . $a1['passkey'] . "&id=";
        $a1['deturl'] = '/details.php?id=';
        isset($cookie) || isset($a1['cookie']) && $a1['cookietime'] > time() && ($cookie=$a1['cookie']) || ($Curl=new Curl) && ($cookie=$Curl->curllog($a1['host'].$a1['login'],json_decode($a1['logindata']))) && ($a1['cookie']=$a11['cookie']=$cookie) && ($a11['cookietime']=time()+60*60*23) && ($res = $Pt->save($a11,['id'=>$a1['id']]));
        $a1['cookie1']=$cookie;
        $f=0;

        $b = $this->tjtor($a1,$totsize);
        $c = $this->tjdet($a1);
        $d = $this->tjdow($a1);
        $f = $this->tjret($a1);
        return $f;
    }
    // tj
        //1
        private function tjtor($a1,&$totsize)
        {
            // 筛选种子
            $b = Tor::where('status', 0)->where('ref', $a1['id'])->where('time', '>', BASE['timelim'] * 60 * 60 + time())->order('id desc')->where('size', '<', BASE['limitSize'])->select();
            foreach ($b as $key => $value) {
                $totsize += $value['size'];
                // $torurls[$value['id']] = $a1['host'] . $a1['torurl'] . $value['id1'];
                // $deturls[$value['id']] = $a1['host'] . $a1['deturl'] . $value['id1'];
                $torsta[]=['id'=>$value['id'],'status'=>1];
                if ($totsize > BASE['totlim']) {break;}
            }
            if (!isset($torsta)) {return 0;}
            $Tor = new Tor;
            $Tor->saveAll($torsta);
            echo "添加".count($torsta).'条记录(tjupt)';
            return 1;
        }
        // 2
        private function tjdet($a1)
        {
            // $Pt = new Pt;
            // 获取种子详细信息
            $b = Tor::where('status', 1)->where('ref', $a1['id'])->select();
            foreach ($b as $key => $value) {
                // $torurls[$value['id']] = $a1['host'] . $a1['torurl'] . $value['id1'];
                $deturls[$value['id']] = $a1['host'] . $a1['deturl'] . $value['id1'];
                // $torsta[]=['id'=>$value['id'],'status'=>2];
            }
            if (!isset($deturls)) {return 0;}
            $Curl = new Curl;
            $c = $Curl -> mulGet($deturls,$a1['cookie1']);
            foreach ($c as $key => $value) {
                preg_match('/Hash码:<\/b>&nbsp;(.*?)<\/td>/',$value,$hash);
                preg_match('/(?s)<div id=\'kdescr\'>(.*?)<\/div><\/td><\/tr>/',$value,$descr);
                //(?s)(?<=title=\"淘金币).+?(?=<del>)
                isset($descr[1]) && ($descr[1]=strip_tags($descr[1])) && isset($descr[1]{10000}) && $descr[1]=mb_substr($descr[1],0,3000);
                $data1[] = [
                    'id'     => $key,
                    'status' => 2,
                    'hash1'  => $hash[1] ?? 0,
                    'descr'  => $descr[1] ?? 0
                ];
            }
            $Tor = new Tor;
            $Tor->saveAll($data1);
            // exit();
            return 1;
        }
        // 3
        private function tjdow($a1)
        {
            $b = Tor::where('status', 2)->field(['id','id1'])->where('ref', $a1['id'])->select();
            // $torurl=null;
            foreach ($b as $key => $value) {
                $torurls[$value['id']] = $a1['host'] . $a1['torurl'] . $value['id1'];
                $torsta[]=['id'=>$value['id'],'status'=>3];
            }
            if (!isset($torurls)) {return 0;}
            $Curl = new Curl;
            $Curl->mulDown($torurls,$a1['cookie1']);
            $Tor=New Tor;
            $Tor->saveAll($torsta);
            // exit();
            return $torurls;
        }
        private function tjret($a1)
        {
            $b = Tor::where('status', 3)->field(['id','id1'])->where('ref', $a1['id'])->select();
            foreach ($b as $key => $value) {
                $torurls[$value['id']] = $a1['host'] . $a1['torurl'] . $value['id1'];
            }
            if (!isset($torurls)) {return 0;}
            return $torurls;
        }
    private function qb($b,$tors)
    {
        $Curl = new Curl;
        $b['login']='/api/v2/auth/login';
        isset($cookie) || isset($b['cookie']) && $b['cookietime'] > time() && ($cookie=$b['cookie']) || 
        ($cookie=$Curl->curllog($b['host'].$b['login'],json_decode($b['logindata']))) && 
        ($b['cookie']=$b1['cookie']=$cookie) && ($b1['cookietime']=time()+60*10) && ($Pt = new Pt) && ($res = $Pt->save($b1,['id'=>$b['id']]));
        $url = null;
        foreach ($tors as $key => $value) {
            $url .= $value . PHP_EOL;
            $torsta[] = [
                'id' => $key,
                'status' => 4
            ];
        }
        //设置做种机数据
        $data = [
            'urls'        => $url,
            // 'savepath'    => '/var/qb/dow/',
            'savepath'    => CONFIG1['savepath'],
            'category'    => CONFIG1['category1'],//种子类别
            // 'category'    => 'fa2_1',//种子类别
            'root_folder' => 'false',
            'paused'      => 'false'//测试
        ];
        $qbUrl = $b['host'] . '/api/v2/torrents/add';
        $a =$Curl -> mulPost($qbUrl,[$data],$cookie);
        $a && ($Tor=new Tor) && $Tor->saveAll($torsta);
        return 1;
    }
    private function tordel()
    {
        $a =  Tor::where('status','=',0);
        $b = $a->delete();
        echo 'clear '.$b.' tors';
    }
    public function test()
    {
        $b = Pt::where(['status' => 1, 'is_dow' => 1])->find();
        $Curl = new Curl;
        // $b['login']='/api/v2/auth/login';
        $b['login']='/api/v2/auth/login';
        isset($cookie) || isset($b['cookie']) && $b['cookietime'] > time() && ($cookie=$b['cookie']) || 
        ($cookie=$Curl->curllog($b['host'].$b['login'],json_decode($b['logindata']))) && 
        ($b['cookie']=$b1['cookie']=$cookie) && ($b1['cookietime']=time()+60*10) && ($Pt = new Pt) && ($res = $Pt->save($b1,['id'=>$b['id']]));
    }
    /*
        qb下载
        种子下载
        抓取详情页
    */
}