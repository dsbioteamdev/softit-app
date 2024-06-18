<?php
include_once(dirname(__FILE__) .'/system/common.php');

define('_INDEX_', true);
if (!defined('_V6_')) exit; // 개별 페이지 접근 불가
?>

<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>TEST</title>
    <script defer src="../system/js/jquery-3.7.1.min.js"></script>
</head>

<body>
    <div class="w3-container w3-teal">
            <h1>header</h1>
    </div>
    <div class="w3-container">
            content
    </div>
    <div class="w3-container w3-teal">
            footer<br>
    </div>
    
    <!-- Navigation Bar -->
    <div class="w3-container">
            <div class="w3-bar w3-light-grey w3-border w3-large">
            <a href="#" class="w3-bar-item w3-button w3-green"><i class="fa fa-home"></i></a>
            <a href="#" class="w3-bar-item w3-button"><i class="fa fa-search"></i></a>
            <a href="#" class="w3-bar-item w3-button"><i class="fa fa-envelope"></i></a>
            <a href="#" class="w3-bar-item w3-button"><i class="fa fa-globe"></i></a>
            <a href="#" class="w3-bar-item w3-button" onclick="w3_open()"><i class="fa fa-sign-in"></i></a>
            </div>
    </div>
    <script>
        function w3_open() {
        document.getElementById("mySidebar").style.width = "100%";
        document.getElementById("mySidebar").style.display = "block";
        }

        function w3_close() {
        document.getElementById("mySidebar").style.display = "none";
        }
    </script>

    <!-- Sidebar -->
    <div class="w3-sidebar w3-bar-block" style="display:none" id="mySidebar">
        <button onclick="w3_close()" class="w3-bar-item w3-button w3-large">Close &times;</button>
        <a href="#" class="w3-bar-item w3-button">Link 1</a>
        <a href="#" class="w3-bar-item w3-button">Link 2</a>
        <a href="#" class="w3-bar-item w3-button">Link 3</a>
    </div>

    <!-- link -->
    <a href="#" class="w3-hover-text-yellow">Hover</a>



<?php
//회원테이블 테스트
$sql = "select * from `{$table['members']}`";
$result = db_query($sql, $DB_CONNECT);
print_r($result);
for ($i=0; $row=db_fetch_assoc($result); $i++) {
    echo $row['uid'].$row['id'];
}
?>
</body>
</html>