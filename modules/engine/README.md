### ViewEngine For the Response->render($engine,$name,$data);
* $res->render($engine,$name,$data=null);//$engine is \engine\ViewEngine
* \engine\EchoEngine implements.
```javascript
class EchoEngine extends ViewEngine{
    public function render($name,$data=null){
          //echo $name file string.
    }
}
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
