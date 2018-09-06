### 部署教程

#### 使用 Phantomjs 将 html 转图片

 - 安装 Phantomjs
```angular2html
# wget https://bitbucket.org/ariya/phantomjs/downloads/phantomjs-2.1.1-linux-x86_64.tar.bz2
# tar -jxf phantomjs-2.1.1-linux-x86_64.tar.bz2
# cp phantomjs-2.1.1-linux-x86_64/bin/phantomjs /usr/local/bin/

# phantomjs /home/wwwroot/laravel/public/js/html2Image.js 7777


```

 - 解决中文乱码
 
 ```angular2html
yum install bitmap-fonts bitmap-fonts-cjk fontconfig ttmkfdir
mkdir -p /usr/share/fonts/chinese
cd /usr/share/fonts/chinese
cp /home/wwwroot/laravel/public/fonts/pf.ttf /usr/share/fonts/chinese
chmod -R 755 /usr/share/fonts/chinese
ttmkfdir -e /usr/share/X11/fonts/encodings/encodings.dir

vi /etc/fonts/fonts.conf

--------
<!-- Font directory list -->

        <dir>/usr/share/fonts</dir>
        <dir>/usr/share/X11/fonts/Type1</dir> <dir>/usr/share/X11/fonts/TTF</dir> <dir>/usr/local/share/fonts</dir>
        <dir>/usr/share/fonts/chinese</dir>
        <dir prefix="xdg">fonts</dir>
        <!-- the following element will be removed in the future -->
        <dir>~/.fonts</dir>

<!--
--------

fc-cache
fc-list

```
