### ext by imagick.so
* imagick.so go to the site <a href='http://pecl.php.net/pcakge/imagick' target='_blank'>pecl-imagick</a>
* you should setup the ImageMagick and ImageMagick-devel software.

### what is Image's functions?
* ppt parsed to pdf
```javascript
$pic_files = Image::ppt2pdf('a.ppt');//return array
```
* pdf parsed to pic
```javascript
$pic_files = Image::pdf2pic('a.pdf','./','png');//return array
```