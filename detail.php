
<?php
session_start();
//１．PHP
//select.phpのPHPコードをマルっとコピーしてきます。
//※SQLとデータ取得の箇所を修正します。


$shainno = $_GET["shainno"];

include("funcs.php");
$pdo = db_conn();

sschk();

//２．データ登録SQL作成
$sql = "SELECT * FROM allmembers WHERE shainno =:shainno";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':shainno',   $shainno,   PDO::PARAM_INT);  //Integer（数値の場合 PDO::PARAM_INT)

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
<form id="employeeForm" method="post" action="update.php">
  <div class="jumbotron">
    <fieldset>

      <legend>社員情報更新</legend>
      <div id="leaveFlagContainer">
         <input type="checkbox" name="life_flg" id="life_flg" <?= $v["life_flg"] == 1 ? "checked" : "" ?>>
         <label for="life_flg">退職者フラグ</label>
      </div><br>

      <label>社員番号：<input type="number" name="shainno" id="shainno" class="form-control" minlength="6" maxlength="6" value="<?=$v["shainno"]?>"></label><br>
      <label>名前：<input type="text" name="name" id="name" class="form-control" value="<?=$v["name"]?>"></label><br>
      <label>性別：
      <select name="gender" id="gender" class="form-control">
         <option value="">--選択してください--</option>
         <option value="男性" <?= $v["gender"] == "男性" ? "selected" : "" ?>>男性</option>
         <option value="女性" <?= $v["gender"] == "女性" ? "selected" : "" ?>>女性</option>
         <option value="その他" <?= $v["gender"] == "その他" ? "selected" : "" ?>>その他</option>
     </select>
      </label><br>
      <label>年齢：<input type="number" name="age" id="age" class="form-control" min="18" max="100" value="<?=$v["age"]?>"></label><br>
      <label>部署：
      <select name="busho" id="busho" class="form-control">
        <option value="">--選択してください--</option>
        <option value="営業部" <?= $v["busho"] == "営業部" ? "selected" : "" ?>>営業部</option>
        <option value="人事部" <?= $v["busho"] == "人事部" ? "selected" : "" ?>>人事部</option>
        <option value="IT部" <?= $v["busho"] == "IT部" ? "selected" : "" ?>>IT部</option>
    </select>
    </label>
      <div id="adminFlagContainer">
      <input type="checkbox" name="kanri_flg" id="kanri_flg" <?= $v["kanri_flg"] == 1 ? "checked" : "" ?>>
      <label for="kanri_flg">管理者フラグ</label>
      </div>
      <br>
      <label>役職：
        <select name="position" id="position" class="form-control" >
        <option value="">--選択してください--</option>
          <option value="マネージャー"  <?= $v["position"] == "マネージャー" ? "selected" : "" ?>>マネージャー</option>
          <option value="一般職"  <?= $v["position"] == "一般職" ? "selected" : "" ?>>一般職</option>
          <option value="役員"  <?= $v["position"] == "役員" ? "selected" : "" ?>>役員</option>
        </select>
      </label><br>
      <label>入社年：<input type="number" name="year" id="year" class="form-control" minlength="4" maxlength="4" value="<?=$v["year"]?>"></label><br>
      <div style="display: flex; gap: 10px;">
        <input type="submit" value="更新" class="form-control">
        <button class="form-control" id="return">戻る</button>
      </div> 
    </fieldset>
  </div>
</form>
<!-- Main[End] -->

<script>
  // jQueryでフォームのバリデーションを実装
  $(document).ready(function() {
    // 戻るボタンのクリックイベント
    $('#return').click(function(event) {
      event.preventDefault(); // フォーム送信を防ぐ
      window.location.href = 'select.php'; // 社員情報一覧にリダイレクト
    });

    // 部署が変更されたときの処理
      $('#busho').change(function() {
        if ($(this).val() === '人事部') {
          $('#adminFlagContainer').show(); // 人事部が選択された場合、管理者フラグを表示
        } else {
          $('#adminFlagContainer').hide(); // 他の部署が選択された場合、管理者フラグを非表示
          $('#kanri_flg').prop('checked', false); // 管理者フラグのチェックを外す
        }
    });

    $('#employeeForm').submit(function(event) {
      // エラーメッセージを格納する配列
      let errors = [];

      // 各フィールドの値を取得
      let shainno = $('#shainno').val();
      let name = $('#name').val();
      let gender = $('#gender').val();
      let age = $('#age').val();
      let busho = $('#busho').val();
      let position = $('#position').val();
      let year = $('#year').val();


      // バリデーション: 必須項目チェック
      if (!shainno || shainno.length !== 6) {
        errors.push('社員番号は6桁で入力してください。');
      }
      if (!name) {
        errors.push('名前を入力してください。');
      }
      if (!gender) {
        errors.push('性別を選択してください。');
      }
      if (!age || age < 18 || age > 100) {
        errors.push('年齢は18歳以上100歳以下で入力してください。');
      }
      if (!busho) {
        errors.push('部署を選択してください。');
      }
      if (!position) {
        errors.push('役職を選択してください。');
      }
      if (!year || year.length !== 4) {
        errors.push('入社年は4桁で入力してください。');
      }

      // エラーがあればアラートで表示
      if (errors.length > 0) {
        event.preventDefault(); // フォームの送信を防ぐ
        alert(errors.join('\n')); // エラーメッセージをアラートで表示
      }else{
        alert('更新完了しました。'); // 更新完了メッセージを表示
      }
    });
  });
</script>

</body>
</html>
