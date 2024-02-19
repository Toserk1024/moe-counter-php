<?php
//引入配置和函数文件
require('config.php');
require('functions.php');

//配置与数据检测
$json_data = check_config($length, $data_file);
$theme = check_data($id, $theme, $default_theme, $id_min, $id_max);
//计数与显示数据处理
$all_data = tool_data(json_decode($json_data, true), $id, $length, $data_file);
$display_data = tool_display_data($all_data[$id], $length);
//图像坐标计算及生成
$x_data = image_x($theme, $length);
$y = image_y($theme);
$image = image_content($x_data, $y, $length, $theme, $theme_path, $display_data);
echo $image;
?>