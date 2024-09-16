<?php
/*配置区域*/
$length = 6; //设定计数器显示位数
$id_min = 5; //计数id的最短长度
$id_max = 13; //计数id的最大长度
$id_rule = '/^[a-zA-Z0-9]+$/'; //计数id允许的表达式
$theme_path = 'theme'; //主题图像路径
$default_theme = 'rule34'; //未指定时的默认主题
$image_type = 'webp'; //需要输出的图片类型
$db = array(
    'type' => 'sqlite', //储存方式(json, mysql, sqlite)
    'json' => __DIR__ . '/data.json', //json文件储存路径
    'sqlite' => __DIR__ . '/counter.db', //sqlite数据库储存路径
    'mysql' => array(
        'server' => 'localhost', //默认不用改
        'db_name' => '数据库名称',
        'user_name' => '用户名',
        'password' => '用户密码'
    )
);

/*-------以下为代码执行部分，不需要自行修改-------*/

//输入数据获取
$id = @$_REQUEST['id'];
$theme = (!empty($_REQUEST['theme'])) ?
$_REQUEST['theme'] : $default_theme;

//主题分类处理
$theme_big = ['gelbooru'];
$theme_small = ['asoul', 'moebooru', 'rule34'];
$theme_all = ['asoul', 'moebooru', 'rule34', 'gelbooru'];
$db_types = ['json', 'sqlite', 'mysql']; //有效的储存方式

//数据合法性判断
$error_if = array(
    ($id == ''),
    ($length <= '1'),
    (!in_array($db['type'], $db_types)),
    @(!preg_match($id_rule, $id)),
    @($id_min > strlen($id) || strlen($id) > $id_max),
    @(!in_array($theme, $theme_all))
);

$error_tips = array(
    '请输入统计id',
    '计数器配置的位数不能小于2',
    "该存储方式无效: {$db['type']}",
    '非法的计数id，默认只允许包括数字及字母',
    "对不起，您当前id在长度限制之外: {$id_min}~{$id_max}",
    "请输入有效的主题 当前值: {$theme}"
); 

$i= 0;
while($i +1 < count($error_if)) {
    if ($error_if[$i]) {
        echo $error_tips[$i];
        exit;
    }
    $i++;
}


if ($db['type'] == 'json') {
    //读取或创建文件
    if (file_exists($db['json'])) {
        $data = json_decode(file_get_contents($db['json']), 1);
    } else {
        file_put_contents($db['json'], '');
    }
    
    //数据处理
    if (!isset($data[$id]) || $data[$id] >= str_repeat('9', $length)) {
        $data[$id] = 0;
    }
    $count = ++$data[$id];
    file_put_contents($db['json'], json_encode($data));
} elseif ($db['type'] == 'sqlite') {
    $db = new SQLite3($db['sqlite']);
    
    //数据获取
    $db-> exec("CREATE TABLE IF NOT EXISTS counter (
        id TEXT PRIMARY KEY,
        count INTEGER
    )");
    $get = $db-> prepare("SELECT count FROM counter WHERE id = :id");
    $get-> bindValue(':id', $id, SQLITE3_TEXT);
    $data = $get-> execute() -> fetchArray(SQLITE3_ASSOC);
    
    //数据处理
    if ($data === false || @$data['count'] >= str_repeat('9', $length)) {
        $count = 0;
    } else {
         $count = $data['count'];
    }
    $count++;
    
    //数据写入
    $put = $db-> prepare("INSERT INTO counter (id, count) VALUES (:id, :count)
        ON CONFLICT(id) DO UPDATE SET count = EXCLUDED.count"
    );
    $put-> bindValue(':id', $id, SQLITE3_TEXT);
    $put-> bindValue(':count', $count, SQLITE3_INTEGER);
    $put-> execute();
  
    $db-> close(); //释放资源
} elseif ($db['type'] == 'mysql') {
    $db = $db['mysql'];
    $db = new mysqli($db['server'], $db['user_name'], $db['password'], $db['db_name']);
    if ($db-> connect_error) {
         echo '数据库连接失败，请检查配置是否正确';
    }
    //创建数据表
    $db-> query("CREATE TABLE IF NOT EXISTS toserk_counter (
         id VARCHAR(255) PRIMARY KEY,
         count INT
    )");
    
    //数据获取
    $get = $db-> prepare("SELECT count FROM toserk_counter WHERE id = ?");
    $get-> bind_param("s", $id);
    $get-> execute();
    $data = $get-> get_result() -> fetch_assoc();

    //数据处理
    if ($data === null || @$data['count'] >= str_repeat('9', $length)) {
        $count = 0;
    } else {
        $count = $data['count'];
    }
    $count++;
    
    //数据写入
    $put = $db->prepare("INSERT INTO toserk_counter (id, count) VALUES (?, ?) 
        ON DUPLICATE KEY UPDATE count = VALUES(count)"
    );
    $put->bind_param("si", $id, $count);
    $put->execute();

    //释放资源
    $get->close();
    $put->close();
    $db->close();
}
//显示数据计算
$display_data = str_split(str_pad($count, $length, '0', STR_PAD_LEFT));

//坐标计算
if (in_array($theme, $theme_small)) {
    $x_size = 45;
    $y = 100;
} elseif (in_array($theme, $theme_big)) {
    $x_size = 68;
    $y = 150;
}
$x = array();
for ($i=0; $i < $length+1; $i++) {
    $x[] = $i * $x_size;
}

//图像生成及输出
$i = 0; $image = ''; $theme_path = "{$theme_path}/{$theme}/";
while ($i < $length) {
    $image_path = $theme_path . $display_data[$i] . '.gif';
    $base_image = base64_encode(file_get_contents($image_path));
    $image_body = "<image x=\"{$x[$i]}\" y=\"0\" width=\"{$x['1']}\" height=\"{$y}\" xlink:href=\"data:image/gif;base64,{$base_image}\"/>\n";
    $image .= $image_body;
    $i++;
}
$image_head = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><svg width=\"{$x[$length]}\" height=\"{$y}\" version=\"1.1\" xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\"><g>\n";
$svg = $image_head . $image . '</g></svg>';

header('Cache-Control: no-cache');
if ($image_type == 'svg') {
    header('Content-Type: image/svg+xml');
    echo $svg;
} else {
    $image = new Imagick();
    $image-> readImageBlob($svg);
    $image-> setImageFormat($image_type);
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    //自动判断图像类型
    header('Content-Type: ' . $finfo->buffer($image));
    echo $image;
}
?>