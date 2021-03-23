<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use ApkParser\Parser;
use Illuminate\Console\Command;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calculating:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'calculating test';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \ApkParser\Exceptions\XmlParserException
     */
    public function handle()
    {
        dump('start');
        $apkParser = new Parser(storage_path('app/public/app-release.apk'));

        dump($apkParser->getManifest()->getPackageName()); //获取apk包名
        dump($apkParser->getManifest()->getVersionCode()); //获取apk版本号
        dump($apkParser->getManifest()->getVersionName()); //获取apk版本名称

        dump($apkParser->getManifest()->getMinSdk()); // 支持最低sdk的平台
        dump($apkParser->getManifest()->getMinSdkLevel()); // 支持最低sdk的版本
        dump($apkParser->getManifest()->getTargetSdk()); // 目标Sdk的平台
        dump($apkParser->getManifest()->getTargetSdkLevel()); //目标sdk的版本

        $labelIndex = $apkParser->getManifest()->getApplication()->getLabel(); //获取应用名称的索引
        dump($apkParser->getResources($labelIndex)); //获取应用名称的数组

        $iconIndex = $apkParser->getManifest()->getApplication()->getIcon();//获取图标的索引
        dump($icons = $apkParser->getResources($iconIndex)); //获取图标路径的数组
        dump('end');
    }

}
