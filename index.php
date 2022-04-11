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

// 年間目標
$text_yearly = "";
// お知らせ
$text_news = "";


//　各安全項目の日数の更新および取得
try {
    // 各災害連続日数データ格納配列
    $disaster_array = [];

    // 労働災害用
    $w_disaster_days = 0;
    $w_disaster_Ymd = "";

    // 交通事故用
    $t_disaster_days = 0;
    $t_disaster_Ymd = "";

    // 運転事故用
    $d_disaster_days = 0;
    $d_disaster_Ymd = "";

    //　カウント用
    $s = 0;

    // DB接続
    $pdo = new PDO(
        /*dbname=DB名*/
        /*ユーザー名*/
        /*パスワード*/
        'mysql:dbname=boardsys;host=localhost;charset=utf8',
        'root',
        'shinei4005',

        // レコード列名をキーとして取得させる
        /*カラム名のみ取得*/
        [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );

    //　安全管理区分順に格納される(10：労働災害、20：交通事故、30：運転災害)
    $get_disaster_Info = $pdo->prepare('SELECT * FROM kg_days WHERE 1 ORDER BY safety_kbn ASC');
    $get_disaster_Info->execute();

    //　日数管理テーブルより、「日数」「アップデート日付」を配列に格納
    foreach ($get_disaster_Info as $sql_col1) {
        $disaster_array[$s] = [$sql_col1['days'], $sql_col1['update_dt']];
        //　TODO　日数更新処理

        //　TODO　本日日付より、未来の更新日データがある場合

        //　TODO　本日日付より、未来の更新日データがない場合

        $s += 1;
    }

    //　月間安全目標手紙より、月間安全目標を取得
    $get_goal_letter = $pdo->prepare('SELECT * FROM kg_goal_letter WHERE 1');
    $get_goal_letter->execute();

    //年間目標表示
    $get_disaster_Info = $pdo->prepare('SELECT * FROM kg_goal_letter WHERE safety_kbn="10"');
    $get_disaster_Info->execute();

    //　日数管理テーブルより、「日数」「アップデート日付」を配列に格納
    foreach ($get_disaster_Info as $sql_col1) {
        // $_SESSION["text_yearly"] = $sql_col1['safety_goal'];
        $text_yearly = $sql_col1['safety_goal'];
    }

    //お知らせ表示
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
} finally {
    // DB接続を閉じる
    $pdo = null;
}
?>

<?php
//　各ボタンクリックファンクションを作成

//　TODO　労働災害リセット

//　TODO　交通災害リセット

//　TODO　運転事故リセット

//　TODO　投稿
?>

<!-- 年間目標 -->
<?php
if (isset($_POST["btn_yearly"])) {
    if (isset($_POST["text_yearly"])) {
        // DB接続
        try {
            $pdo = new PDO(
                /*dbname=DB名*/
                /*ユーザー名*/
                /*パスワード*/
                'mysql:dbname=boardsys;host=localhost;charset=utf8',
                'root',
                'shinei4005',

                // レコード列名をキーとして取得させる
                /*カラム名のみ取得*/
                [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
            );

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

<!-- お知らせ -->
<?php
if (isset($_POST["btn_news"])) {
    if (isset($_POST["text_news"])) {
        // DB接続
        try {
            $pdo = new PDO(
                /*dbname=DB名*/
                /*ユーザー名*/
                /*パスワード*/
                'mysql:dbname=boardsys;host=localhost;charset=utf8',
                'root',
                'shinei4005',

                // レコード列名をキーとして取得させる
                /*カラム名のみ取得*/
                [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
            );

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

//リロードによる再登録対策（書き込み後
// if ($_SERVER['REQUEST_METHOD'] == 'POST') {
//     header("Location:index.php");
//     exit;
// }
// echo $_SESSION["text_yearly"];
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
                <!-- 年間目標と月間目標とお知らせ -->
                <div class="three_grop">
                    <!-- 年間目標と月間目標 -->
                    <div class="two_grop">
                        <table class="yearly_table" border=1>
                            <thead>
                                <tr>
                                    <th class="table_tlt">●年間目標</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <textarea name="text_yearly" id="" maxlength="60"><?= $text_yearly ?></textarea>
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
                                        <textarea name="月間目標（労働災害）" id="" maxlength="30">40文字以内</textarea>
                                    </td>
                                    <td>
                                        <textarea name="月間目標（交通事故）" id="">40文字以内</textarea>
                                    </td>
                                    <td>
                                        <textarea name="月間目標（運転事故）" id="">40文字以内</textarea>
                                    </td>
                                </tr>
                                <!-- ボタン -->
                                <tr class="table_btn">
                                    <td>
                                        <input type="submit" name="btn1" value="登録">
                                    </td>
                                    <td>
                                        <input type="submit" name="btn1" value="登録">
                                    </td>
                                    <td>
                                        <input type="submit" name="btn1" value="登録">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <table class="news_table" border=1>
                        <thead>
                            <tr>
                                <th class="table_tlt">●お知らせ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="position:relative;">
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
                                <?php echo '<input type="date" min=' . $two_day_ago . ' max=' . $today . ' value=' . $today . ' class="calendar_box">'; ?>
                                <input type="submit" name="btn1" value="登録" class="register_btn">
                            </td>
                            <td class="str_day">
                                <textarea name="労働災害の内容" id="">50文字以内</textarea>
                            </td>
                            <td class="continue_days">
                                <input type="text" name="continue_days1" size="6" value="100053" readonly="readonly">
                                <p>日</p>
                            </td>
                        </tr>
                        <!-- 交通災害 -->
                        <tr class="t_disaster">
                            <th class="disaster_item red_color" scope="row">
                                <span class="tlt_back">交通事故</span>
                            </th>
                            <td class="btncol">
                                <?php echo '<input type="date" min=' . $two_day_ago . ' max=' . $today . ' value=' . $today . ' class="calendar_box">'; ?>
                                <input type="submit" name="btn1" value="登録" class="register_btn">
                            </td>
                            <td class="str_day">
                                <textarea name="労働災害の内容" id="">50文字以内</textarea>
                            </td>
                            <td class="continue_days">
                                <input type="text" name="continue_days2" value="44" readonly="readonly">
                                <p>日</p>
                            </td>
                        </tr>
                        <!-- 運転事故 -->
                        <tr class="d_disaster">
                            <th class="disaster_item yellow_color" scope="row">
                                <span class="tlt_back">運転事故</span>
                            </th>
                            <td class="btncol">
                                <?php echo '<input type="date" min=' . $two_day_ago . ' max=' . $today . ' value=' . $today . ' class="calendar_box">'; ?>
                                <input type="submit" name="btn1" value="登録" class="register_btn">
                            </td>
                            <td class="str_day">
                                <textarea name="労働災害の内容" id="">50文字以内</textarea>
                            </td>
                            <td class="continue_days">
                                <input type="text" name="continue_days3" value="1" readonly="readonly">
                                <p>日</p>
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
        <a href="/index02.php" style="font-size:18px; color:red;">縦型はこちら</a>
    </div>
    <div class="logo_image">
        <img src="/img/nishimura_logo.png" alt="">
        <img src="/img/nishimura_name.png" alt="">
    </div>
</body>

</html>