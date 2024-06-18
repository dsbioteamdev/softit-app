<?php

/********************
    상수 선언
********************/

// 이 상수가 정의되지 않으면 각각의 개별 페이지는 별도로 실행될 수 없음
define('_V6_', true);

// 기본 시간대 설정
date_default_timezone_set("Asia/Seoul");

/********************
    경로 상수
********************/

/*
보안서버 도메인
*/
define('G5_DOMAIN', '');

define('V6_DBCONFIG_FILE',  'db.php');
define('V6_DBMYSQL_FILE',  'mysql.php');
define('V6_DBTABLE_FILE',  'table.php');

define('V6_ALLI_DIR',      'alliance');
define('V6_CONDOL_DIR',        'condolence');
define('V6_FUNER_DIR',        'funeral');
define('V6_GIIL_DIR',       'giil_allim');
define('V6_JOIN_DIR',     'join');
define('V6_OBITU_DIR',        'obituary');
define('V6_ORDER_DIR',         'order');
define('V6_SYSTEM_DIR',     'system');

// URL 은 브라우저상에서의 경로 (도메인으로 부터)
if (G5_DOMAIN) {
    define('V6_URL', G5_DOMAIN);
} else {
    if (isset($v6_path['url']))
        define('V6_URL', $v6_path['url']);
    else
        define('V6_URL', '');
}

if (isset($v6_path['path'])) {
    define('V6_PATH', $v6_path['path']);
} else {
    define('V6_PATH', '');
}

define('V6_ALLI_URL',      V6_URL.'/'.V6_ALLI_DIR);
define('V6_CONDOL_URL',        V6_URL.'/'.V6_CONDOL_DIR);
define('V6_FUNER_URL',        V6_URL.'/'.V6_FUNER_DIR);
define('V6_GIIL_URL',       V6_URL.'/'.V6_GIIL_DIR);
define('V6_JOIN_URL',        V6_URL.'/'.V6_JOIN_DIR);
define('V6_OBITU_URL',         V6_URL.'/'.V6_OBITU_DIR);
define('V6_ORDER_URL',       V6_URL.'/'.V6_ORDER_DIR);
define('V6_SYSTEM_URL',     V6_URL.'/'.V6_SYSTEM_DIR);

// PATH 는 서버상에서의 절대경로
define('V6_ALLI_PATH',     V6_PATH.'/'.V6_ALLI_DIR);
define('V6_CONDOL_PATH',       V6_PATH.'/'.V6_CONDOL_DIR);
define('V6_GIIL_PATH',      V6_PATH.'/'.V6_FUNER_DIR);
define('V6_JOIN_PATH',    V6_PATH.'/'.V6_GIIL_DIR);
define('V6_JOIN_PATH',       V6_PATH.'/'.V6_JOIN_DIR);
define('V6_OBITU_PATH',    V6_PATH.'/'.V6_OBITU_DIR);
define('V6_ORDER_PATH',      V6_PATH.'/'.V6_ORDER_DIR);
define('V6_SYSTEM_PATH',    V6_PATH.'/'.V6_SYSTEM_DIR);
//==============================================================================


/********************
    시간 상수
********************/
// 서버의 시간과 실제 사용하는 시간이 틀린 경우 수정하세요.
// 하루는 86400 초입니다. 1시간은 3600초
// 6시간이 빠른 경우 time() + (3600 * 6);
// 6시간이 느린 경우 time() - (3600 * 6);
define('V6_SERVER_TIME',    time());
define('V6_TIME_YMDHIS',    date('Y-m-d H:i:s', V6_SERVER_TIME));
define('V6_TIME_YMD',       substr(V6_TIME_YMDHIS, 0, 10));
define('V6_TIME_HIS',       substr(V6_TIME_YMDHIS, 11, 8));

// 퍼미션
define('V6_DIR_PERMISSION',  0755); // 디렉토리 생성시 퍼미션
define('V6_FILE_PERMISSION', 0644); // 파일 생성시 퍼미션

// 모바일 인지 결정 $_SERVER['HTTP_USER_AGENT']
define('V6_MOBILE_AGENT',   'phone|samsung|lgtel|mobile|[^A]skt|nokia|blackberry|BB10|android|sony');

// SMTP
define('V6_SMTP',      '127.0.0.1');
define('V6_SMTP_PORT', '25');


/********************
    기타 상수
********************/

// 암호화 함수 지정
// 사이트 운영 중 설정을 변경하면 로그인이 안되는 등의 문제가 발생합니다.
//define('V6_STRING_ENCRYPT_FUNCTION', 'sql_password');
define('V6_STRING_ENCRYPT_FUNCTION', 'create_hash');

// SQL 에러를 표시할 것인지 지정
// 에러를 표시하려면 true 로 변경
define('V6_DISPLAY_SQL_ERROR', false);

// escape string 처리 함수 지정
// addslashes 로 변경 가능
define('V6_ESCAPE_FUNCTION', 'sql_escape_string');

// sql_escape_string 함수에서 사용될 패턴
//define('V6_ESCAPE_PATTERN',  '/(and|or).*(union|select|insert|update|delete|from|where|limit|create|drop).*/i');
//define('V6_ESCAPE_REPLACE',  '');