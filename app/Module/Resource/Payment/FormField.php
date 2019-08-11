<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-5
 * Time: 下午3:53
 */

namespace App\Module\Resource\Payment;


class FormField
{
    public $displayName;
    public $placeholder;
    public $type;
    public $options;

    public function __construct($displayName, $placeholder = "", $type = "text", $options = [])
    {
        $this->displayName = $displayName;
        $this->placeholder = $placeholder;

        $this->type = $type;
        $this->options = $options;
    }

    public static function create($displayName, ... $arguments)
    {
        return new static($displayName, ... $arguments);
    }

    public function withPlaceholder($placeholder)
    {
        $this->placeholder = $placeholder;
        return $this;
    }

    public function withType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function withOptions($options)
    {
        $this->options = $options;
        return $this;
    }
}