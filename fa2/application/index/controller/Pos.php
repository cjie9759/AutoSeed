<?php

namespace app\index\controller;

use app\index\model\Tor;
use app\index\model\Pt;

class Pos extends Base
{
	public function index()
	{
		$this->main();
	}
	public function s()
	{
		sleep(60 * 20);
		$this->main();
	}
	public function main()
	{
		// $a=Pt::where('status',1)->where('is_pos',1)->select();
		$a = Pt::where(['status' => 1, 'is_pos' => 1])->find();
		isset($a) || exit('无可用的pos_Pt');
		$b = Pt::where(['status' => 1, 'is_dow' => 1])->find();
		isset($b) || exit('无可用的dow_');

		$b['login'] = '/api/v2/auth/login';
		// $b['login']='';

		isset($b['cookie']) && $b['cookietime'] > time() ||
			($Curl = new Curl) && ($Pt = new Pt) && ($cookie = $Curl->curllog($b['host'] . $b['login'], json_decode($b['logindata']))) && ($b['cookie'] = $b1['cookie'] = $cookie) && ($b1['cookietime'] = time() + 60 * 10) && $res = $Pt->save($b1, ['id' => $b['id']]);

		$b['mod'] === 1 && $b1 = $this->qbche($b);
		$a['mod'] === 1 && $a1 = $this->yxpos($a);
		$a['mod'] === 1 && $tors = $this->yxret($a);


		isset($tors) && $tors !== 0 || exit('tors异常');
		dump(count($tors));

		$b['mod'] === 1 && $b2 = $this->qbdow($b, $tors);

		$b['mod'] === 1 && $b3 = $this->qbdel($b);


		// 状态检查 qbche
		// yx发布 yx
		// 获取id 
		// qb下载 qbdow
		// qb清理空间
	}
	// 6
	private function yxpos($a1)
	{
		$a1['login'] = '/takelogin.php';
		$url = $a1['host'] . '/takeupload.php';
		$tors = Tor::where('status', 5)->select();
		if (!isset($tors[0])) {
			return 0;
		}
		$befDesc = BASE['befDsec'] ?? '该种由自动发种机发布' . PHP_EOL;


		isset($cookie) ||
			isset($a1['cookie']) && $a1['cookietime'] > time() && ($cookie = $a1['cookie']) ||
			($Curl = new Curl) && ($Pt = new Pt) && ($cookie = $Curl->curllog($a1['host'] . $a1['login'], json_decode($a1['logindata']))) && ($a1['cookie'] = $a11['cookie'] = $cookie) && ($a11['cookietime'] = time() + 60 * 60 * 23) && $res = $Pt->save($a11, ['id' => $a1['id']]);
		$path = './torrents/';

		foreach ($tors as $key => $value) {
			$torrentPath = $path . $value['id'] . '.torrent';
			$cfile = curl_file_create($torrentPath, 'application/x-bittorrent', $value['name'] . '.torrent');
			$data[$value['id']] = [
				'file'        => $cfile,
				'uplver'      => 'yes',
				'descr'       => $befDesc . $value['descr'],
				'source_sel'  => '49',
				'type'        => '409',
				'dburl'       => null,
				'name'        => $value["name"],
				'small_descr' => BASE['deptit'] . $value['id'],
				'imdburl'     => NULL,
				'nfo'         => NULL,
				'id'          => $value['id'],
			];
			// $data[] = $b;
		}
		$Curl = new Curl;
		$res = $Curl->mulPost($url, $data, $cookie, 60, 1, 0, 0);
//		$res = $Curl->mulPost('127.0.0.1',$data,$cookie,60,1,0,0);
		foreach ($res as $key => $value) {
			$res1 = preg_match('/pt\.syau\.edu\.cn\/details\.php\?id=(\d{5,6})/', $value, $id2);
			isset($id2[0]) && ($status = 6) || $status = 5;
			$torsta[] = [
				'id'    => $key,
				'id2'   => $id2[1] ?? null,
				'status' => $status ?? 5,
			];
		}
		$Tor = new Tor;
		$Tor->saveAll($torsta);
		dump($res);
		dump($torsta);
		return 1;
	}
	private function yxret($a1)
	{
		$a1['torurl'] = "/download.php?passkey=" . $a1['passkey'] . "&id=";
		$b = Tor::where('status', 6)->field(['id', 'id2'])->select();
		foreach ($b as $key => $value) {
			$torurls[$value['id']] = $a1['host'] . $a1['torurl'] . $value['id2'];
		}
		if (!isset($torurls)) {
			return 0;
		}
		return $torurls;
	}
	// 5
	private function qbche($b1)
	{
		$tors = Tor::where('status', 4)->field(['id', 'hash1'])->select();
		if (!isset($tors[0])) {
			return 0;
		}
		$url = $b1['host'] . '/api/v2/torrents/properties';
		// $url = $b1['host'] . '/api/v2/torrents/pieceStates';
		$Curl = new Curl;
		foreach ($tors as $key => $value) {
			$data[$value['id']] = ['hash' => $value['hash1']];
		}
		$res = $Curl->mulPost($url, $data, $b1['cookie'], 20, 0, 0, 0);
		foreach ($res as $key => $value) {
			if ($value != '') {
				$time = json_decode($value)->completion_date;
				$time !== -1 && ($status = 5) || $status = 4;
				$torsta[] = [
					'id' => $key,
					'status' => $status ?? 4
				];
			}
		}
		$Tor = new Tor;
		isset($torsta) && $Tor->saveAll($torsta);
		return 1;
	}
	// 7
	private function qbdow($b1, $tors)
	{
		$Curl = new Curl;
		$url = null;
		foreach ($tors as $key => $value) {
			$url .= $value . PHP_EOL;
			$torsta[] = [
				'id' => $key,
				'status' => 7
			];
		}
		//设置做种机数据
		$data = [
			'urls'        => $url,
			// 'savepath'    => '/var/qb/dow/',
			'savepath'    => CONFIG1['savepath'],
            'category'    => CONFIG1['category2'],
			// 'category'    => 'fa2_2', //种子类别
			'skip_checking' => 'true',
			'root_folder' => 'false',
			'paused'      => 'false' //测试
		];
		$qbUrl = $b1['host'] . '/api/v2/torrents/add';
		$a = $Curl->mulPost($qbUrl, [$data], $b1['cookie']);
		$a && ($Tor = new Tor) && $Tor->saveAll($torsta);

		return 1;
	}
	private function qbdel($b1 = null)
	{
		$b1 = Pt::where(['status' => 1, 'is_dow' => 1])->find();
		isset($b1) || exit('无可用的dow_');

		$b1['login'] = '/api/v2/auth/login';
		$curl = new Curl;


		isset($b1['cookie']) && $b1['cookietime'] > time() ||
			($Curl = new Curl) && ($Pt = new Pt) && ($cookie = $Curl->curllog($b1['host'] . $b1['login'], json_decode($b1['logindata']))) && ($b1['cookie'] = $b11['cookie'] = $cookie) && ($b11['cookietime'] = time() + 60 * 10) && $res = $Pt->save($b11, ['id' => $b1['id']]);

		$b1['torslist'] = '/api/v2/torrents/info';
		$b1['sync'] = '/api/v2/sync/maindata';
		$b1['del'] = '/api/v2/torrents/delete';

		do {
			$sync = $curl->mulGet([$b1['host'] . $b1['sync']], $b1['cookie']);
			$disk = json_decode($sync[0])->server_state->free_space_on_disk;
			$disk = intval($disk / 1024 / 1024 / 1024);

			echo "free_disk {$disk}GB" . PHP_EOL;
			$disk > 300 && exit('finish');
			// $disk < 200 && ehco 'continue';
			$infodata = null;
			$infodata[] = [
				'filter'   => '', //串	按状态过滤种子列表。允许的状态滤波器：all，downloading，completed，paused，active，inactive，resumed，stalled，stalled_uploading，stalled_downloading
				'category' => 'fa2_1', //串	获取具有给定类别的种子（空字符串表示“无类别”；没有“ category”参数表示“任何类别” <-直到解析＃11748为止）。记住要对类别名称进行URL编码。例如，My category变为My%20category
				'sort'     => 'added_on', //串	按给定键对种子进行排序。可以使用响应的JSON数组（在下面记录）的任何字段作为排序键对它们进行排序。
				// 'reverse'  => 'true' , //布尔	启用反向排序。默认为false
				'limit'    => '10', //整数	限制返回的种子数量
				// 'offset'   => '' , //整数	设置偏移量（如果小于0，则从末端偏移）
				// 'hashes'   => '' , //串	按哈希过滤。可以包含多个散列，由|
			];
			$url = $b1['host'] . $b1['torslist'];
			// $dickinfo = $curl ->mulGet([$b1['host'].$b1['diskinfo']],$b1['cookie']);
			// dump (json_decode($dickinfo[0]));

			$resinfo = $curl->mulPost($url, $infodata, $b1['cookie']);
			$resinfo = array_reverse(json_decode($resinfo[0]));
			$hashs = null;
			foreach ($resinfo as $key => $value) {
				$hashs .= $value->hash . '|';
			}
			$deldata = null;
			$deldata[] = [
				'hashes' => $hashs,
				'deleteFiles' => 'true',
			];
			$delurl = $b1['host'] . $b1['del'];
			$curl->mulPost($delurl, $deldata, $b1['cookie']);
			sleep(30);
		} while (true);

		// json_decode()
	}
}
