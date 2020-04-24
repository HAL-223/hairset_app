<?php
require_once('config.php');
require_once('functions.php');

session_start();
$dbh = connectDb();

$sql = 'select * from categories order by id';
$stmt = $dbh->prepare($sql);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $category_id = $_POST['category_id'];
  $user_id = $_SESSION['id'];
  $picture = $_FILES['image']['name'];
  $body = $_POST['body'];

  $errors = [];

  if ($category_id == '') {
    $errors[] = 'カテゴリーが未選択です';
  }

  // if ($picture) {
  //   $ext = substr($picture, -3);
  //   if ($ext != 'jpg' && $ext != 'git') {
  //     $errors[] = '画像アップロードに失敗しました';
  //   }
  // }
  // if (empty($errors)) {
  //   $image = date('YmgHis') . $_FILES['image']['name'];
  //   move_uploaded_file($_FILES['image']['tmp_name'], 'styles/' . $image);
  //   $_SESSION['join'] = $_POST;
  //   $_SESSION['join']['image'] = $image;
  // }
  if ($picture) {
    $ext = substr($picture, -4);
    if ($ext == '.gif' || $ext == '.jpg' || $ext == '.png') {
      $filePath = './style_img/' . $_FILES['name'];
      $success = move_uploaded_file($_FILES['tmp_name'], $filePath);
    }
  } else {
    $errors[] = '画像アップロードに失敗しました';
  }

  if (empty($errors)) {
    $sql = <<<SQL
    insert into
    styles
    (
      category_id,
      user_id,
      picture,
      body
    )
    values
    (
      :category_id,
      :user_id,
      :picture,
      :body
    )
    SQL;
    $stmt = $dbh->prepare($sql);

    $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':picture', $picture, PDO::PARAM_STR);
    $stmt->bindParam(':body', $body, PDO::PARAM_STR);
    $stmt->execute();
  }
  header("Location: show.php?id={$id}");
  exit;
}
?>
<!DOCTYPE html>
<html lang="ja">

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
              <h5 class="card-title text-center">New Post</h5>
              <?php if ($errors) : ?>
                <ul class="alert">
                  <?php foreach ($errors as $error) : ?>
                    <li><?php echo $error; ?></li>
                  <?php endforeach; ?>
                </ul>
              <?php endif; ?>
              <form action="new.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                  <input type="file" name="image" id="">
                </div>
                <div class="form-group">
                  <label for="category_id">Category</label>
                  <select name="category_id" class="form-control" required>
                    <option value='' disabled selected>選択して下さい</option>
                    <?php foreach ($categories as $c) : ?>
                      <option value="<?php echo h($c['id']); ?>"><?php echo h($c['name']); ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="form-group">
                  <label for="body">Text</label>
                  <textarea name="body" id="" cols="30" rows="10" class="form-control"></textarea>
                </div>
                <div class="form-group text-center">
                  <input type="submit" value="Post" class="button">

                  <!-- <input type="submit" value="Post" class="btn-block"> -->
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    <footer class="footer font-small bg-dark">
      <div class="footer-copyright text-center py-3 text-light">&copy; HAL hair</div>
    </footer>
  </div>
</body>

</html>