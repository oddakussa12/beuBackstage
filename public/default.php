<?php
include_once ('config.php');
include_once ('com/umeng/uapp/param/UmengUappGetDurationsParam.class.php');
include_once ('com/umeng/uapp/param/UmengUappGetDurationsResult.class.php');

// ---------------------------引入SDK工具类(以用户实际路径为准)---------------------------------
include_once ('com/alibaba/openapi/client/policy/RequestPolicy.class.php');
include_once ('com/alibaba/openapi/client/entity/ByteArray.class.php');
include_once ('com/alibaba/openapi/client/util/DateUtil.class.php');
include_once ('com/alibaba/openapi/client/policy/ClientPolicy.class.php');
include_once ('com/alibaba/openapi/client/policy/ClientPolicy.class.php');
include_once ('com/alibaba/openapi/client/APIRequest.class.php');
include_once ('com/alibaba/openapi/client/APIId.class.php');
include_once ('com/alibaba/openapi/client/SyncAPIClient.class.php');

$type = intval(isset($_GET['type'])?$_GET['type']:2);


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
    echo 1;die;
    if($type==2)
    {
       $android = getResult(1);
       $ios = getResult(0);
       $result = array('android'=>$android , 'ios'=>$ios);
    }else{
        $result = getResult($type);
    }
    var_dump($result);
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

    $startDate =  isset($_GET['date'])?$_GET['date']:date('Y-m-d' , time());
    $startDate = date('Y-m-d' , strtotime($startDate));

    $param = new UmengUappGetDurationsParam();
    $param->setAppkey($app_key);
    $param->setDate($startDate);
    $param->setStatType("daily");
//    $param->setChannel("App%20Store");
//    $param->setVersion("1.0.0");


    // --------------------------构造请求----------------------------------

    $request = new APIRequest ();
    $apiId = new APIId ("com.umeng.uapp", "umeng.uapp.getDurations", 1 );
    $request->apiId = $apiId;
    $request->requestEntity = $param;

    // --------------------------构造结果----------------------------------

    $result = new UmengUappGetDurationsResult();

    $syncAPIClient->send ( $request, $result, $reqPolicy );

    return $result;
}