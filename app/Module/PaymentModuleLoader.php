<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-5
 * Time: 下午2:51
 */

namespace App\Module;


use App\Currency;
use App\Module\Contract\PaymentModule;
use App\Module\Exception\ModuleNotFoundException;
use App\Module\Resource\Payment\DatabaseLogger;

class PaymentModuleLoader implements \JsonSerializable
{
    const BASIC_NAME_SPACE = "App\\Module\\Payment\\";

    private $name;

    private $className;

    private $reflectionClass;

    /**
     * PaymentModuleLoader constructor.
     * @param $name
     * @throws ModuleNotFoundException
     */
    public function __construct($name)
    {
        $this->name = $name;
        $this->className = self::makeClassName($name);
        try {
            $reflectionClass = new \ReflectionClass($this->className);
            if (!$reflectionClass->implementsInterface(PaymentModule::class)) {
                throw new ModuleNotFoundException();
            }
            $this->reflectionClass = $reflectionClass;
        } catch (\ReflectionException $e) {
            throw new ModuleNotFoundException();
        }
    }

    public function getInternalName()
    {
        return $this->name;
    }

    public function getName()
    {
        return call_user_func([$this->className, "getName"]);
    }

    public function getVersion()
    {
        return call_user_func([$this->className, "getVersion"]);
    }

    public function getDescription()
    {
        return call_user_func([$this->className, "getDescription"]);
    }

    public function getAvailableSettings()
    {
        return call_user_func([$this->className, "getAvailableSettings"]);
    }

    public function getAvailableChannels()
    {
        return call_user_func([$this->className, "getAvailableChannels"]);
    }

    /**
     * @return \ReflectionClass
     */
    public function getReflectionClass(): \ReflectionClass
    {
        return $this->reflectionClass;
    }

    /**
     * @param \App\PaymentModule $paymentModule
     * @return PaymentModule
     */
    public function newInstance(\App\PaymentModule $paymentModule)
    {
        if ($paymentModule->currency) {
            $currencyCode = $paymentModule->currency->code;
        } else {
            $currencyCode = Currency::query()->findOrFail(1)->code;
        }

        return $this->reflectionClass->newInstance(new DatabaseLogger($paymentModule->id), $paymentModule->settings()->pluck("value", "name"), $currencyCode);
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            "internalName" => $this->getInternalName(),
            "name" => $this->getName(),
            "version" => $this->getVersion(),
            "description" => $this->getDescription(),
            "availableSettings" => $this->getAvailableSettings(),
            "availableChannels" => $this->getAvailableChannels(),
        ];
    }

    public static function scan()
    {
        $directories = scandir(__DIR__ . "/Payment/");
        array_shift($directories);
        array_shift($directories);

        /**
         * @var self[] $modules
         */
        $modules = [];

        foreach ($directories as $directory) {
            try {
                $modules[$directory] = new self($directory);
            } catch (ModuleNotFoundException $e) {
            }
        }

        return $modules;
    }

    public static function makeClassName($name)
    {
        return self::BASIC_NAME_SPACE . $name . "\\" . $name;
    }
}