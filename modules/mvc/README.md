### simple-mvc
* Use individually.
```javascript
require __DIR__.'/modules/import/autoload.php';
require __DIR__.'/modules/mvc/autoload.php';

//set the controller dir.
//set the model dir.
MVC::create('controller','model')();

//load './controller/UserController.php';
//new controller\UserController()->getInfo('id');
$value = MVC::c('user','getInfo','id');

```
* Use it as the middleWare.
app.php
```javascript
require __DIR__.'/modules/import/autoload.php';
require __DIR__.'/modules/mvc/autoload.php';

$router = Application:router();

//set the controller dir.
//set the model dir.
$router->on(MVC::create('controller','model'));

```
controller/UserController.php
```javascript
use mvc\MVC_Controller;

class UserController extends MVC_Controller{
    public function getInfo($value = null){
        return $this->model('getInfo',$value);
    }
}

```
model/UserModel.php
```javascript
use mvc\MVC_Model;

class UserModel extends MVC_Model{
    public function getInfo($value = null){
        return 'getInfo';
    }
}

```