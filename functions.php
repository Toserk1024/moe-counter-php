<?php
$theme_all = ['asoul', 'moebooru', 'moebooru-h', 'rule34', 'gelbooru', 'gelbooru-h'];
$theme_small = ['asoul', 'moebooru', 'moebooru-h', 'rule34'];
$theme_big = ['gelbooru', 'gelbooru-h'];

function check_config($length, $data_file) {
    if ($length <= '1') {
        echo "Error:计数器位数不能小于2<br>当前值：{$length}";
        exit;
    }
    if (file_exists($data_file)) {
        $data = file_get_contents($data_file);
        return $data;
    } else {
        $file = fopen($data_file, "w");
        if ($file == false) {
            echo "Error:无法创建数据文件：{$data_file}";
            exit;
        }
        fclose($file);
        echo "Done:数据文件创建成功 位置：./{$data_file}<br>为保证您的数据安全，请确保数据文件使用随机文件名或者使用防火墙保护";
        exit;
    }
}

function check_data($id, $theme, $default_theme) {
    global $theme_all;
    if($id == '') {
        echo '请输入统计id';
        exit;
    } elseif ($theme == '') {
        $theme = $default_theme;
    } elseif (!in_array($theme, $theme_all)) {
        echo "请输入有效的主题 当前值：{$theme}";
        exit;
    }
return $theme;
}

function tool_data($all_data, $id, $length, $file) {
    if (isset($all_data[$id])) {
        $i = 0; $full = '';
        while ($i < $length) {
            $full = $full . '9';
            $i++;
        }
        if ($all_data[$id] == (int)$full) {
            $all_data[$id] = 0;
        } 
    } else {
        $all_data[$id] = 0;
    }
$all_data[$id]++;
$json_data = json_encode($all_data);
file_put_contents($file, $json_data);
return $all_data;
}

function tool_display_data($data, $length) {
    $data = (string)$data;
    $data_length = strlen($data);
    while ($data_length < $length) {
        $data = '0' . $data;
        $data_length = strlen($data);
    }
$display_data = str_split($data);
return $display_data;
}

function x_count($x, $length) {
     $i = 0;
     $data = array();
     while ($i <= $length) {
         $x_data = $i*$x;
         $data[$i] = (string)$x_data;
         $i++;
     }
return $data;
}

function image_x($theme, $length) {
    global $theme_small;
    global $theme_big;
    if (in_array($theme, $theme_small)) {
        $data = x_count(45, $length);
    } elseif (in_array($theme, $theme_big)) {
        $data = x_count(68, $length);
    }
return $data;
}

function image_y($theme) {
    global $theme_small;
    global $theme_big;
    if (in_array($theme, $theme_small)) {
        $y = 100;
    } elseif (in_array($theme, $theme_big)) {
        $y = 150;
    }
return $y;
}

function image_content($x_data, $y, $length, $theme_path, $display_data) {
    $i = 0; $data = '';
    while ($i < $length) {
        $image_path = $theme_path . $display_data[$i] . '.gif';
        $base_image = base64_encode(file_get_contents($image_path));
        $image_data = "<image x=\"{$x_data[$i]}\" y=\"0\" width=\"{$x_data['1']}\" height=\"{$y}\" xlink:href=\"data:image/gif;base64,{$base_image}\"/>\n";
        $data = $data . $image_data;
        $i++;
    }
    $image_head = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><svg width=\"{$x_data[$length]}\" height=\"{$y}\" version=\"1.1\" xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\"><g>\n";
    $data = $image_head . $data;
return $data;
}
?>