#laravel-soft-delete-custom

这是一个 laravel Eloquent 软删除的扩展组件
可以根据业务自定义软删除字段和删除值


## Installation

To install, use composer:

```php
composer require zw/laravel-soft-delete-custom
```


## 在model中使用
```php
<?php

namespace App\Models\Test;

use App\Models\BaseModel;
use ZW\Laravel\Eloquent\Custom\SoftDeletes;//注意 不用引用错

class TestModel extends BaseModel
{
    use SoftDeletes;//使用软删除trait
    
    protected $table = 'test';

    const DELETED = 'is_deleted';//软删除字段 不设置默认为is_deleted
    const DELETED_VALUE = 1;//软删除值 不设置默认为1
    const UN_DELETED_VALUE = 0;//未删除值 不设置默认为o
    
    
    //如果是比较发杂的删除值 可以在模型中覆盖 trait中分方法getDeletedValue()、getUnDeletedValue()
//    public function getDeletedValue()
//    {
//        return time();
//    }    


}
```


Eloquent 软删除用法https://laravel.com/docs/8.x/eloquent#soft-deleting
已在lumen7.X 8.X 中使用过






