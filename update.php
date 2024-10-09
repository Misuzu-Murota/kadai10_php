<?php
session_start();


// エラーを表示する設定
ini_set('display_errors', 1);
error_reporting(E_ALL);

//1. POSTデータ取得
$life_flg = isset($_POST["life_flg"]) ? 1 : 0; // 管理者フラグの取得
$shainno = $_POST["shainno"];
$name = $_POST["name"];
$gender = $_POST["gender"];
$age = $_POST["age"];
$busho = $_POST["busho"];
$position = $_POST["position"];
$year = $_POST["year"];
$kanri_flg = isset($_POST["kanri_flg"]) ? 1 : 0; // 管理者フラグの取得

//2. DB接続します
include("funcs.php");
$pdo = db_conn();

sschk();


//4．データ登録SQL作成
$sql = "UPDATE allmembers SET life_flg=:life_flg, name=:name, gender=:gender,age=:age,busho=:busho ,position=:position ,year=:year, kanri_flg=:kanri_flg, indate = CURRENT_TIMESTAMP";

$sql .= " WHERE shainno=:shainno";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':life_flg', $life_flg, PDO::PARAM_INT); // 管理者フラグのバインド
$stmt->bindValue(':shainno', $shainno,  PDO::PARAM_INT);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':name',   $name,    PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':gender',  $gender,   PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':age',    $age,     PDO::PARAM_INT);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':busho',  $busho,   PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':position', $position,  PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':year', $year,  PDO::PARAM_INT);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':kanri_flg', $kanri_flg, PDO::PARAM_INT); // 管理者フラグのバインド



$status = $stmt->execute(); // True or False


//４．データ登録処理後
if($status==false){
    sql_error($stmt);
}else{
    redirect("select.php");
}


?>