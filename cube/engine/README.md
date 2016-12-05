### ViewEngine For the Response->render($engine,$name,$data);
* $res->render($name,$data=null);//$engine is \engine\RaintplEngine
* $res->angular($string);//use \engine\AngularEngine
```
* \engine\AngularEngine implements.
```javascript
class AngularEngine extends ViewEngine{
    public function render($name,$data=null){
          //add <script> angularObject $name file string.
    }
}
```
* \engine\RaintplEngine implements.
```javascript
class RaintplEngine extends ViewEngine{
    public function render($name,$data=null){
          //render the html by the raintpl
    }
}
```
* your engine.
class MyEngine extends ViewEngine{
    public function render($name,$data=null){
          //render by yourself
    }
}
