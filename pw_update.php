<?php
session_start();


// エラーを表示する設定
ini_set('display_errors', 1);
error_reporting(E_ALL);

//1. POSTデータ取得
$lpw = $_POST["lpw"];


//2. DB接続します
include("funcs.php");
$pdo = db_conn();

sschk();

// 3. パスワードをハッシュ化（初期化）
$hashed_password = password_hash($lpw, PASSWORD_DEFAULT);

//4．データ登録SQL作成
$sql = "UPDATE allmembers SET ";

// パスワードが入力された場合、SQL文にパスワード更新を追加
$updateFields = [];
if (!empty($lpw)) {
    $updateFields[] = "lpw = :lpw";
}

// 更新するフィールドがあるかチェック
if (count($updateFields) > 0) {
    $sql .= implode(", ", $updateFields);
    $sql .= " WHERE shainno = :shainno";  // WHERE句を追加
} else {
    // 更新フィールドがなければ終了（例: パスワードが空の場合）
    redirect("select.php");
}

//SQLを準備
$stmt = $pdo->prepare($sql);

// パスワードが入力されている場合、ハッシュ化してバインド
if (!empty($lpw)) {
    $hashed_password = password_hash($lpw, PASSWORD_DEFAULT); // パスワードをハッシュ化
    $stmt->bindValue(':lpw', $hashed_password, PDO::PARAM_STR);
}

// セッションから社員番号を取得してバインド
$stmt->bindValue(':shainno', $_SESSION["shainno"], PDO::PARAM_INT);

// 実行
$status = $stmt->execute();

//4. データ登録処理後
if($status==false){
    sql_error($stmt);  // SQLエラーが発生した場合の処理
} else {
    redirect("select.php");  // 更新が成功した場合、リダイレクト
}

?>