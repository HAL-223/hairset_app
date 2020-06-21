<?php

require_once('config.php');
require_once('functions.php');

session_start();

$id = $_REQUEST['id'];
if (!is_numeric($id)) {
  header('Location: index.php');
  exit;
}

$dbh = connectDb();
// データの取得
$sql = 'SELECT * FROM styles WHERE id = :id';
$stmt = $dbh->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$style = $stmt->fetch(PDO::FETCH_ASSOC);

if (empty($style)) {
  header('Location: index.php');
  exit;
}
// カテゴリー取得
$sql = 'SELECT * FROM categories ORDER BY id';
$stmt = $dbh->prepare($sql);
$stmt->execute();

$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $category_id = $_POST['category_id'];
  $user_id = $_SESSION['id'];
  $picture = $_FILES['picture']['name'];
  $body = $_POST['body'];

  $errors = [];

  if ($category_id == '') {
    $errors[] = 'カテゴリーが未選択です';
  }

  if ($picture) {
    $ext = substr($picture, -3);
    if ($ext != 'jpg' && $ext != 'gif' && $ext != 'png') {
      $errors[] = 'アップロード失敗';
    } elseif (file_exists($picture)) {
      $errors[] = "画像が選択されておりません";
    }
  }


  if (empty($errors)) {
    $picture = date('YmgHis') . $picture;
    move_uploaded_file($_FILES['picture']['tmp_name'], 'style_img/' . $picture);
    $_SESSION['join'] = $_POST;
    $_SESSION['join']['picture'] = $picture;
  }

  if (empty($errors)) {
    $sql = <<<SQL
    UPDATE
      styles
    SET
      category_id = :category_id,
      user_id = :user_id,
      picture = :picture,
      body = :body
    WHERE
      id = :id
    SQL;
    $stmt = $dbh->prepare($sql);

    $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':picture', $picture, PDO::PARAM_STR);
    $stmt->bindParam(':body', $body, PDO::PARAM_STR);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    header("Location: show.php?id={$id}");
    exit;
  }
}
?>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HAIR SET STYLES</title>
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
            <li class="nav-item">
              <a href="sign_out.php" class="nav-link">ログアウト</a>
            </li>
            <li class="nav-item">
              <a href="new.php" class="nav-link">New Post</a>
            </li>
          <?php else : ?>
            <li class="nav-item">
              <a href="sign_in.php" class="nav-link">ログイン</a>
            </li>
            <li class="nav-item">
              <a href="sign_up.php" class="nav-link">アカウント登録</a>
            </li>
          <?php endif; ?>
        </ul>
      </div>
    </nav>

    <div class="container">
      <div class="row">
        <div class="col-sm-11 col-md-9 col-lg-7 mx-auto">
          <div class="card my-5">
            <div class="card-body">
              <h5 class="card-title text-center">記事編集</h5>
              <?php if ($errors) : ?>
                <ul class="alert alert-danger">
                  <?php foreach ($errors as $error) : ?>
                    <li><?php echo $error; ?></li>
                  <?php endforeach; ?>
                </ul>
              <?php endif; ?>
              <form action="edit.php?id={$id}" method="post" enctype="multipart/form-data">
                <div class="form-group">
                  <!-- <input type=" file" name="picture" id=""> -->
                  <img src="<?php echo h('style_img/' . $style['picture']); ?>" alt="">
                </div>
                <div class="form-group">
                  <label for="category_id">Category</label>
                  <select name="category_id" class="form-control" required>
                    <option value='' disabled selected>選択して下さい</option>
                    <?php foreach ($categories as $c) : ?>
                      <option value="<?php echo h($c['id']); ?>" <?php echo $style['category_id'] == $c['id'] ? "selected" : "" ?>>
                        <?php echo h($c['name']); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="form-group">
                  <label for="body">Text</label>
                  <textarea name="body" id="" cols="30" rows="10" class="form-control"><?php echo $style['body'] ?></textarea>
                </div>
                <div class="form-group text-center">
                  <input type="submit" value="Post" class="button btn page-link text-dark d-inline-block">
                </div>
              </form>
              <div class="back text-center">
                <a href=" show.php?id=<?php echo h($style['id']); ?>" class="btn page-link text-dark d-inline-block">戻る</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>