<?php

namespace Macavity\TypeScriptGeneratorBundle\Parser;

class ParserInterface
{
    /** @var string */
    public $name;

    /** @var Property[] */
    public $properties = [];

    public function __construct($name)
    {
        // TODO Make prefix and suffix configurable
        $this->name = 'I'.$name;
    }

    public function __toString()
    {
        $interfaceBody = implode(",\n", array_map(function (Property $p) {
            return "  " . $p->__toString();
        }, $this->properties));

        $result = <<< HEREDOC
interface {$this->name} {
    {$interfaceBody}
}
HEREDOC;
        return $result;
    }
}