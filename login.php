<?php
session_start();
if (isset($_SESSION["login_error"])) {
    echo '<p style="color:red;">'.$_SESSION["login_error"].'</p>';
    unset($_SESSION["login_error"]); // エラーメッセージを一度表示したら削除
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/main.css">
  <title>社員情報一覧 - ログイン</title>
  <style>
    body {
      background-color: #f8f9fa;
      font-family: 'Arial', sans-serif;
    }

    .navbar {
      background-color: #343a40;
      color: white;
      padding: 15px;
      text-align: center;
    }

    .container {
      max-width: 500px;
      margin: 50px auto;
      padding: 20px;
      background-color: white;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .form-group input {
      width: 100%;
      padding: 10px;
      margin: 10px 0;
      border-radius: 4px;
      border: 1px solid #ced4da;
    }

    .form-group input:focus {
      border-color: #80bdff;
      outline: none;
      box-shadow: 0 0 5px rgba(0, 123, 255, 0.25);
    }

    .btn {
      width: 100%;
      padding: 10px;
      background-color: #007bff;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }

    .btn:hover {
      background-color: #0056b3;
    }

    .login-title {
      text-align: center;
      margin-bottom: 20px;
      font-size: 24px;
      font-weight: bold;
      color: #343a40;
    }

    .login-subtitle {
      text-align: center;
      margin-bottom: 20px;
      color: #6c757d;
    }

    .error-message {
      color: red;
      text-align: center;
      margin-bottom: 20px;
    }
  </style>
</head>
<body>

<header>
  <nav class="navbar">
    社員情報一覧
  </nav>
</header>

<div class="container">
  <h1 class="login-title">ログイン</h1>
  <p class="login-subtitle">社員情報にアクセスするにはログインしてください。</p>

  <?php if (isset($_SESSION["login_error"])): ?>
    <p class="error-message"><?= $_SESSION["login_error"] ?></p>
    <?php unset($_SESSION["login_error"]); ?>
  <?php endif; ?>

  <!-- lLOGINogin_act.php は認証処理用のPHPです。 -->
  <form name="form1" action="login_act.php" method="post">
    <div class="form-group">
      <label for="lid">社員番号:</label>
      <input type="number" name="lid" class="form-control" placeholder="社員番号を入力" required>
    </div>
    <div class="form-group">
      <label for="lpw">パスワード:</label>
      <input type="password" name="lpw" class="form-control" placeholder="パスワードを入力" required>
    </div>
    <button type="submit" class="btn btn-primary">ログイン</button>
  </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="js/bootstrap.min.js"></script>

</body>
</html>
