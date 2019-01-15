<?php

namespace Macavity\TypeScriptGeneratorBundle\Parser;

class Property
{
    /** @var string */
    public $name;
    /** @var string */
    public $type;

    public function __construct($name, $type = "any")
    {
        $this->name = $name;
        $this->type = $type;
    }

    public function __toString()
    {
        return "{$this->name}: {$this->type}";
    }
}