<?php
// エラーを表示するための設定
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


//最初にSESSIONを開始！！ココ大事！！
session_start();

//POST値
$shainno = $_POST["lid"]; //lid
$lpw = $_POST["lpw"]; //lpw

//1.  DB接続します
include("funcs.php");
$pdo = db_conn();

// 2. allmembers で認証
$stmt = $pdo->prepare("SELECT * FROM allmembers WHERE shainno = :shainno AND life_flg = 0");
$stmt->bindValue(':shainno', $shainno, PDO::PARAM_INT);
$status = $stmt->execute();

// 3. エラーチェック
// 3. エラーチェック
if ($status == false) {
  sql_error($stmt); // SQLエラーがあれば表示
} else {
  $val = $stmt->fetch(); // データを取得
  if ($val === false) {
      // 該当の社員番号が見つからない場合、life_flgが1の社員がいるか確認
      $stmt = $pdo->prepare("SELECT * FROM allmembers WHERE shainno = :shainno AND life_flg = 1");
      $stmt->bindValue(':shainno', $shainno, PDO::PARAM_INT);
      $stmt->execute();
      $resignedVal = $stmt->fetch();

      if ($resignedVal) {
          // 退職済の場合
          $_SESSION["login_error"] = "退職済の社員番号です。";
      } else {
          // 該当の社員番号が見つからない場合
          $_SESSION["login_error"] = "該当の社員番号が見つかりません。";
        }
        redirect("login.php");
      exit();
  }
}

// 4. life_flgが1の場合は退職済エラーを表示
// 4. life_flgが1の場合は退職済エラーを表示
if ($val && isset($val["life_flg"]) && $val["life_flg"] == 1) {
  $_SESSION["login_error"] = "退職済の社員番号です。";
  redirect("login.php");
  exit();
}

// 5. 該当レコードがあれば、パスワードの確認
if ($val) {
// ハッシュ化したパスワードを取得
$hashed_password = password_hash($lpw, PASSWORD_DEFAULT);

  if (password_verify($lpw, $val["lpw"])) {
    // 2. ログイン履歴を user_table に追加
    $stmt = $pdo->prepare("INSERT INTO user_table (shainno, lpw, busho, kanri_flg, name, life_flg, indate) VALUES (:shainno, :lpw, :busho, :kanri_flg, :name,:life_flg ,sysdate())");
    $stmt->bindValue(':name', $val['name'], PDO::PARAM_STR); // 名前
    $stmt->bindValue(':shainno', $shainno, PDO::PARAM_INT);
    $stmt->bindValue(':lpw', $hashed_password, PDO::PARAM_STR);
    $stmt->bindValue(':busho', $val['busho'], PDO::PARAM_STR); // 部署
    $stmt->bindValue(':kanri_flg', $val['kanri_flg'], PDO::PARAM_INT); // 管理フラグ
    $stmt->bindValue(':life_flg', $val['life_flg'], PDO::PARAM_INT); // 管理フラグ

    $stmt->execute(); // 履歴の挿入を実行

      // セッション情報を設定
      $_SESSION["chk_ssid"] = session_id();
      $_SESSION["kanri_flg"] = $val['kanri_flg']; // 変更しない場合
      $_SESSION["shainno"] = $shainno; // 社員番号
      $_SESSION["name"] = $val['name']; // 名前
      $_SESSION["busho"] = $val['busho']; // 部署

      // リダイレクト
      redirect("select.php");
  } else {
      $_SESSION["login_error"] = "社員番号またはパスワードが間違っています。";
  }
} else {
  $_SESSION["login_error"] = "社員番号またはパスワードが間違っています。";
}

// ログイン失敗時はlogin.phpにリダイレクト
redirect("login.php");
exit();