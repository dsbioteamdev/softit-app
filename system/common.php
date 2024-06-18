<?php
/*******************************************************************************
** 공통 변수, 상수, 코드
*******************************************************************************/
error_reporting( E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING );

// 보안설정이나 프레임이 달라도 쿠키가 통하도록 설정
header('P3P: CP="ALL CURa ADMa DEVa TAIa OUR BUS IND PHY ONL UNI PUR FIN COM NAV INT DEM CNT STA POL HEA PRE LOC OTC"');

if (!defined('V6_SET_TIME_LIMIT')) define('V6_SET_TIME_LIMIT', 0);
@set_time_limit(V6_SET_TIME_LIMIT);

if( version_compare( PHP_VERSION, '5.2.17' , '<' ) ){
    die(sprintf('PHP 5.2.17 or higher required. Your PHP version is %s', PHP_VERSION));
}

//==========================================================================================================================
// extract($_GET); 명령으로 인해 page.php?_POST[var1]=data1&_POST[var2]=data2 와 같은 코드가 _POST 변수로 사용되는 것을 막음
//--------------------------------------------------------------------------------------------------------------------------
$ext_arr = array ('PHP_SELF', '_ENV', '_GET', '_POST', '_FILES', '_SERVER', '_COOKIE', '_SESSION', '_REQUEST',
                  'HTTP_ENV_VARS', 'HTTP_GET_VARS', 'HTTP_POST_VARS', 'HTTP_POST_FILES', 'HTTP_SERVER_VARS',
                  'HTTP_COOKIE_VARS', 'HTTP_SESSION_VARS', 'GLOBALS');
$ext_cnt = count($ext_arr);
for ($i=0; $i<$ext_cnt; $i++) {
    // POST, GET 으로 선언된 전역변수가 있다면 unset() 시킴
    if (isset($_GET[$ext_arr[$i]]))  unset($_GET[$ext_arr[$i]]);
    if (isset($_POST[$ext_arr[$i]])) unset($_POST[$ext_arr[$i]]);
}
//==========================================================================================================================


function v6_path()
{
    $chroot = substr($_SERVER['SCRIPT_FILENAME'], 0, strpos($_SERVER['SCRIPT_FILENAME'], dirname(__FILE__))); 
    $result['path'] = str_replace('\system', '', $chroot.dirname(__FILE__)); 
    $result['path'] = str_replace('\\', '/', $result['path']); 
    $server_script_name = preg_replace('/\/+/', '/', str_replace('\\', '/', $_SERVER['SCRIPT_NAME'])); 
    $server_script_filename = preg_replace('/\/+/', '/', str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME'])); 
    $tilde_remove = preg_replace('/^\/\~[^\/]+(.*)$/', '$1', $server_script_name); 
    $document_root = str_replace($tilde_remove, '', $server_script_filename); 
    $pattern = '/.*?' . preg_quote($document_root, '/') . '/i';
    $root = preg_replace($pattern, '', $result['path']); 
    $port = ($_SERVER['SERVER_PORT'] == 80 || $_SERVER['SERVER_PORT'] == 443) ? '' : ':'.$_SERVER['SERVER_PORT']; 
    $http = 'http' . ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on') ? 's' : '') . '://'; 
    $user = str_replace(preg_replace($pattern, '', $server_script_filename), '', $server_script_name); 
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME']; 
    if(isset($_SERVER['HTTP_HOST']) && preg_match('/:[0-9]+$/', $host)) 
        $host = preg_replace('/:[0-9]+$/', '', $host); 
    $host = preg_replace("/[\<\>\'\"\\\'\\\"\%\=\(\)\/\^\*]/", '', $host); 
    $result['url'] = $http.$host.$port.$user.$root; 
    return $result;
}

$v6_path = v6_path();

include_once($v6_path['path'].'/system/config.php');   // 설정 파일

unset($v6_path);

// IIS 에서 SERVER_ADDR 서버변수가 없다면
if(! isset($_SERVER['SERVER_ADDR'])) {
    $_SERVER['SERVER_ADDR'] = isset($_SERVER['LOCAL_ADDR']) ? $_SERVER['LOCAL_ADDR'] : '';
}

// Cloudflare 환경을 고려한 https 사용여부
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === "https") {
    $_SERVER['HTTPS'] = 'on';
}

// multi-dimensional array에 사용자지정 함수적용
function array_map_deep($fn, $array)
{
    if(is_array($array)) {
        foreach($array as $key => $value) {
            if(is_array($value)) {
                $array[$key] = array_map_deep($fn, $value);
            } else {
                $array[$key] = call_user_func($fn, $value);
            }
        }
    } else {
        $array = call_user_func($fn, $array);
    }

    return $array;
}


// SQL Injection 대응 문자열 필터링
function sql_escape_string($str)
{
    if(defined('V6_ESCAPE_PATTERN') && defined('V6_ESCAPE_REPLACE')) {
        $pattern = V6_ESCAPE_PATTERN;
        $replace = V6_ESCAPE_REPLACE;

        if($pattern)
            $str = preg_replace($pattern, $replace, $str);
    }

    $str = call_user_func('addslashes', $str);

    return $str;
}


//==============================================================================
// SQL Injection 등으로 부터 보호를 위해 sql_escape_string() 적용
//------------------------------------------------------------------------------
// magic_quotes_gpc 에 의한 backslashes 제거
if (7.0 > (float)phpversion()) {
    if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
        $_POST    = array_map_deep('stripslashes',  $_POST);
        $_GET     = array_map_deep('stripslashes',  $_GET);
        $_COOKIE  = array_map_deep('stripslashes',  $_COOKIE);
        $_REQUEST = array_map_deep('stripslashes',  $_REQUEST);
    }
}

// sql_escape_string 적용
$_POST    = array_map_deep(V6_ESCAPE_FUNCTION,  $_POST);
$_GET     = array_map_deep(V6_ESCAPE_FUNCTION,  $_GET);
$_COOKIE  = array_map_deep(V6_ESCAPE_FUNCTION,  $_COOKIE);
$_REQUEST = array_map_deep(V6_ESCAPE_FUNCTION,  $_REQUEST);
//==============================================================================


// PHP 4.1.0 부터 지원됨
// php.ini 의 register_globals=off 일 경우
@extract($_GET);
@extract($_POST);
@extract($_SERVER);


// $member 에 값을 직접 넘길 수 있음
$member = array();
$v6     = array();

$DB = array();
$SDB = array();
$table = array();

if( version_compare( phpversion(), '8.0.0', '>=' ) ) { $v6 = array('title'=>''); }

//==============================================================================
// 공통
//------------------------------------------------------------------------------
$dbconfig_file = V6_SYSTEM_PATH.'/'.V6_DBCONFIG_FILE;
if (file_exists($dbconfig_file)) {
    include_once($dbconfig_file);
    $dbmysql_file = V6_SYSTEM_PATH.'/'.V6_DBMYSQL_FILE;
    $dbtable_file = V6_SYSTEM_PATH.'/'.V6_DBTABLE_FILE;
    if (file_exists($dbmysql_file)) { include_once($dbmysql_file); }
    if (file_exists($dbtable_file)) { include_once($dbtable_file); }

    $DB_CONNECT = isConnectDb($DB) or die('MySQL Connect Error!!!');
    $SDB_CONNECT = isConnectDb($SDB) or die('MySQL Connect Error!!!');
    //echo 'sucess';
} else {
    //echo 'fail';
    exit;
}
//==============================================================================


//==============================================================================
// SESSION 설정
//------------------------------------------------------------------------------
@ini_set("session.use_trans_sid", 0);    // PHPSESSID를 자동으로 넘기지 않음
@ini_set("url_rewriter.tags",""); // 링크에 PHPSESSID가 따라다니는것을 무력화함

// 세션파일 저장 디렉토리를 지정할 경우
// session_save_path(V6_SESSION_PATH);

if (isset($SESSION_CACHE_LIMITER))
    @session_cache_limiter($SESSION_CACHE_LIMITER);
else
    @session_cache_limiter("no-cache, must-revalidate");

ini_set("session.cache_expire", 180); // 세션 캐쉬 보관시간 (분)
ini_set("session.gc_maxlifetime", 10800); // session data의 garbage collection 존재 기간을 지정 (초)
ini_set("session.gc_probability", 1); // session.gc_probability는 session.gc_divisor와 연계하여 gc(쓰레기 수거) 루틴의 시작 확률을 관리합니다. 기본값은 1입니다. 자세한 내용은 session.gc_divisor를 참고하십시오.
ini_set("session.gc_divisor", 100); // session.gc_divisor는 session.gc_probability와 결합하여 각 세션 초기화 시에 gc(쓰레기 수거) 프로세스를 시작할 확률을 정의합니다. 확률은 gc_probability/gc_divisor를 사용하여 계산합니다. 즉, 1/100은 각 요청시에 GC 프로세스를 시작할 확률이 1%입니다. session.gc_divisor의 기본값은 100입니다.

if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
    session_set_cookie_params(0, '/', null, true, true);
} else {
    session_set_cookie_params(0, '/', null, false, true);
}

function chrome_domain_session_name(){
    // 크롬90버전대부터 아래 도메인을 포함된 주소로 접속시 특정조건에서 세션이 생성 안되는 문제가 있을수 있다.
    $domain_array=array(
    '.cafe24.com',  // 카페24호스팅
    '.dothome.co.kr',     // 닷홈호스팅
    '.phps.kr',     // 스쿨호스팅
    '.maru.net',    // 마루호스팅
    );

    $add_str = '';
    $document_root_path = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT']));

    if( V6_PATH !== $document_root_path ){
        $add_str = substr_count(V6_PATH, '/').basename(dirname(__FILE__));
    }

    if($add_str || (isset($_SERVER['HTTP_HOST']) && preg_match('/('.implode('|', $domain_array).')/i', $_SERVER['HTTP_HOST'])) ){  // 위의 도메인주소를 포함한 url접속시 기본세션이름을 변경한다.
        if(! defined('V6_SESSION_NAME')) define('V6_SESSION_NAME', 'V6'.$add_str.'PHPSESSID');
        @session_name(V6_SESSION_NAME);
    }
}

chrome_domain_session_name();

//==============================================================================
// 공용 변수
//------------------------------------------------------------------------------
// 기본환경설정
// 기본적으로 사용하는 필드만 얻은 후 상황에 따라 필드를 추가로 얻음
// 본인인증 또는 쇼핑몰 사용시에만 secure; SameSite=None 로 설정합니다.
// Chrome 80 버전부터 아래 이슈 대응
// https://developers-kr.googleblog.com/2020/01/developers-get-ready-for-new.html?fbclid=IwAR0wnJFGd6Fg9_WIbQPK3_FxSSpFLqDCr9bjicXdzy--CCLJhJgC9pJe5ss
if(!function_exists('session_start_samesite')) {
    function session_start_samesite($options = array())
    {
        global $v6;

        $res = @session_start($options);

        // IE 브라우저 또는 엣지브라우저 또는 IOS 모바일과 http환경에서는 secure; SameSite=None을 설정하지 않습니다.
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            if (preg_match('/Edge/i', $_SERVER['HTTP_USER_AGENT'])
                || preg_match('/(iPhone|iPod|iPad).*AppleWebKit.*Safari/i', $_SERVER['HTTP_USER_AGENT'])
                || preg_match('~MSIE|Internet Explorer~i', $_SERVER['HTTP_USER_AGENT'])
                || preg_match('~Trident/7.0(; Touch)?; rv:11.0~',$_SERVER['HTTP_USER_AGENT'])
                || !(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on')) {
                return $res;
            }
        }

        $headers = headers_list();
        krsort($headers);
        $cookie_session_name = 'PHPSESSID'; 
        foreach ($headers as $header) {
            if (!preg_match('~^Set-Cookie: '.$cookie_session_name.'=~', $header)) continue;
            $header = preg_replace('~(; secure; HttpOnly)?$~', '; secure; HttpOnly; SameSite=None', $header);
            header($header, false);
            $v6['session_cookie_samesite'] = 'none';
            break;
        }
        return $res;
    }
}

session_start_samesite();
@session_start();
//==============================================================================

ob_start();

// 자바스크립트에서 go(-1) 함수를 쓰면 폼값이 사라질때 해당 폼의 상단에 사용하면 캐쉬의 내용을 가져옴.
header('Content-Type: text/html; charset=utf-8');
$gmnow = gmdate('D, d M Y H:i:s') . ' GMT';
header('Expires: 0'); // rfc2616 - Section 14.21
header('Last-Modified: ' . $gmnow);
header('Cache-Control: no-store, no-cache, must-revalidate'); // HTTP/1.1
header('Cache-Control: pre-check=0, post-check=0, max-age=0'); // HTTP/1.1
header('Pragma: no-cache'); // HTTP/1.0
