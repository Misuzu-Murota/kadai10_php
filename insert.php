<!-- 社員番号登録 -->
<?php
session_start();

//1. POSTデータ取得
//[shainno,name,gender,age,busho,position,year,kanri_flg,lpw]
$shainno = $_POST["shainno"];
$name = $_POST["name"];
$gender = $_POST["gender"];
$age = $_POST["age"];
$busho = $_POST["busho"];
$position = $_POST["position"];
$year = $_POST["year"];
$kanri_flg = isset($_POST["kanri_flg"]) ? 1 : 0; // 管理者フラグの取得
$lpw = $_POST["lpw"];

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// 関数用のファイルを使用できるように呼び出す
require_once('funcs.php');
sschk();
echo "funcs.php loaded!<br>";

$pdo = db_conn();
if (!$pdo) {
  exit("Database connection failed.");
}

// 2. 社員番号の重複チェック
$sql = "SELECT COUNT(*) FROM allmembers WHERE shainno = :shainno;";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':shainno', $shainno, PDO::PARAM_INT);
$stmt->execute();
$count = $stmt->fetchColumn();

// 既に登録されている場合
if ($count > 0) {
    $_SESSION["error_message"] = "既に同一の社員番号で登録があります。";
    header("Location: index.php");
    exit();
}

// 3. パスワードをハッシュ化
$hashed_password = password_hash($lpw, PASSWORD_DEFAULT);

//３．データ登録SQL作成
$sql = "INSERT INTO allmembers(shainno,name,gender,age,busho,position,year,kanri_flg,lpw,indate)VALUES(:shainno,:name,:gender,:age,:busho,:position,:year, :kanri_flg, :lpw, sysdate());";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':shainno', $shainno,  PDO::PARAM_INT);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':name',   $name,    PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':gender',  $gender,   PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':age',    $age,     PDO::PARAM_INT);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':busho',  $busho,   PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':position', $position,  PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':year', $year,  PDO::PARAM_INT);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':kanri_flg', $kanri_flg, PDO::PARAM_INT); // 管理者フラグのバインド
$stmt->bindValue(':lpw', $hashed_password, PDO::PARAM_STR);  // ハッシュ化したパスワードを保存


$status = $stmt->execute(); //True or False

//４．データ登録処理後
if($status==false){
  //SQL実行時にエラーがある場合（エラーオブジェクト取得して表示）
  $error = $stmt->errorInfo();
  exit("SQL_ERROR:".$error[2]);
}else{
  $_SESSION["success_message"] = "登録されました";
  header("Location: index.php");
  exit();
}
?>
