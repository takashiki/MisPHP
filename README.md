# MisPHP (Kernel)

> **Note:** This repository contains the core code of the MisPHP framework. 
If you want to build an application using MisPHP, visit the main [MisPHP repository](https://github.com/takashiki/mis).

## MisPHP Framework

I worte MisPHP for the purpose of learning. At first, I refered to Slim, flight, b2core and other micro php frameworks. 
However, I was impressed by the elegance and strength of laravel. Now, MisPHP is mostly like laravel and some code is basicly based on laravel's.

MisPHP是我的一个练习作品，最初是参考Slim、flight、b2core等微框架来编写的，
但有感于laravel的强大和优美，又开始主要参考laravel来编写了，本框架部分代码直接在laravel代码基础上修改而成。

## Documentation

### Routing

anonymous function

```php
$app->route('/', function() {echo 'null';});
```

function

```php
function home() {
  echo 'welcome';
}
$app->route('/', 'home');
```

member function

```php
class Home
{
  public function index() {
    echo 'welcome';
  }
}
$app->route('/', 'Home->index');
```

static member function

```php
class Home
{
  public static function index() {
    echo 'welcome';
  }
}
$app->route('/', 'Home::index');
```

本框架支持pathinfo模式的默认路由，'/home/index'会被解析到'HomeController'的'index'方法。

### Model

init

```php
use mis\db\teck\Model;

class User extends Model
{

}
```

select

```php
User::get();
User::where('column_name', 'value')->get();
User::where('column_name >', 'value')->get();
User::where(array('column_name operator' => 'value', ...))->get();
```

insert

```php
User::insert(array('column_name' => 'value', ...));
User::insert(array(array('column_name' => 'value', ...), ...));
```

update

```php
User::where('column_name', 'value')->update(array('column_name' => 'value', ...));
```

delete

```php
User::delete('id_value');
User::where('column_name', 'value')->delete();
```

## Contributing

Issues and Pull Requests are welcome at any time.
非常欢迎大家对本框架提出宝贵的意见、寻找框架的问题或是直接参与到开发中来。

### License

The MisPHP framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
