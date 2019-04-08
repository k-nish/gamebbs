<?php
try{
require('db.php');
session_start();

//ログインチェック用のid,passを取得
$sql = 'SELECT * FROM `secret` WHERE 1';
$stmt = mysqli_query($db,$sql) or die(mysqli_error($db));
$rec = mysqli_fetch_assoc($stmt);
$login_pass = $rec['pass'];

//ログインチェック
if ($_SESSION['pass'] != $login_pass) {
  header('Location: http://ut-sunfriend.com/gamebbs/index.php');
}

function h($value){
    return htmlspecialchars($value,ENT_QUOTES,'UTF-8');
}

$gameid ='';
if (isset($_GET['gameid'])&&!empty($_GET)) {
    $gameid = $_GET['gameid'];
}

//ページング処理
$page = '';
if (isset($_GET['page'])) {
  $page = $_GET['page'];
}
if ($page=='') {
  $page = 1;
}
$page = max($page,1);
$table = array();
$s = sprintf('SELECT COUNT(*) AS cnt FROM `results` WHERE `gameid`= %d',
        mysqli_real_escape_string($db,$gameid));
$stmt = mysqli_query($db,$s) or die(mysqli_error($db));
$table = mysqli_fetch_assoc($stmt);
$maxpage = ceil($table['cnt'] / 6);
$page = min($page,$maxpage);
$start = ($page -1) * 6;
$start = max($start,0);

$resultid = '';
$result = '';
$edit_result = '';
 //編集するための編集元の情報を表示
if(isset($_GET['action'])&& ($_GET['action']=='edit')) {
    $sql = sprintf('SELECT * FROM `results` WHERE id=%d',
            mysqli_real_escape_string($db,$_GET['resultid']));
    $stmt = mysqli_query($db,$sql) or die(mysqli_error($db));
    $rec = mysqli_fetch_assoc($stmt);
    $gameid = $rec['gameid'];
    $resultid = $rec['id'];
    $edit_result = $rec['result'];
} elseif(isset($_GET['action'])&& ($_GET['action']=='delete')) {
    $sql = sprintf('SELECT * FROM `results` WHERE id=%d',
            mysqli_real_escape_string($db,$_GET['resultid']));
    $stmt = mysqli_query($db,$sql) or die(mysqli_error($db));
    $rec = mysqli_fetch_assoc($stmt);
    $gameid = $rec['gameid'];
    $sql=sprintf('DELETE FROM `results` WHERE `id`="%d"',
         mysqli_real_escape_string($db,$_GET['resultid']));
    $stmt = mysqli_query($db,$sql) or die(mysqli_error($db));
    header('Location: http://ut-sunfriend.com/gamebbs/kekka.php?gameid='.$gameid);
}

$error = array();
if(isset($_POST) && !empty($_POST)){
    if(isset($_POST['update'])&&!empty($_POST['result'])){
        $result = mb_convert_kana($_POST['result'],'sa','UTF-8');
        $sql = sprintf('UPDATE `results` SET `result`="%s" WHERE `id`=%d',
                 mysqli_real_escape_string($db,$result),
                 mysqli_real_escape_string($db,$_POST['resultid']));
        $stmt = mysqli_query($db,$sql) or die(mysqli_error($db));
    } else {
        $result = mb_convert_kana($_POST['result'],'sa','UTF-8');
        $contributor = mb_convert_kana('sunfriend','sa','UTF-8');
        $sql = sprintf('INSERT INTO `results`(`result`, `contributor`, `date`, `gameid`)
            VALUES ("%s","%s",now(),"%d")',
            mysqli_real_escape_string($db,$result),
            mysqli_real_escape_string($db,$contributor),
            mysqli_real_escape_string($db,$gameid));
        $stmt = mysqli_query($db,$sql) or die(mysqli_error($db));
        header('Location: http://ut-sunfriend.com/gamebbs/kekka.php?gameid='.$gameid);
    }
}

$sql = sprintf('SELECT * FROM `results` WHERE gameid = "%d" ORDER BY `id` DESC LIMIT %d,6',
    mysqli_real_escape_string($db,$gameid),
    mysqli_real_escape_string($db,$start));
$stmt = mysqli_query($db,$sql) or die(mysqli_error($db));
$posts = array();
while(1){
    $rec = mysqli_fetch_assoc($stmt);
    if($rec == false){
        break;
    }
    $posts[]=$rec;
}

$sq = sprintf('SELECT * FROM `names` WHERE gameid ="%d"',
  mysqli_real_escape_string($db,$gameid));
$stmt = mysqli_query($db,$sq) or die(mysqli_error($db));
$rec = mysqli_fetch_assoc($stmt);
$name = $rec['gamename'];

$dbh=null;
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>SunFriend掲示版!結果ページ!</title>
    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <link rel="stylesheet" href="assets/font-awesome/css/font-awesome.css">
    <link rel="stylesheet" href="assets/css/form.css">
    <link rel="stylesheet" href="assets/css/timeline.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/link.css">
    <link rel="shotcut icon"  href="assets/favicon.ico">

    <script type="text/javascript">
    function destroy(resultid){
        console.log(resultid);
        if (confirm('削除しますか')) {
           location.href = 'http://ut-sunfriend.com/gamebbs/kekka.php?action=delete&resultid='+resultid;
           return true;
        }else{
            return false;
        }
    }
    </script>
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
                <a class="navbar-brand" href="http://ut-sunfriend.com/gamebbs/bbs.php"><span class="strong-title"><i class="fa fa-sun-o"></i>
                  SunFriend!実況掲示板!<?php echo h($name); ?></span></a>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right">
                    <li class="page-scroll">
                        <a href="http://ut-sunfriend.com">HPへ戻る</a>
                    </li>
                    <li class="page-scroll">
                        <a href="http://ut-sunfriend.com/gamebbs/bbs.php">実況掲示板TOPへ</a>
                    </li>
                    <li class="page-scroll">
                        <a href="http://ut-sunfriend.com/gamebbs/check.php">編集用ページへ</a>
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
            <form action="http://ut-sunfriend.com/gamebbs/kekka.php?gameid=<?php echo h($gameid); ?>" method="post">
                <div class="form-group">
                    <h1>実況</h1>
                    <div class="input-group" data-validate="length" data-length="1">
                        <?php if (isset($error['key'])&&($error['key']=='wrong')) { ?>
                        <input type="text" class="form-control" name="result" id="validate-length"
                            placeholder="結果 ex.ファイナルイン!" value="<?php echo h($_POST['result']); ?>" required>
                        <?php }else{ ?>
                        <input type="text" class="form-control" name="result" id="validate-length" placeholder="結果 ex.ファイナルイン!"
                        value="<?php echo h($edit_result); ?>" required>
                        <?php } ?>
                        <span class="input-group-addon danger"><span class="glyphicon glyphicon-remove"></span></span>
                    </div>
                </div>
                <!-- <h5>実況投稿!</h5> -->
                <?php if($edit_result=='') { ?>
                    <input type="hidden" name="gameid" value=<?php echo h($gameid); ?>>
                    <button type="submit"  name='report' class="btn btn-danger col-xs-12" disabled>実況する!</button>
                <?php }elseif($edit_result!='') {?>
                    <input type="hidden" name="resultid" value=<?php echo h($resultid); ?>>
                    <button type="submit" name="update" class="btn btn-danger col-xs-12" disabled>書き直す</button>
                <?php } ?>
                <br><br>
                <p>
                    <?php if ($page<$maxpage){ ?>
                    <a href="http://ut-sunfriend.com/gamebbs/kekka.php?page=<?php echo ($page + 1); ?>&gameid=<?php echo $gameid; ?>" class="btn btn-default">以前の投稿へ</a>
                    <?php }else{ ?>
                    最終ページだよ
                    <?php } ?>
                    <?php if ($page>1) { ?>
                    <a href="http://ut-sunfriend.com/gamebbs/kekka.php?page=<?php echo ($page - 1); ?>&gameid=<?php echo $gameid; ?>" class="btn btn-default">最新の投稿へ</a>
                    <?php }else{ ?>
                    最新のページだよ
                    <?php } ?>
                </p>
            </form>
        </div>
          <!--<h3>実況なう!</h3>-->
        <div class="col-md-7 content-margin-top">
            <!--<h3>実況なう!</h3>-->
            <div class="timeline-centered">
            <?php foreach ($posts as $post) { ?>
                <article class="timeline-entry">
                    <div class="timeline-entry-inner">
                        <div class="timeline-icon bg-info">
                            <i class="entypo-feather"></i>
                            <i class="fa fa-play-circle"></i>
                        </div>
                        <div class="timeline-label">
                            <h2><a href="#"><?php echo h($post['contributor']);?></a>
                                <?php
                                    //一旦日時型に変換
                                    $date = strtotime($post['date']);
                                    //書式を変換
                                    $date = date('Y/m/d H:i:s',$date);
                                ?>
                                <span style="font-size:20px"><?php echo h($date);?></span>
                                <a href="http://ut-sunfriend.com/gamebbs/kekka.php?action=edit&gameid=<?php echo $gameid; ?>&resultid=<?php echo h($post['id']); ?>"><i class="fa fa-pencil-square-o bg-info"></i></a>
                                <!-- 削除ボタンを使う場合は下の行のコメントアウトを外す -->
                                <!-- <a href="#" onclick="destroy(<?php echo h($post['id']);?>)"><i class="fa fa-trash-o red_button"></i></a> -->
                            </h2>
                            <p><strong><?php echo h($post['result']);?><strong></br></p>
                        </div>
                </article>
            <?php } ?>
            <article class="timeline-entry begin">
                <div class="timeline-entry-inner">
                    <div class="timeline-icon" style="-webkit-transform: rotate(-90deg); -moz-transform: rotate(-90deg);">
                        <i class="entypo-flight"></i> +
                    </div>
                </div>
            </article>
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
<?php }catch(Excepton $e){
    echo "サーバーエラーが生じております。sunfriend2016@gamil.comまでご連絡ください。";
} ?>

