<?php

require_once('config.php');
require_once('functions.php');

session_start();
$dbh = connectDb();


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  $email = $_POST['email'];
  $password = $_POST['password'];
  $errors = [];

  if ($email == '') {
    $errors[] = 'emailが未入力です';
  }

  if ($password == '') {
    $errors[] = 'passwordが未入力です';
  }

  if (empty($errors)) {
    $sql = 'select * from users where email = :email';
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (password_verify($password, $user['password'])) {
      $_SESSION['id'] = $user['id'];
      if ($_SESSION['id']) {
        header('Location: index.php');
        exit;
      }
    } else {
      $errors[] = 'メールアドレスパスワードが間違っています';
    }
  }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ログイン画面</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="css/style.css">
</head>

<body>
  <div class="flex-col-area">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-5">
      <a href="http://localhost/index.php" class="navbar-brand">Hair set style</a>
      <div class="collapse navbar-collapse" id="navbarToggle">
        <ul class="navbar-nav ml-auto mt-2 mt-lg-0">
          <?php if ($_SESSION['id']) : ?>
            <!-- ログアウト -->
            <li class="nav-item">
              <a href="sign_out.php" class="nav-link"><i class="fas fa-sign-out-alt fa-lg"></i></a>
            </li>
            <!-- NewPost -->
            <li class="nav-item">
              <a href="new.php" class="nav-link"><i class="fas fa-camera-retro fa-lg"></i></a>
            </li>
            <!-- お気に入り -->
            <li class="nav-item">
              <a href="favorite.php" class="nav-link"><i class="far fa-images fa-lg"></i></a>
            </li>
          <?php else : ?>
            <!-- サインイン -->
            <li class="nav-item">
              <a href="sign_in.php" class="nav-link"><i class="fas fa-sign-in-alt fa-lg"></i></a>
            </li>
            <!-- アカウント登録 -->
            <li class="nav-item">
              <a href="sign_up.php" class="nav-link"><i class="fas fa-user-plus fa-lg"></i></a>
            </li>
          <?php endif; ?>
        </ul>
      </div>
    </nav>
    <div class="row">
      <div class="col-sm-6">
        <div class="card">
          <div class="card-body">
            <h5>ログイン</h5>
            <?php if ($errors) : ?>
              <ul class="alert">
                <?php foreach ($errors as $error) : ?>
                  <li><?php echo $error; ?></li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
            <form class="" action="sign_in.php" method="post">
              <div class="form-group">
                <label for="email">メールアドレス</label>
                <input type="email" class="form-control" required autofocus name="email">
              </div>
              <div class="form-group">
                <label for="password">パスワード</label>
                <input type="password" class="form-control" required name="password">
              </div>
              <div class="form-group">
                <input type="submit" value="ログイン" class="login-button">
              </div>
              <a href="sign_up.php">アカウント登録</a>
          </div>
        </div>
      </div>
    </div>
    </form>

</body>

</html>