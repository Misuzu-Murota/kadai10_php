<!-- 社員情報一覧 -->
<?php
//0. SESSION開始！！
session_start();

//1.  DB接続します
// 関数用のファイルを使用できるように呼び出す
require_once('funcs.php');

//LOGINチェック → funcs.phpへ関数化しましょう！
sschk();

// 上記のfuncs.phpに書いている関数(db_conn)を呼び出して
// データベースに接続し、データ取得できるようにします。
// なるべく呼び出し先の関数と同じ変数名($pdoのことです)にしておくのが混乱を防ぐのにおすすめです
$pdo = db_conn();

//２．データ登録SQL作成
$sql = "SELECT * FROM allmembers WHERE life_flg != 1;";
$stmt = $pdo->prepare($sql);
$status = $stmt->execute(); //True or false

//３．データ表示
if($status==false) {
  //execute（SQL実行時にエラーがある場合）
  $error = $stmt->errorInfo();
  exit("SQL_ERROR:".$error[2]);
}

//全データ取得
$values =  $stmt->fetchAll(PDO::FETCH_ASSOC); //PDO::FETCH_ASSOC[カラム名のみで取得できるモード]
//JSONに値を渡す場合に使う
$json = json_encode($values,JSON_UNESCAPED_UNICODE);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>社員情報一覧</title>
<link rel="stylesheet" href="css/range.css">
<style>
    div {
        padding: 5px;
        font-size: 16px;
    }
    .button-container {
        margin: 5px 0; /* ボタンの上部と下部にマージンを追加 */
    }
    .navbar-brand {
        margin: 0 10px; /* ボタン間の距離を広げるためにマージンを追加 */
        display: inline-block; /* ボタンとして表示 */
        padding: 8px 8px; /* ボタンのパディング */
        background-color: #007bff; /* ボタンの背景色 */
        color: white; /* ボタンのテキスト色 */
        border: none; /* ボタンのボーダーをなしに */
        border-radius: 4px; /* ボタンの角を丸く */
        text-decoration: none; /* テキストの下線をなしに */
        transition: background-color 0.3s; /* ホバー時の背景色の遷移 */
    }
    .navbar-brand:hover {
        background-color: #0056b3; /* ホバー時の背景色 */
    }
</style>
</head>
<body id="main">
<!-- Head[Start] -->
<header>
  <nav class="navbar navbar-default">
    <div class="container-fluid">
      <div class="navbar-header">
        <?=$_SESSION["name"]?>さん、こんにちは！ </br>
        <div class="button-container">
        <?php if ($_SESSION["kanri_flg"] == "1") { ?>
            <a class="navbar-brand" href="index.php">社員情報登録</a>
            <a class="navbar-brand" href="record.php">ログイン履歴</a>
        <?php } ?>
            <a class="navbar-brand" href="pw_change.php">パスワード変更</a>        
            <a class="navbar-brand" href="logout.php">ログアウト</a>
        </div>
      </div>
    </div>
  </nav>
</header>
<!-- Head[End] -->

<!-- Main[Start] -->
<div>
    <div class="container jumbotron">
        <table class="styled-table">
            <thead>
            <tr>
        <th onclick="sortTable(0)">社員番号</th>
        <th onclick="sortTable(1)">名前</th>
        <th onclick="sortTable(2)">性別</th>
        <th onclick="sortTable(3)">年齢</th>
        <th onclick="sortTable(4)">部署</th>
        <th onclick="sortTable(5)">役職</th>
        <th onclick="sortTable(6)">入社年</th>
        <?php if($_SESSION["kanri_flg"]=="1"){ ?>
            <th onclick="sortTable(7)">最終更新日</th>
            <th onclick="sortTable(8)">管理者フラグ</th>
            <th>更新</th>
            <th>削除</th>
        <?php } ?>
    </tr>
            </thead>
            <tbody>
                <?php foreach($values as $value){ ?>
                <tr>
                    <td><?=$value["shainno"]?></td>
                    <td><?=$value["name"]?></td>
                    <td><?=$value["gender"]?></td>
                    <td><?=$value["age"]?></td>
                    <td><?=$value["busho"]?></td>
                    <td><?=$value["position"]?></td>
                    <td><?=$value["year"]?></td>
                    <?php if($_SESSION["kanri_flg"]=="1"){ ?>
                    <td><?= date('Y年m月d日 H:i', strtotime($value["indate"])) ?></td>
                    <td>
                        <?php if ($value["kanri_flg"] == 1): ?>
                            ✔️
                        <?php else: ?>
                            <!-- 空欄 -->
                        <?php endif; ?>
                    </td>
                    <td><a href="detail.php?shainno=<?=h($value["shainno"])?>">更新</a></td>    
                    <td>
                        <a href="#" onclick="confirmDelete('<?=h($value["shainno"])?>', '<?=h($value["name"])?>')">削除</a>
                    </td>
                    <?php } ?>
                </tr>
                <?php } ?>
            </tbody>
        </table> 
    </div>
</div>
<!-- Main[End] -->

<script>
  //JSON受け取り
  const a = '<?php echo $json; ?>';
  console.log(JSON.parse(a));

  function confirmDelete(shainno, name) {
    const message = `社員番号：${shainno} 氏名：${name}の社員情報を本当に削除しますか？`;
    if (confirm(message)) {
      // OKが押された場合、削除処理のためにdelete.phpに遷移
      window.location.href = `delete.php?shainno=${shainno}`;
    }
  }

  let sortOrder = {}; // 各カラムのソート順を保持するオブジェクト

  function sortTable(columnIndex) {
    const table = document.querySelector(".styled-table tbody");
    const rows = Array.from(table.rows);

    // 現在のソート順をトグル
    sortOrder[columnIndex] = !sortOrder[columnIndex]; // true:昇順, false:降順
    const isAscending = sortOrder[columnIndex];

    // 並び替えのキーとなるデータを取得
    const sortedRows = rows.sort((rowA, rowB) => {
        const cellA = rowA.cells[columnIndex].innerText;
        const cellB = rowB.cells[columnIndex].innerText;

        // 数値として比較する場合と、文字列として比較する場合を考慮
        const a = isNaN(cellA) ? cellA : parseFloat(cellA);
        const b = isNaN(cellB) ? cellB : parseFloat(cellB);

        return isAscending ? (a > b ? 1 : -1) : (a < b ? 1 : -1);
    });

    // テーブルを空にしてから並び替えた行を追加
    table.innerHTML = "";
    sortedRows.forEach(row => table.appendChild(row));
}
</script>
</body>
</html>
