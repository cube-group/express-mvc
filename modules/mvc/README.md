### simple-mvc
* Use it as the middleWare.
app.php
```javascript
require __DIR__.'/modules/import/autoload.php';
require __DIR__.'/modules/mvc/autoload.php';

$app = App:app();

//set the controller dir.
//set the model dir.
$router->on(MVC::create([
    'controller_dir'=>'src/controller',
    'controller_prefix'=>'src\controller',
    'model_dir'=>'src/model',
    'model_prefix'=>'src\model'
]));

```
controller/UserController.php
```javascript
use mvc\MVC_Controller;

class UserController extends MVC_Controller{
    public function indexValue($value = null){
        return $this->model('index',$value);
    }
}

```
model/UserModel.php
```javascript
use mvc\MVC_Model;

class UserModel extends MVC_Model{
    public function indexProxy($value = null){
        return 'getInfo';
    }
}

```