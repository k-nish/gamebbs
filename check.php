<?php
require('db.php');
session_start();

$sql = 'SELECT * FROM `secret` WHERE 1';
$stmt = mysqli_query($db,$sql) or die(mysqli_error($db));
$rec = mysqli_fetch_assoc($stmt);
$editid = $rec['editid'];
$editpass = $rec['editpass'];

$error = array();
if (isset($_POST)&&!empty($_POST)) {
    if ($_POST['editid'] == $editid && $_POST['editpass'] == $editpass) {
        $_SESSION['editid'] = $_POST['editid'];
        $_SESSION['editpass'] = $_POST['editpass'];
        header('Location: http://ut-sunfriend.com/gamebbs/edit.php');
    } else if (($_POST['editid'] != $editid )&&($_POST['editpass'] == $editpass)) {
        $error['id'] = 'wrong';
    } else if (($_POST['editid'] == $editid )&&($_POST['editpass'] != $editpass)) {
        $error['pass'] = 'wrong';
    } else if (($_POST['editid'] != $editid )&&($_POST['editpass'] != $editpass)) {
        $error['id'] = 'wrong';
        $error['pass'] = 'wrong';
    }
} else if (isset($_POST['editid'])&&$_POST['editid'] == '') {
    $error['id'] = 'blank';
} else if (isset($_POST['editpass'])&&$_POST['editpass'] == '') {
    $error['pass'] = 'blank';
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>サンフレンド実況掲示板!</title>

  <!-- CSS -->
  <link rel="stylesheet" href="assets/css/bootstrap.css">
  <link rel="stylesheet" href="assets/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="assets/css/form.css">
  <link rel="stylesheet" href="assets/css/timeline.css">
  <link rel="stylesheet" href="assets/css/main.css">
  <link rel="shotcut icon"  href="assets/favicon.ico">

</head>
<body>
  <nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
      <!-- Brand and toggle get grouped for better mobile display -->
      <div class="navbar-header page-scroll">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="http://ut-sunfriend.com/gamebbs/check.php"><span class="strong-title"><i class="fa fa-sun-o"></i> 管理者ログインページ</span></a>
      </div>
      <!-- Collect the nav links, forms, and other content for toggling -->
      <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        <ul class="nav navbar-nav navbar-right">
          <li class="page-scroll">
              <a href="http://ut-sunfriend.com">HPへ戻る</a>
          </li>
          <li class="page-scroll">
              <a href="http://ut-sunfriend.com/gamebbs/index.php">実況掲示板TOPへ</a>
          </li>
          <li class="page-scroll">
              <a href="http://ut-sunfriend.com/gamebbs/check.php">編集用ページ</a>
          </li>
        </ul>
      </div>
      <!-- /.navbar-collapse -->
    </div>
      <!-- /.container-fluid -->
  </nav>
  <div class="container">
    <div class="row">
      <div class="col-md-5 content-margin-top">
        <form action="http://ut-sunfriend.com/gamebbs/check.php" method="post">
          <div class="form-group">
            <h1>ID</h1>
            <div class="input-group">
              <input type="text" name="editid" class="form-control"
                     id="validate-text" placeholder="ID" required>
              <span class="input-group-addon danger"><span class="glyphicon glyphicon-remove"></span></span>
            </div>
          </div>
          <div class="form-group">
            <h1>Password</h1>
            <div class="input-group" data-validate="length" data-length="3">
            <input type="text" class="form-control" name="editpass" id="validate-length" placeholder="当ててみろ!" required>
            <span class="input-group-addon danger"><span class="glyphicon glyphicon-remove"></span></span>
          </div>
      </div>
      <?php if ((isset($error['id'])&&$error['id'] == 'wrong') || (isset($error['pass'])&&$error['pass'] == 'wrong')) { ?>
      <p class='error'>*IDかpasswordを間違えています。</p>
      <?php } ?>
      <button type="submit" href="http://ut-sunfriend.com/gamebbs/edit.php" class="btn btn-danger col-xs-12" disabled>ログイン</button>
    </form>
    </div>
    <div class="col-md-7 content-margin-top">
      <div class="timeline-centered">
      </div>
    </div>
  </div>
  </div>
  <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
  <!-- Include all compiled plugins (below), or include individual files as needed -->
  <script src="assets/js/bootstrap.js"></script>
  <script src="assets/js/form.js"></script>
</body>
</html>