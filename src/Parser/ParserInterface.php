<?php

namespace Macavity\TypeScriptGeneratorBundle\Parser;

class ParserInterface
{
    /** @var string */
    public $name;
    /** @var Property_[] */
    public $properties = [];

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function __toString()
    {
        $result = "interface {$this->name} {\n";
        $result .= implode(",\n", array_map(function ($p) { return "  " . (string)$p;}, $this->properties));
        $result .= "\n}";
        $result .= "\n";
        $result .= "declare var {$this->name}: {$this->name};\n";
        return $result;
    }
}