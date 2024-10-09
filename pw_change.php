<!-- 社員情報一覧 -->
<?php
//0. SESSION開始！！
session_start();

//1.  DB接続します
// 関数用のファイルを使用できるように呼び出す

require_once('funcs.php');
$pdo = db_conn();

//LOGINチェック → funcs.phpへ関数化しましょう！
sschk();

// セッションに保存されている社員番号を取得
if (!isset($_SESSION["shainno"])) {
    // セッションに社員番号がなければ、エラーまたはログイン画面にリダイレクトなどの処理
    exit('セッションが無効です。ログインしてください。');
}
$shainno = $_SESSION["shainno"];  // セッションから社員番号を取得


//２．データ登録SQL作成
$sql = "SELECT * FROM allmembers WHERE shainno = :shainno";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':shainno', $shainno, PDO::PARAM_INT);  // セッションの社員番号をバインド
$status = $stmt->execute();


//３．データ表示
$values = "";
if($status==false) {
  sql_error($stmt);
}

//対象データ取得
$v =  $stmt->fetch();  //PDO::FETCH_ASSOC[カラム名のみで取得できるモード]
// $json = json_encode($values,JSON_UNESCAPED_UNICODE);

?>

<!-- 社員情報登録 -->
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>社員情報更新</title>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/sample.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 20px;
    }

    .container {
      width: 80%;
      margin: 0 auto;
      padding: 20px;
      border: 1px solid #ccc;
      background-color: #f9f9f9;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }

    table th, table td {
      border: 1px solid #ddd;
      padding: 10px;
      text-align: left;
    }

    table th {
      background-color: #f2f2f2;
    }

    input[type="text"], input[type="submit"] {
      width: 100%;
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 4px;
      box-sizing: border-box;
    }

    input[type="submit"] {
      background-color: #4CAF50;
      color: white;
      border: none;
      cursor: pointer;
    }

    input[type="submit"]:hover {
      background-color: #45a049;
    }

    .form-control {
      margin-bottom: 20px;
    }

    legend {
      font-size: 1.2em;
      margin-bottom: 20px;
    }

  </style>
</head>
<body>

<!-- Head[Start] -->
<header>
  <nav class="navbar navbar-default">
    <div class="container-fluid">
      <div class="navbar-header"><a class="navbar-brand" href="select.php">社員情報一覧</a></div>
    </div>
  </nav>
</header>
<!-- Head[End] -->

<!-- Main[Start] -->
<form id="employeeForm" method="post" action="pw_update.php">
  <div class="jumbotron">
    <fieldset>

      <legend>ログインパスワード更新</legend>
    <!-- 社員情報を表形式で表示 -->
        <table border="1" cellpadding="10">
                <tr>
                    <th>社員番号</th>
                    <td><?= htmlspecialchars($v["shainno"]) ?></td>
                </tr>
                <tr>
                    <th>名前</th>
                    <td><?= htmlspecialchars($v["name"]) ?></td>
                </tr>
                <tr>
                    <th>性別</th>
                    <td><?= htmlspecialchars($v["gender"]) ?></td>
                </tr>
                <tr>
                    <th>年齢</th>
                    <td><?= htmlspecialchars($v["age"]) ?></td>
                </tr>
                <tr>
                    <th>部署</th>
                    <td><?= htmlspecialchars($v["busho"]) ?></td>
                </tr>
                <tr>
                    <th>役職</th>
                    <td><?= htmlspecialchars($v["position"]) ?></td>
                </tr>
                <tr>
                    <th>入社年</th>
                    <td><?= htmlspecialchars($v["year"]) ?></td>
                </tr>
            </table>
            <br>

            <!-- ログインパスワードのみ更新可能なフィールド -->
            <label>ログインpw：<input type="text" name="lpw" id="lpw" class="form-control" placeholder="新しいPWを入力してください"></label><br>
            <div style="display: flex; gap: 10px;">
                <input type="submit" value="更新" class="form-control">
                <button class="form-control" id="return">戻る</button>
            </div>
        </fieldset>
    </div>
</form>
<!-- Main[End] -->

<script>
  $(document).ready(function() {
    // 戻るボタンのクリックイベント
    $('#return').click(function(event) {
      event.preventDefault(); // フォーム送信を防ぐ
      window.location.href = 'select.php'; // 社員情報一覧にリダイレクト
    });

    // jQueryでフォームのバリデーションを実装
    $('#employeeForm').submit(function(event) {
      // エラーメッセージを格納する配列
      let errors = [];
      let lpw = $('#lpw').val();

      // バリデーション: 必須項目チェック
      if (lpw.length > 0 && lpw.length < 6) { // パスワードが入力されている場合のみ6文字以上をチェック
        errors.push('ログインパスワードは6文字以上の英数字で設定してください。');
      }

      // エラーがあればアラートで表示
      if (errors.length > 0) {
        event.preventDefault(); // フォームの送信を防ぐ
        alert(errors.join('\n'));
      } else if (lpw.length === 0) {
        // 「更新する内容がないため、社員情報一覧に戻ります。」というアラートは出さない
        window.location.href = 'select.php'; // 社員情報一覧にリダイレクト
        event.preventDefault(); // フォームの送信を防ぐ
      } else {
        alert('パスワードを更新しました。');
      }
    });
  });
</script>

</body>
</html>
