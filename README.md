
	AutoSeed可以自动从指定pt站点搬运资源

	源站点（from）
 + 北洋
 	支持站点（to）
 + 银杏

## 特点

 - 自动生成并提交简介，与原种简介尽量一致。
 - 自动清理旧种子、硬盘容量释放。

## 环境要求

- 软件：
  - qBittorrent v4.1+； 
  -	mysql v5.4+;
  - curl;
  - php 5.6+;

## 使用

- clone 本 repo (或者下载 zip) 至本地；

- 使用composer安装必要文件

~~~
cd ./AutoSeed/fa2/
php composer-1.phar install
~~~

- 在 ./AutoSeed/fa2/application/index/controller/Base.php 中修改做种机信息；
- 数据库导入；并在 ./AutoSeed/fa2/application/index/controller/Test.php 中修改相关信息，并执行下列命令，将数据写入数据库

~~~
cd ./AutoSeed/fa2/
php -f public/index.php index/test
~~~

- 配置crontab，定时运行sh脚本；

## 更新日志

- 2020-11-3 --> 1.0
  -发布
