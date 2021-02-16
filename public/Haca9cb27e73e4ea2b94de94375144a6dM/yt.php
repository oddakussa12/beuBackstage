<?php
//获取App今天与昨天的统计数据
include_once ('config.php');
$type = intval(isset($_GET['type'])?$_GET['type']:1);


// ---------------------------引入接口参数类(以用户实际路径为准)---------------------------------

include_once ('com/umeng/uapp/param/UmengUappGetTodayYesterdayDataParam.class.php');
include_once ('com/umeng/uapp/param/UmengUappGetTodayYesterdayDataResult.class.php');

// ---------------------------引入SDK工具类(以用户实际路径为准)---------------------------------
include_once ('com/alibaba/openapi/client/policy/RequestPolicy.class.php');
include_once ('com/alibaba/openapi/client/entity/ByteArray.class.php');
include_once ('com/alibaba/openapi/client/util/DateUtil.class.php');
include_once ('com/alibaba/openapi/client/policy/ClientPolicy.class.php');
include_once ('com/alibaba/openapi/client/policy/ClientPolicy.class.php');
include_once ('com/alibaba/openapi/client/APIRequest.class.php');
include_once ('com/alibaba/openapi/client/APIId.class.php');
include_once ('com/alibaba/openapi/client/SyncAPIClient.class.php');


try {
    // ---------------------------example start---------------------------------

    // 请替换第一个参数apiKey和第二个参数apiSecurity
    $clientPolicy = new ClientPolicy (API_KEY, SEC_KEY, 'gateway.open.umeng.com');
    $syncAPIClient = new SyncAPIClient ( $clientPolicy );

    $reqPolicy = new RequestPolicy ();
    $reqPolicy->httpMethod = "POST";
    $reqPolicy->needAuthorization = false;
    $reqPolicy->requestSendTimestamp = false;
    // 测试环境只支持http
    // $reqPolicy->useHttps = false;
    $reqPolicy->useHttps = true;
    $reqPolicy->useSignture = true;
    $reqPolicy->accessPrivateApi = false;

    // --------------------------构造参数----------------------------------

    if($type==2)
    {
        $android = getResult(1);
        $ios = getResult(0);
        echo json_encode(array('result'=>array('ios'=>array('t'=>$ios->getTodayData() , 'y'=>$ios->getYesterdayData()) , 'android'=>array('t'=>$android->getTodayData() , 'y'=>$android->getYesterdayData()))));
    }else{
        $result = getResult($type);
        echo  json_encode(array('result'=>array('t'=>$result->getTodayData() , 'y'=>$result->getYesterdayData())));
    }
    die;
    // ----------------------------example end-------------------------------------
} catch ( OceanException $ex ) {
    echo "Exception occured with code[";
    echo $ex->getErrorCode ();
    echo "] message [";
    echo $ex->getMessage ();
    echo "].";
}



function getResult($type=0)
{
    global $reqPolicy;
    global $syncAPIClient;
    if($type==0)
    {
        $app_key = IOS_APP_KEY;
    }else
    {
        $app_key = ANDROID_APP_KEY;
    }

    $param = new UmengUappGetTodayYesterdayDataParam();
    $param->setAppkey($app_key);


    // --------------------------构造请求----------------------------------

    $request = new APIRequest ();
    $apiId = new APIId ("com.umeng.uapp", "umeng.uapp.getTodayYesterdayData", 1 );
    $request->apiId = $apiId;
    $request->requestEntity = $param;

    // --------------------------构造结果----------------------------------

    $result = new UmengUappGetTodayYesterdayDataResult();

    $syncAPIClient->send ( $request, $result, $reqPolicy );

    return $result;
}