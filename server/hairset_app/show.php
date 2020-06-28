<?php

require_once('config.php');
require_once('functions.php');

session_start();

$id = $_GET['id'];
if (!is_numeric($id)) {
  header('Location: index.php');
  exit;
}

$dbh = connectDb();

if ($_SESSION['id']) {

  $sql = <<<SQL
SELECT
  s.*,
  c.name,
  u.name as user_name,
  g.id as good_id
FROM
  styles s
LEFT JOIN
  categories c
ON
  s.category_id = c.id
LEFT JOIN
  users u
ON 
  s.user_id = u.id
LEFT JOIN
  good g
ON 
  s.id = g.style_id
AND
  g.user_id = :user_id
WHERE
  s.id = :id
SQL;
} else {
  $sql = <<<SQL
SELECT
  s.*,
  c.name,
  u.name as user_name
FROM
  styles s
LEFT JOIN
  categories c
ON
  s.category_id = c.id
LEFT JOIN
  users u
ON 
  s.user_id = u.id
WHERE
  s.id = :id
SQL;
}

$stmt = $dbh->prepare($sql);

if ($_SESSION['id']) {
  $stmt->bindParam(":user_id", $_SESSION['id'], PDO::PARAM_INT);
}

$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();

$style = $stmt->fetch(PDO::FETCH_ASSOC);

if (empty($style)) {
  header('Location: show.php');
  exit;
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
  <script src="https://kit.fontawesome.com/f8d88e43cf.js" crossorigin="anonymous"></script>
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

    <div class="container">
      <div class="row">
        <div class="col-md-11 col-lg-9 mx-auto mt-5">
          <p>
            <img src="<?php echo h('style_img/' . $style['picture']); ?>" alt="">
          </p>
          <p>Categories : <?php echo h($style['name']); ?></p>
          <?php echo (h($style['body'])); ?>
          <?php if ($_SESSION['id']) : ?>
            <?php if ($style['good_id']) : ?>
              <a href="good.php?id=<?php echo h($style['good_id']); ?>" class="btn-bad-link"><i class="fas fa-thumbs-up"></i></a>
            <?php else : ?>
              <a href="good.php?style_id=<?php echo h($style['id']) . "&user_id=" . $_SESSION['id']; ?>" class="btn-good-link"><i class="far fa-thumbs-up"></i></a>
            <?php endif; ?>
          <?php endif; ?>
          <hr>
          <p>Posted date : <?php echo h($style['created_at']); ?></p>

          <?php if (($_SESSION['id']) && ($_SESSION['id'] == $style['user_id'])) : ?>
            <button type="button" class="btn page-link text-dark d-inline-block" data-toggle="modal" data-target="#style-edit">編集</button>
            <!-- <a href="edit.php?id=<?php echo h($style['id']); ?>" class="btn">編集</a> -->
            <button type="button" class="btn page-link text-dark d-inline-block" data-toggle="modal" data-target="#style-delete">削除</button>
          <?php endif; ?>
          <a href="index.php" class="btn page-link text-dark d-inline-block">戻る</a>
        </div>
      </div>
      <br>
    </div>
    <!-- ヘッター -->
    <footer class="footer font-small bg-dark">
      <div class="footer-copyright text-center py-3 text-light">&copy; HAL hair</div>
    </footer>
  </div>
  <!-- モーダル画面 -->
  <div class="modal fade" id="style-delete" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">
            「<?php echo h($style['body']) ?>」の投稿を削除しますか？
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p>
            <img src="<?php echo h('style_img/' . $style['picture']); ?>" alt="">
          </p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">キャンセル</button>
          <a href="delete.php?id=<?php echo h($style['id']) ?>" class="btn btn-warning">削除</a>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="style-edit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">
            「<?php echo h($style['body']) ?>」の投稿を編集しますか？
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p>
            <img src="<?php echo h('style_img/' . $style['picture']); ?>" alt="">
          </p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">いいえ</button>
          <a href="edit.php?id=<?php echo h($style['id']) ?>" class="btn btn-default">はい</a>
        </div>
      </div>
    </div>
  </div>
</body>

</html>