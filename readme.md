### 一.关于本项目
本项目基于[Moe-Counter](https://github.com/journey-ad/Moe-Counter)进行重构<br>
支持**JSON/SQLite及MySQL**的储存方式<br><br>
TO DO List：
- [x] ~~1. 多方式储存数据(sqlite, mysql)~~**Done**
- [ ] 2. 制作一个默认的Demo主页
### 二.使用方法
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
    <td>asoul / moebooru / rule34 / gelbooru</td>
  </tr>
</table>

1. 例子：index.php?id=123456&theme=rule34
2. 您可以使用nginx或apache进行重写url
3. 部署时请注意将themes文件夹一同下载下来，主题路径可以在配置里修改

### 三.注意事项
1. 使用前请修改文件中的数据库配置，并合理使用防火墙保护您的数据文件<br>
2. 保证您的安全，计数ID默认只允许使用字母+数字，您可自行修改配置中的正则表达式<br>
3. 计数器位数应避免设置过小，当计数值大于当前位数所允许的最大值时，计数值将自动归零，保证程序正常运行。