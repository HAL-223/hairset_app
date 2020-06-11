<?php
require_once('config.php');
require_once('functions.php');

session_start();

$dbh = connectDb();

$keyword = $_GET['keyword'];

// stylesの取得
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
SQL;

// 条件付加

if (isset($keyword)) {
  $sql_where = " where s.body like :keyword";
} else {
  $sql_where = "";
}

$sql_order = ' ORDER BY s.created_at DESC';

// sqlの結合
$sql = $sql . $sql_where . $sql_order;
$stmt = $dbh->prepare($sql);

// キーワード検索された場合
if (isset($keyword)) {
  $sql_where = " where s.body like :keyword";
  $keyword_param = '\'%' . $keyword . '%\'';
  $stmt->bindParam(":keyword", $keyword_param, PDO::PARAM_INT);
}

$stmt->execute();
$styles = $stmt->fetchAll(PDO::FETCH_ASSOC);
  

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
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-3">
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

    <div class="search pr-3 my-3">
      <form action="" method="get">
        <input type="text" name="keyword" placeholder="SEARCH">
        <input type="submit" class="btn btn-dark" value="検索">
      </form>
      <!-- <form class="form-inline my-3">
        <input class="form-control mr-sm-2" type="search" name="keyword" placeholder="SEARCH" aria-label="Search">
        <button type="submit" class="btn btn-dark">検索</button>
      </form> -->
    </div>
    <div class="container">
      <div class="row">
        <div class="col-sm-10 col-md-10 col-lg-10 mx-auto">
          <div class="row">
            <?php foreach ($styles as $style) : ?>
              <div class="col-md-4">
                <div class="article">
                  <a href="show.php?id=<?php echo h($style['id']) ?>"><img src="<?php echo h('style_img/' . $style['picture']); ?>" alt=""></a>
                  <p>☆:<?php echo h($style['user_name']); ?></p>
                  <p>投稿日:<?php echo h($style['created_at']); ?></p>
                  <p><?php echo h($style['body']); ?></p>
                </div>
                <hr>
              </div>
            <?php endforeach; ?>
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