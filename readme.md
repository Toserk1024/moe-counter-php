### 关于本项目
本项目基于[Moe-Counter](https://github.com/journey-ad/Moe-Counter)进行重构<br>
采用<strong>PHP+JSON</strong>的方式运行

### 使用方法
<table>
  <tr>
    <th>参数</th>
    <th>值</th>
  </tr>
  <tr>
    <td>id</td>
    <td>自定义</td>
  </tr>
  <tr>
    <td>theme</td>
    <td>asoul / moebooru / moebooru-h / rule34 / gelbooru / gelbooru-h</td>
  </tr>
</table>
例子：index.php?id=123456&theme=rule34

### 注意事项
1.为保证数据安全，请在第1次运行前更改"index.php"第5行的数据文件的文件名，或使用防火墙保护数据文件。<br>
2.ID参数因避免特殊字符导致json结构破坏，只允许使用字母和数字。<br>
3.计数器位数应避免设置过小，当计数值大于当前位数所允许的最大值时，计数值将自动归零，保证程序正常运行。