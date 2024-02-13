<?php
$length = 6; //设定计数器显示位数
$id_max = 10; //计数id的最大长度
$id_min = 3;//计数id的最短长度
$data_file = 'data.json'; //数据文件路径
$default_theme = 'rule34'; //未指定时的默认主题

$id = @$_REQUEST['id'];
$theme = @$_REQUEST['theme'];
require('functions.php');
$json_data = check_config($length, $data_file);
$theme = check_data($id, $theme, $default_theme, $id_min, $id_max);
$all_data = tool_data(json_decode($json_data, true), $id, $length, $data_file);
$display_data = tool_display_data($all_data[$id], $length);

$theme_path = "theme/{$theme}/"; //主题文件路径
$x_data = image_x($theme, $length);
$y = image_y($theme);
$image = image_content($x_data, $y, $length, $theme_path, $display_data);
echo $image;
?>