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

$sql = <<<SQL
select
  s.*,
  c.name
from
  styles s
left join
  categories c
on
  s.category_id = c.id
where
  s.id = :id
SQL;

$stmt = $dbh->prepare($sql);
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
        <div class="col-md-11 col-lg-9 mx-auto mt-5">
          <p>
            <img src="<?php echo h('style_img/' . $style['picture']); ?>" alt="">
          </p>
          <p>Categories : <?php echo h($style['name']); ?></p>
          <?php echo (h($style['body'])); ?>
          <br>
          <?php if ($tweet['good'] == false) : ?>
            <a href="good.php?id=<?php echo h($style['id']) . "&good=1"; ?>" class="bad-link"><?php echo '☆'; ?></a>
          <?php else : ?>
            <a href="good.php?id=<?php echo h($style['id']) . "&good=0"; ?>" class="good-link"><?php echo '★'; ?></a>
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