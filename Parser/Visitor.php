<?php

namespace Janit\TypeScriptGeneratorBundle\Parser;

use PhpParser;
use PhpParser\Node;

class Visitor extends PhpParser\NodeVisitorAbstract
{
    private $isActive = false;

    /** @var TypeScript/Interface_[] */
    private $output = [];

    /** @var TypeScript\Interface_ */
    private $currentInterface;

    public function enterNode(Node $node)
    {
        if ($node instanceof PhpParser\Node\Stmt\Class_) {

            /** @var PhpParser\Node\Stmt\Class_ $class */
            $class = $node;
            // If there is "@TypeScriptMe" in the class phpDoc, then ...
            if ($class->getDocComment() && strpos($class->getDocComment()->getText(), "@TypeScriptMe") !== false) {
                $this->isActive = true;
                $this->output[] = $this->currentInterface = new ParserInterface($class->name);
            }
        }

        if ($this->isActive) {
            if ($node instanceof PhpParser\Node\Stmt\Property) {
                /** @var PhpParser\Node\Stmt\Property $property */
                $property = $node;

                if ($property->isPublic()) {
                    $type = $this->parsePhpDocForProperty($property->getDocComment());
                    $this->currentInterface->properties[] = new Property($property->props[0]->name, $type);
                }
            }
        }
    }

    public function leaveNode(Node $node)
    {
        if ($node instanceof PhpParser\Node\Stmt\Class_) {
            $this->isActive = false;
        }
    }

    /**
     * @param \PhpParser\Comment|null $phpDoc
     */
    private function parsePhpDocForProperty($phpDoc)
    {
        $result = "any";

        if ($phpDoc !== null) {
            if (preg_match('/@var[ \t]+([a-z0-9]+)/i', $phpDoc->getText(), $matches)) {
                $t = trim(strtolower($matches[1]));

                if ($t === "int") {
                    $result = "number";
                }
                elseif ($t === "string") {
                    $result = "string";
                }
            }
        }

        return $result;
    }

    public function getOutput()
    {
        return implode("\n\n", array_map(function ($i) { return (string)$i;}, $this->output));
    }
}
