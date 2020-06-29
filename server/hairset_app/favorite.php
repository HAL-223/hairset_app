<?php
require_once('config.php');
require_once('functions.php');

session_start();

$id = $_GET['id'];
$user_id = $_SESSION['id'];
$style_id  = $_GET['style_id'];

$dbh = connectDb();

$sql = <<<SQL
SELECT
  s.*,
  u.name as user_name,
  g.user_id,
  g.id as good_id
FROM
  styles s
LEFT JOIN
  users u
ON
  s.user_id = u.id
INNER JOIN
  good g
ON 
  s.id = g.style_id
AND
  g.user_id = :user_id
SQL;

$good = 'select count(good) > 0 from styles';

$stmt = $dbh->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();

$styles = $stmt->fetchAll(PDO::FETCH_ASSOC);



// if ($style['good'] = 0) {
//   return $styles;
// }

//   $stmt = $dbh->prepare($sql);
//   $stmt->bindParam(":good", $good, PDO::PARAM_INT);
//   $stmt->execute();
//   $styles = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
  <script src="https://kit.fontawesome.com/f8d88e43cf.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="css/style.css">
</head>

<body>
  <div class="flex-col-area">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-3">
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
        <div class="col-sm-10 col-md-10 col-lg-10 mx-auto">
          <div class="row">
            <?php foreach ($styles as $style) : ?>
              <div class="col-md-4">
                <div class="article">
                  <a href="show.php?id=<?php echo h($style['id']) ?>"><a class="thumbnail" href="image1.jpg" target="_blank"><img src="<?php echo h('style_img/' . $style['picture']); ?>" alt="" class="img-fluid img-thumbnail"></a></a>
                    <p>☆:<?php echo h($style['user_name']); ?></p>
                    <p>投稿日:<?php echo h($style['created_at']); ?></p>
                    <p><?php echo h($style['body']); ?></p>
                    <?php if ($_SESSION['id']) : ?>
                      <?php if ($style['good_id']) : ?>
                        <a href="good.php?id=<?php echo h($style['good_id']); ?>" class="btn-bad-link"><i class="fas fa-thumbs-up"></i></a>
                      <?php else : ?>
                        <a href="good.php?style_id=<?php echo h($style['id']) . "&user_id=" . $_SESSION['id']; ?>" class="btn-good-link"><i class="far fa-thumbs-up"></i></a>
                      <?php endif; ?>
                    <?php endif; ?>
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