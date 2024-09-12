<?php
/*配置区域*/
$length = 6; //设定计数器显示位数
$id_min = 5; //计数id的最短长度
$id_max = 13; //计数id的最大长度
$id_rule = '/^[a-zA-Z0-9]+$/' //计数id允许的表达式
$data_file = 'data.json'; //数据文件路径
$theme_path = 'theme'; //主题图像路径
$default_theme = 'rule34'; //未指定时的默认主题

/*-------以下为代码执行部分，不需要自行修改-------*/

//数据文件读取及自动创建
if (file_exists($data_file)) {
    $data = json_decode(file_get_contents($data_file), 1);
} else {
    file_put_contents($data_file,'');
}

//输入数据获取
$id = @$_REQUEST['id'];
$theme = (!empty($_REQUEST['theme'])) ?
$_REQUEST['theme'] : $default_theme;

//主题分类处理
$theme_big = ['gelbooru'];
$theme_small = ['asoul', 'moebooru', 'rule34'];
$theme_all = ['asoul', 'moebooru', 'rule34', 'gelbooru'];

//输入数据合法性判断
$error_if = array(
    ($id == ''),
    ($length <= '1'),
    @(!preg_match($id_rule, $id)),
    @($id_min > strlen($id) || strlen($id) > $id_max),
    @(!in_array($theme, $theme_all))
);

$error_tips = array(
    '请输入统计id',
    '计数器配置的位数不能小于2',
    '因安全原因，统计id目前只支持字母和数字',
    "对不起，您当前id在长度限制之外：{$id_min}~{$id_max}",
    "请输入有效的主题 当前值：{$theme}"
); 

$i= 0;
while($i +1 < count($error_if)) {
    if ($error_if[$i]) {
        echo $error_tips[$i];
        exit;
    }
    $i++;
}

//数据处理
if (!isset($data[$id]) || $data[$id] >= str_repeat('9', $length)) {
    $data[$id] = 0;
}
$data[$id]++;
file_put_contents($data_file, json_encode($data));
$display_data = str_split(str_pad($data[$id], $length, '0', STR_PAD_LEFT));

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
header('Content-Type: image/svg+xml');
$image_head = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><svg width=\"{$x[$length]}\" height=\"{$y}\" version=\"1.1\" xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\"><g>\n";
echo $image_head . $image . '</g></svg>';
?>