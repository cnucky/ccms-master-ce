# 付款模块开发规范
## 结构
付款模块存放于app/Modules/Payment/，各模块文件存放于各自的目录中。

模块目录下需创建一个与目录同名的PHP文件，且于命名空间"App\Module\Payment\目录名"声明一个与目录同名的类，并实现接口App\Module\Contract\PaymentModule。

例如创建一个名为CCMS的付款模块：
```
mkdir app/Modules/Payment/CCMS
touch app/Modules/Payment/CCMS.php
```

app/Modules/Payment/CCMS.php中基本内容如下:
```
<?php
namespace App\Module\Payment\CCMS;

use App\Module\Contract\PaymentModule;

class Test implements PaymentModule
{
    // Your implements...
}
```

## 接口App\Module\Contract\PaymentModule的实现
### 注意事项
模块处理付款请求时，金额请使用App\PaymentTrade的fee属性，如非必要，请勿使用fee_in_default_currency属性。

货币之间的转换由系统自动完成，模块仅需负责使用所需的参数执行所需的工作。

系统生成的回调/返回URL：.../api/paymentModules/{paymentModuleId}/payNotify/{tradeNo}，虽带有订单号参数tradeNo，但此订单号仅为方便查看系统日志而添加，如非必要，实现模块时请勿使用回调或返回地址中的订单号参数，请根据付款平台的文档，使用文档中的方法获取订单号。
### 支付
用户发起付款请求时，系统将会生成订单(App\PaymentTrade的实例)，并生成异步回调，同步返回的地址，调用模块的pay()接口。

pay()接口可以返回数组，字符串或者App\Module\Resource\Payment\PayResponse对象。

返回数组，系统将会在客户端浏览器生成表单并立刻提交；返回字符串，系统将会作为一个URL让浏览器跳转；如果是其它的响应类型，如二维码，请通过PayResponse对象返回。

### 回调
当收到回调时，系统将会调用notify()接口，接口要求返回App\Module\Resource\Payment\PayNotifyResult的对象。

如果模块经验证，确定回调可信，请在PayNotifyResult对象中写入订单号(id)。

如果用户已支付，请为PayNotifyResult对象记录金额(fee)，并把result置为常量App\Module\Constants\Payment\ValidationResult::CORRECT_SIGNATURE，系统在接收到result为ValidationResult::CORRECT_SIGNATURE的PayNotifyResult对象后，将会通过订单号，对订单进行处理。注意：如果返回的金额于订单中记录的金额不一致，系统将不会进行支付成功的处理。

对于用户未支付，或者回调请求不可信，切勿把NotifyResult对象的result置为ValidationResult::CORRECT_SIGNATURE。
如果回调可信，但用户未支付，或无需系统执行任何操作，请置result为ValidationResult::CORRECT_SIGNATURE_AND_RETURN。

最后，请为view赋值，定义系统需要输出的数据。

### 返回
如果模块需要在浏览器访问返回URL时进行处理，请实现接口App\Module\Contract\PaymentModule\PayReturnable。

此接口请返回App\Module\Resource\Payment\PayReturnResult对象，其属性除view外，要求与App\Module\Resource\Payment\PayNotifyResult一致。view属性会被忽略。