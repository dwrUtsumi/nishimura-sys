<?php
//タイムゾーンを設定
date_default_timezone_set('Asia/Tokyo');

// 本日日付を取得
$today = date('Y-m-d');

// 二日前日付を取得
$two_day_ago = date('Y-m-d', strtotime('-2 day'));


$year = date("Y");
$today_show = date('Ym'); //前月の表示・非表示判断に使う(<<前月マーク)
$ym = date('Y-m');
$ymd = date('Y-m') . '-01';
$timestamp = strtotime($ym . '-01');
//該当月の日数を取得
$day_count = '-' . date('t', $timestamp);
$ymd1 = date('Y-m') . $day_count;

$sql_reserve_array = [];


session_start();


//　各安全項目の日数の更新および取得
try {

    //　カウント用
    $s = 0;

     // DB接続
        $pdo = new PDO(

            //サーバー
            'mysql:dbname=heroku_4a02e2868c97e65;host=us-cdbr-east-05.cleardb.net;charset=utf8',
            'b891c787c3a4c7',
            'c6e85687',


            //ローカル
            // 'mysql:dbname=stsys;host=localhost;charset=utf8',
            // 'root',
            // 'shinei4005',

            // レコード列名をキーとして取得させる
            [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
        );


    //---------年間目標表示
    $get_disaster_Info = $pdo->prepare('SELECT * FROM kg_goal_letter WHERE safety_kbn="10"');
    $get_disaster_Info->execute();

    //　日数管理テーブルより、「日数」「アップデート日付」を配列に格納
    foreach ($get_disaster_Info as $sql_col1) {
        // $_SESSION["text_yearly"] = $sql_col1['safety_goal'];
        $text_yearly = $sql_col1['safety_goal'];
    }

    //---------月間目標の表示
    //---- 労働災害
    $get_disaster_Info = $pdo->prepare('SELECT * FROM kg_goal_letter WHERE safety_kbn="20" AND kind="10"');
    $get_disaster_Info->execute();

    //　日数管理テーブルより、「日数」「アップデート日付」を配列に格納
    foreach ($get_disaster_Info as $sql_col1) {
        // $_SESSION["text_yearly"] = $sql_col1['safety_goal'];
        $m_text_w = $sql_col1['safety_goal'];
    }
    //---- 交通事故
    $get_disaster_Info = $pdo->prepare('SELECT * FROM kg_goal_letter WHERE safety_kbn="20" AND kind="20"');
    $get_disaster_Info->execute();

    //　日数管理テーブルより、「日数」「アップデート日付」を配列に格納
    foreach ($get_disaster_Info as $sql_col1) {
        // $_SESSION["text_yearly"] = $sql_col1['safety_goal'];
        $m_text_t = $sql_col1['safety_goal'];
    }
    //---- 運転事故
    $get_disaster_Info = $pdo->prepare('SELECT * FROM kg_goal_letter WHERE safety_kbn="20" AND kind="30"');
    $get_disaster_Info->execute();

    //　日数管理テーブルより、「日数」「アップデート日付」を配列に格納
    foreach ($get_disaster_Info as $sql_col1) {
        // $_SESSION["text_yearly"] = $sql_col1['safety_goal'];
        $m_text_d = $sql_col1['safety_goal'];
    }

    //---------無災害記録の表示
    //---- 労働災害
    $get_disaster_Info = $pdo->prepare('SELECT * FROM kg_days WHERE safety_kbn="10"');
    $get_disaster_Info->execute();

    //　日数管理テーブルより、「日数」「アップデート日付」を配列に格納
    foreach ($get_disaster_Info as $sql_col1) {
        // $_SESSION["text_yearly"] = $sql_col1['safety_goal'];
        $d_text_w = $sql_col1['contents'];
        $days_w = $sql_col1['days'];
        $start_date_w = $sql_col1['start_date'];
    }
    //---- 交通事故
    $get_disaster_Info = $pdo->prepare('SELECT * FROM kg_days WHERE safety_kbn="20"');
    $get_disaster_Info->execute();

    //　日数管理テーブルより、「日数」「アップデート日付」を配列に格納
    foreach ($get_disaster_Info as $sql_col1) {
        // $_SESSION["text_yearly"] = $sql_col1['safety_goal'];
        $d_text_t = $sql_col1['contents'];
        $days_t = $sql_col1['days'];
        $start_date_t = $sql_col1['start_date'];
    }
    //---- 運転事故
    $get_disaster_Info = $pdo->prepare('SELECT * FROM kg_days WHERE safety_kbn="30"');
    $get_disaster_Info->execute();

    //　日数管理テーブルより、「日数」「アップデート日付」を配列に格納
    foreach ($get_disaster_Info as $sql_col1) {
        // $_SESSION["text_yearly"] = $sql_col1['safety_goal'];
        $d_text_d = $sql_col1['contents'];
        $days_d = $sql_col1['days'];
        $start_date_d = $sql_col1['start_date'];
    }

    //---------お知らせ表示
    $get_disaster_Info = $pdo->prepare('SELECT * FROM kg_goal_letter WHERE safety_kbn="30"');
    $get_disaster_Info->execute();

    //　日数管理テーブルより、「日数」「アップデート日付」を配列に格納
    foreach ($get_disaster_Info as $sql_col1) {
        // $_SESSION["text_yearly"] = $sql_col1['safety_goal'];
        $text_news = $sql_col1['safety_goal'];
    }
} catch (PDOException $e) {
    // エラー発生
    echo $e->getMessage();
}
?>

<!---- 登録 ---->
<!-- 年間目標 -->
<?php
if (isset($_POST["btn_yearly"])) {
    if (isset($_POST["text_yearly"])) {
        // DB接続
        try {
            $safety_goal_stmt = $pdo->prepare('INSERT INTO kg_goal_letter (safety_goal,safety_kbn,update_dt)' .
                'VALUES(:safety_goal,:safety_kbn,:update_dt)');

            $text_yearly = $_POST['text_yearly'];
            $update_dt = $today;

            $safety_goal_stmt->bindValue(':safety_goal', $text_yearly);
            $safety_goal_stmt->bindValue(':safety_kbn', 10);
            $safety_goal_stmt->bindValue(':update_dt', $update_dt);

            // SQL実行
            $safety_goal_stmt->execute();

            //再ロード時、多重書込み対策
            header("Location: index.php");
        } catch (PDOException $e) {
            // エラー発生
            echo $e->getMessage();
        } finally {
            // DB接続を閉じる
            $pdo = null;
        }
    }
}

//リロードによる再登録対策（書き込み後
// if ($_SERVER['REQUEST_METHOD'] == 'POST') {
//     header("Location:index.php");
//     exit;
// }
// echo $_SESSION["text_yearly"];
?>

<!-- 月間目標 -->
<?php
// 労働災害
if (isset($_POST["m_btn_w"])) {
    if (isset($_POST["m_text_w"])) {
        try {
            $safety_goal_stmt = $pdo->prepare('INSERT INTO kg_goal_letter (safety_goal,safety_kbn,update_dt,kind)' .
                'VALUES(:safety_goal,:safety_kbn,:update_dt,:kind)');

            $m_text_w = $_POST['m_text_w'];
            $update_dt = $today;

            $safety_goal_stmt->bindValue(':safety_goal', $m_text_w);
            $safety_goal_stmt->bindValue(':safety_kbn', 20);
            $safety_goal_stmt->bindValue(':update_dt', $update_dt);
            $safety_goal_stmt->bindValue(':kind', 10);

            // SQL実行
            $safety_goal_stmt->execute();

            //再ロード時、多重書込み対策
            header("Location: index.php");
        } catch (PDOException $e) {
            // エラー発生
            echo $e->getMessage();
        } finally {
            // DB接続を閉じる
            $pdo = null;
        }
    }
}
// 交通事故
if (isset($_POST["m_btn_t"])) {
    if (isset($_POST["m_text_t"])) {
        try {
            $safety_goal_stmt = $pdo->prepare('INSERT INTO kg_goal_letter (safety_goal,safety_kbn,update_dt,kind)' .
                'VALUES(:safety_goal,:safety_kbn,:update_dt,:kind)');

            $m_text_t = $_POST['m_text_t'];
            $update_dt = $today;

            $safety_goal_stmt->bindValue(':safety_goal', $m_text_t);
            $safety_goal_stmt->bindValue(':safety_kbn', 20);
            $safety_goal_stmt->bindValue(':update_dt', $update_dt);
            $safety_goal_stmt->bindValue(':kind', 20);

            // SQL実行
            $safety_goal_stmt->execute();

            //再ロード時、多重書込み対策
            header("Location: index.php");
        } catch (PDOException $e) {
            // エラー発生
            echo $e->getMessage();
        } finally {
            // DB接続を閉じる
            $pdo = null;
        }
    }
}
// 運転事故
if (isset($_POST["m_btn_d"])) {
    if (isset($_POST["m_text_d"])) {
        try {
            $safety_goal_stmt = $pdo->prepare('INSERT INTO kg_goal_letter (safety_goal,safety_kbn,update_dt,kind)' .
                'VALUES(:safety_goal,:safety_kbn,:update_dt,:kind)');

            $m_text_d = $_POST['m_text_d'];
            $update_dt = $today;

            $safety_goal_stmt->bindValue(':safety_goal', $m_text_d);
            $safety_goal_stmt->bindValue(':safety_kbn', 20);
            $safety_goal_stmt->bindValue(':update_dt', $update_dt);
            $safety_goal_stmt->bindValue(':kind', 30);

            // SQL実行
            $safety_goal_stmt->execute();

            //再ロード時、多重書込み対策
            header("Location: index.php");
        } catch (PDOException $e) {
            // エラー発生
            echo $e->getMessage();
        } finally {
            // DB接続を閉じる
            $pdo = null;
        }
    }
}
?>

<!-- 無災害記録 -->
<?php
// 労働災害
if (isset($_POST["d_btn_w"])) {
    if (isset($_POST["d_text_w"])) {
        try {
            $contents_stmt = $pdo->prepare('INSERT INTO kg_days (contents,safety_kbn,start_date,update_dt,days)' .
                'VALUES(:contents,:safety_kbn,:start_date,:update_dt,:days)');

            // 内容
            $d_text_w = $_POST['d_text_w'];
            // 起算日
            $start_date_w = $_POST['start_date_w'];
            // 更新日付
            $update_dt = $today;
            // 連続日数
            // 起算日
            $str_days = new DateTime($start_date_w);
            // 本日日付
            $now_days = new Datetime($today);
            // 差分結果
            $days_W = $str_days->diff($now_days);

            $contents_stmt->bindValue(':contents', $d_text_w);
            $contents_stmt->bindValue(':safety_kbn', 10);
            $contents_stmt->bindValue(':start_date', $start_date_w);
            $contents_stmt->bindValue(':update_dt', $update_dt);
            $contents_stmt->bindValue(':days', $days_W->days);

            // SQL実行
            $contents_stmt->execute();

            //再ロード時、多重書込み対策
            header("Location: index.php");
        } catch (PDOException $e) {
            // エラー発生
            echo $e->getMessage();
        } finally {
            // DB接続を閉じる
            $pdo = null;
        }
    }
}
// 交通事故
if (isset($_POST["d_btn_t"])) {
    if (isset($_POST["d_text_t"])) {
        try {
            $contents_stmt = $pdo->prepare('INSERT INTO kg_days (contents,safety_kbn,start_date,update_dt,days)' .
                'VALUES(:contents,:safety_kbn,:start_date,:update_dt,:days)');

            // 内容
            $d_text_t = $_POST['d_text_t'];
            // 起算日
            $start_date_t = $_POST['start_date_t'];
            // 更新日付
            $update_dt = $today;
            // 連続日数
            // 起算日
            $str_days = new DateTime($start_date_t);
            // 本日日付
            $now_days = new Datetime($today);
            // 差分結果
            $days_t = $str_days->diff($now_days);

            $contents_stmt->bindValue(':contents', $d_text_t);
            $contents_stmt->bindValue(':safety_kbn', 20);
            $contents_stmt->bindValue(':start_date', $start_date_t);
            $contents_stmt->bindValue(':update_dt', $update_dt);
            $contents_stmt->bindValue(':days', $days_t->days);

            // SQL実行
            $contents_stmt->execute();

            //再ロード時、多重書込み対策
            header("Location: index.php");
        } catch (PDOException $e) {
            // エラー発生
            echo $e->getMessage();
        } finally {
            // DB接続を閉じる
            $pdo = null;
        }
    }
}
// 運転事故
if (isset($_POST["d_btn_d"])) {
    if (isset($_POST["d_text_d"])) {
        try {
            $contents_stmt = $pdo->prepare('INSERT INTO kg_days (contents,safety_kbn,start_date,update_dt,days)' .
                'VALUES(:contents,:safety_kbn,:start_date,:update_dt,:days)');

            // 内容
            $d_text_d = $_POST['d_text_d'];
            // 起算日
            $start_date_d = $_POST['start_date_d'];
            // 更新日付
            $update_dt = $today;
            // 連続日数
            // 起算日
            $str_days = new DateTime($start_date_d);
            // 本日日付
            $now_days = new Datetime($today);
            // 差分結果
            $days_d = $str_days->diff($now_days);

            $contents_stmt->bindValue(':contents', $d_text_d);
            $contents_stmt->bindValue(':safety_kbn', 30);
            $contents_stmt->bindValue(':start_date', $start_date_d);
            $contents_stmt->bindValue(':update_dt', $update_dt);
            $contents_stmt->bindValue(':days', $days_d->days);

            // SQL実行
            $contents_stmt->execute();

            //再ロード時、多重書込み対策
            header("Location: index.php");
        } catch (PDOException $e) {
            // エラー発生
            echo $e->getMessage();
        } finally {
            // DB接続を閉じる
            $pdo = null;
        }
    }
}
?>

<!-- お知らせ -->
<?php
if (isset($_POST["btn_news"])) {
    if (isset($_POST["text_news"])) {
        try {
            $safety_goal_stmt = $pdo->prepare('INSERT INTO kg_goal_letter (safety_goal,safety_kbn,update_dt)' .
                'VALUES(:safety_goal,:safety_kbn,:update_dt)');

            $text_news = $_POST['text_news'];
            $update_dt = $today;

            $safety_goal_stmt->bindValue(':safety_goal', $text_news);
            $safety_goal_stmt->bindValue(':safety_kbn', 30);
            $safety_goal_stmt->bindValue(':update_dt', $update_dt);

            // SQL実行
            $safety_goal_stmt->execute();

            //再ロード時、多重書込み対策
            header("Location: index.php");
        } catch (PDOException $e) {
            // エラー発生
            echo $e->getMessage();
        } finally {
            // DB接続を閉じる
            $pdo = null;
        }
    }
}
?>

<!------------------HTML開始------------------>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <title>安全掲示板</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Noto+Sans" rel="stylesheet">
    <link rel="stylesheet" href="/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</head>

<body>
    <!-- <div class="top_div"></div> -->
    <div class="all_body">
        <div class="h_title">
            <h1>
                <img src="/img/image035.png" alt="イメージ画像">
            </h1>
        </div>
        <form method="POST" action="<?php print($_SERVER['PHP_SELF']) ?>">
            <!-- 全部のテーブル -->
            <div class="t_grop">
                <!-- 年間目標 -->
                <table class="yearly_table" border=1>
                    <thead>
                        <tr>
                            <th class="table_tlt">●年間目標</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <textarea name="text_yearly" id=""><?= $text_yearly ?></textarea>
                            </td>
                        </tr>
                        <!-- ボタン -->
                        <tr class="table_btn">
                            <td colspan="4">
                                <input type="submit" name="btn_yearly" value="登録">
                            </td>
                        </tr>
                    </tbody>
                </table>
                <!-- 月間目標 -->
                <table class="monthly_table" border=1>
                    <thead>
                        <tr>
                            <th class="table_tlt" colspan="3">●月間目標</th>
                        </tr>
                        <tr>
                            <th class="blue_color">
                                <span class="tlt_back"><span class="small_text">▼</span>労働災害</span>
                            </th>
                            <th class="red_color">
                                <span class="tlt_back"><span class="small_text">▼</span>交通事故</span>
                            </th>
                            <th class="yellow_color">
                                <span class="tlt_back"><span class="small_text">▼</span>運転事故</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <textarea name="m_text_w" id=""><?= $m_text_w ?></textarea>
                            </td>
                            <td>
                                <textarea name="m_text_t" id=""><?= $m_text_t ?></textarea>
                            </td>
                            <td>
                                <textarea name="m_text_d" id=""><?= $m_text_d ?></textarea>
                            </td>
                        </tr>
                        <!-- ボタン -->
                        <tr class="table_btn">
                            <td>
                                <input type="submit" name="m_btn_w" value="登録">
                            </td>
                            <td>
                                <input type="submit" name="m_btn_t" value="登録">
                            </td>
                            <td>
                                <input type="submit" name="m_btn_d" value="登録">
                            </td>
                        </tr>
                    </tbody>
                </table>
                <!-- 無災害記録 -->
                <table class="days_table" border=1>
                    <thead>
                        <tr>
                            <th colspan="4" class="table_tlt">●無災害記録</th>
                        </tr>
                        <tr>
                            <th>災害項目</th>
                            <th>起算日</th>
                            <th>内容</th>
                            <th>連続日数</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- 労働災害 -->
                        <tr class="w_disaster">
                            <th class="disaster_item blue_color" scope="row">
                                <span class="tlt_back">労働災害</span>
                            </th>
                            <td class="btncol">
                                <?php echo '<input name="start_date_w" type="date" min=' . $two_day_ago . ' max=' . $today . ' value=' . $start_date_w . ' class="calendar_box">'; ?>
                                <input type="submit" name="d_btn_w" value="登録" class="register_btn">
                            </td>
                            <td class="str_day">
                                <textarea name="d_text_w" id=""><?= $d_text_w ?></textarea>
                            </td>
                            <td class="continue_days">
                                <input type="text" name="continue_days1" size="6" value="<?= $days_w ?>" readonly="readonly">
                                <p>日</p>
                            </td>
                        </tr>
                        <!-- 交通災害 -->
                        <tr class="t_disaster">
                            <th class="disaster_item red_color" scope="row">
                                <span class="tlt_back">交通事故</span>
                            </th>
                            <td class="btncol">
                                <?php echo '<input name="start_date_t" type="date" min=' . $two_day_ago . ' max=' . $today . ' value=' . $start_date_t . ' class="calendar_box">'; ?>
                                <input type="submit" name="d_btn_t" value="登録" class="register_btn">
                            </td>
                            <td class="str_day">
                                <textarea name="d_text_t" id=""><?= $d_text_t ?></textarea>
                            </td>
                            <td class="continue_days">
                                <input type="text" name="continue_days2" size="6" value="<?= $days_t ?>" readonly="readonly">
                                <p>日</p>
                            </td>
                        </tr>
                        <!-- 運転事故 -->
                        <tr class="d_disaster">
                            <th class="disaster_item yellow_color" scope="row">
                                <span class="tlt_back">運転事故</span>
                            </th>
                            <td class="btncol">
                                <?php echo '<input name="start_date_d" type="date" min=' . $two_day_ago . ' max=' . $today . ' value=' . $start_date_d . ' class="calendar_box">'; ?>
                                <input type="submit" name="d_btn_d" value="登録" class="register_btn">
                            </td>
                            <td class="str_day">
                                <textarea name="d_text_d" id=""><?= $d_text_d ?></textarea>
                            </td>
                            <td class="continue_days">
                                <input type="text" name="continue_days3" size="6" value="<?= $days_d ?>" readonly="readonly">
                                <p>日</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <!-- お知らせ -->
                <table class="news_table" border=1>
                    <thead>
                        <tr>
                            <th class="table_tlt">●お知らせ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <textarea name="text_news" id=""><?= $text_news ?></textarea>
                            </td>
                        </tr>
                        <!-- ボタン -->
                        <tr class="table_btn">
                            <td colspan="4">
                                <input type="submit" name="btn_news" value="登録">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!--
                <div class="b_title">
                    <img src="/img/moth_goal.png" alt="イメージ画像" style=height:73px;width:400px;><br>
                    <textarea name="contents" rows="8" cols="40" placeholder="110字以内でお願いします。" maxlength="110"></textarea><br>
                    <input type="submit" name="btn4" value="投稿する"><br><br>
                </div>
                -->
        </form>
    </div>
    <div class="logo_image">
        <img src="/img/nishimura_logo.png" alt="">
        <img src="/img/nishimura_name.png" alt="">
    </div>
</body>

</html>
