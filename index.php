<?php
ini_set('display_errors', 1);
$servername = "localhost";
$username = "******";
$password = "******";
$dbname = "******";

function GetRandStr($length){
    //字符组合
    $str = 'abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ023456789';
    $len = strlen($str)-1;
    $randstr = '';
    for ($i=0;$i<$length;$i++) {
        $num=mt_rand(0,$len);
        $randstr .= $str[$num];
    }
    return $randstr;
}

// 创建连接
$conn = new mysqli($servername, $username, $password, $dbname);
 
// 检测连接
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(empty($_POST["poster"]) or empty($_POST["postText"])) die("<script>alert('发送者和发送内容均不能为空！');history.go(-1);</script>");
    $expiry_flag = $_POST["expiry"];
    if (!preg_match('/^[dmwfy]$/', $expiry_flag))
		$expiry_flag='m';
	switch ($expiry_flag)
	{
		case 'd':
			$expires="DATE_ADD(NOW(), INTERVAL 1 DAY)";
			break;
		case 'w':
			$expires="DATE_ADD(NOW(), INTERVAL 7 DAY)";
			break;
		case 'f':
			$expires="NULL";
			break;
		case 'm':
			$expires="DATE_ADD(NOW(), INTERVAL 1 MONTH)";
			break;
		case 'y':
			$expires="DATE_ADD(NOW(), INTERVAL 1 YEAR)";
			break;
	}
    $stmt = $conn->prepare("INSERT INTO pastebin (poster, postText, code, posted, expires, expiry_flag) VALUES (?, ?, ?, now(), ".$expires.", ?)");
    if($stmt->bind_param('ssss', $poster, $postText, $code, $expire_flag) == FALSE){
        die("Error!");
    }
    
    // 设置参数并执行
    $poster = htmlentities($_POST["poster"]);
    $postText = htmlentities($_POST["postText"]);
    $code = GetRandStr(8);
    $expire_flag = $_POST["expiry"];
    if (!preg_match('/^[dmwfy]$/', $expire_flag))
		$expire_flag='m';
    $stmt->execute();
    header('content-type:text/html;charset=uft-8');
    header('Location: /'.$code);
}
else{
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="stylesheet" href="/pastebin.css" />
        <style>
          @media (max-width: 772px) {
            .p-navigation .p-navigation__row {
              padding-left: 0;
              padding-right: 0;
            }
          }
        </style>
        <title>Epis2048 Pastebin</title>
    </head>
    <body>
        <header id="navigation" class="p-navigation is-dark">
            <div class="p-navigation__row">
                <div class="p-navigation__banner" style="background-color: #095470;padding-left: 1rem;">
                    <div class="p-navigation__logo">
                        <a href="/" style="margin-left:10px;margin-top:12px;color:#fff">Epis2048 pastebin</a>
                    </div>
                </div>
            </div>
        </header>
            <?php
                if($_GET["id"] == ""){
            ?>
        <div class="p-strip--light">
            <div class="row">
                <h2>New paste</h2>
                <form action="/" method="POST" id="pasteform" name="pasteform" class="p-form">
                    <div class="row">
                        <div class="col-4">
                            <label for="poster">Poster:</label> <input type="text" name="poster" value="" required id="poster" maxlength="16" />
                            <p class="p-form-help-text">Your name (16 characters max)</p>
                        </div>
                        <div class="col-4">
                            <label for="expiry">Expiration:</label>
                            <select name="expiry" id="expiry">
                                <option value="f">Forever</option>
                                <option value="d">A day</option>
                                <option value="w" selected>A week</option>
                                <option value="m">A month</option>
                                <option value="y">A year</option>
                            </select>
                            <p class="p-form-help-text">Approximate and not guaranteed</p>
                        </div>
                        <div class="col-12">
                            <label for="postText">Content:</label>
                            <textarea name="postText" id="postText" rows="10" cols="80" required></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <input class="p-button--positive" type="submit" value="Submit">
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <hr/>
                <!--<form name="editor" method="post" action="/">
                    <input type="hidden" name="parent_pid" value=""/>
                    <label for="postText">在下面输入要保存的内容：</label>
                    <textarea id="postText" class="codeedit" name="postText" cols="80" rows="10" onkeydown="return onTextareaKey(this,event)"></textarea>
                    <div id="namebox">
                        <label for="poster">您的名字：</label><br/>
                        <input type="text" maxlength="24" size="24" id="poster" name="poster" value="" />
                        <input type="submit" name="paste" value="Send"/>
                    </div>
                    <div id="expirybox">
                        <div id="expiryradios">
                            <label>保存时间?</label><br/>
                            
                            <input type="radio" id="expiry_day" name="expiry" value="d"  />
                            <label id="expiry_day_label" for="expiry_day">a day</label>
                            
                            <input type="radio" id="expiry_month" name="expiry" value="m"  />
                            <label id="expiry_month_label" for="expiry_month">a month</label>
                            
                            <input type="radio" id="expiry_forever" name="expiry" value="f"  />
                            <label id="expiry_forever_label" for="expiry_forever">forever</label>
                        </div>
                        <div id="expiryinfo"></div>
                    </div>
                    <div id="email">
                        <input type="text" size="8" name="email" value="" />
                    </div>
                    <div id="end"></div>
                </form>-->
            <?php
                } else {
                    $stmt = $conn->prepare("SELECT poster, postText, date_format(DATE_ADD(posted, INTERVAL 8 HOUR), '%Y-%m-%d %H:%i:%s') as posted, date_format(DATE_ADD(expires, INTERVAL 8 HOUR), '%Y-%m-%d %H:%i:%s') as expires FROM pastebin WHERE code = ? and (expires >= now() or expiry_flag = 'f')");
                    $stmt->bind_param('s', $_GET["id"]);
                    $stmt->execute();
                    $res = $stmt->get_result();
                    if ($res->num_rows > 0) {
                        // 输出数据
                        while($row = $res->fetch_assoc()) {
            ?>
        <div class="p-strip">
            <div class="row">
    		</div>
            <div class="row">
                <h1 class="p-heading--three">Paste from: <?php echo $row["poster"] ?>, At: <?php echo $row["posted"] ?></h1>
            </div>
            <div class="row">
                <div class="col-8">
                    <p>This paste expires at <?php if($row["expires"] == ""){echo "None";} else{echo $row["expires"];} ?>.</p>
                </div>
                <div class="col-4 u-align--right">
                    <a class="p-button--neutral" href="/">New</a>
                    <button class="p-button--positive js-copy-button">Copy</button>
                </div>
                <div class="paste" style="font-size: 14px;">
                    <div class="highlight">
                        <pre> <?php echo $row["postText"] ?></pre>
                    </div>
                </div>
            </div>
            <div class="row">
                <h4 class="p-heading--three">Link: <a href="/<?php echo $_GET["id"] ?>">https://pastebin.epis2048.net/<?php echo $_GET["id"] ?></a></h4>
            </div>
        </div>
            <?}
                    } else {
            ?>
        <div class="p-strip">
            <div class="row">
    		</div>
            <div class="row">
                <h1 class="p-heading--three">404 - The paste you requested are not found or expired.</h1>
            </div>
        </div>            <?
                    }
                }
            ?>
        <footer class="p-strip is-shallow">
            <div class="row">
                <div class="col-12">
                    © 2021 <a href="https://www.epis2048.net/">Epis2048</a>
                    <nav>
                        <span class="u-off-screen">
                            <a href="#">Go to the top of the page</a>
                        </span>
                    </nav>
                </div>
            </div>
        </footer>
    </body>
</html>
<?php
}
?>
