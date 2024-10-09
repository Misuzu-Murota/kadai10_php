<?php
//0. SESSION開始！！
session_start();

//１．関数群の読み込み
include("funcs.php");

//LOGINチェック
sschk();

//２．データ登録SQL作成
$pdo = db_conn();

// 1ページあたりの表示件数
$limit = 10;

// 現在のページを取得
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// 総レコード数を取得
$total_sql = "SELECT COUNT(*) FROM user_table";
$total_stmt = $pdo->prepare($total_sql);
$total_stmt->execute();
$total_count = $total_stmt->fetchColumn();

// 総ページ数を計算
$total_pages = ceil($total_count / $limit);

// データを取得するSQLを変更
$sql = "SELECT * FROM user_table ORDER BY indate DESC LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$status = $stmt->execute();

//３．データ表示
if($status==false) {
  sql_error($stmt);
}

//全データ取得
$values =  $stmt->fetchAll(PDO::FETCH_ASSOC);
$json = json_encode($values, JSON_UNESCAPED_UNICODE);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ログイン履歴</title>
    <link rel="stylesheet" href="css/range.css">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 10px;
        }
        .jumbotron {
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        table {
            width: 100%;
            margin-top: 10px;
            border-collapse: collapse;
        }
        th, td {
            text-align: center;
            padding: 10px;
            border: 1px solid #dee2e6;
        }
        th {
            background-color: #f2f2f2;
        }
        .pagination {
            display: flex; /* Flexboxを使用して横並びに */
            justify-content: center;
            margin-top: 10px;
            list-style-type: none; /* リストマーカーを非表示に */
        }
        .page-item {
            margin: 0 5px; /* 各ページアイテムの間隔を設定 */
        }
    </style>
</head>
<body id="main">
<!-- Head[Start] -->
<header>
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <div class="navbar-header">
                <a class="navbar-brand" href="select.php">社員情報一覧に戻る</a>
            </div>
        </div>
    </nav>
</header>

<div>
    <div class="container jumbotron">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>部署</th>
                    <th>名前</th>
                    <th>日時</th>
                    <th>削除</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($values as $v) { ?>
                <tr>
                    <td><?= htmlspecialchars($v["busho"]) ?></td>
                    <td><?= htmlspecialchars($v["name"]) ?></td>
                    <td><?= date('Y年m月d日 H:i', strtotime($v["indate"])) ?></td>
                    <td><a href="delete_record.php?id=<?= $v["id"] ?>">[削除]</a></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- ページネーション -->
        <nav>
            <ul class="pagination">
                <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $page - 1 ?>">前のページ</a>
                </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= ($i === $page) ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $page + 1 ?>">次のページ</a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>

    </div>
</div>
<!-- Main[End] -->

<script>
    const a = '<?php echo $json; ?>';
    console.log(JSON.parse(a));
</script>
</body>
</html>
