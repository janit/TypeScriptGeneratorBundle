<?php

namespace Janit\TypeScriptGeneratorBundle\Command;

use Janit\TypeScriptGeneratorBundle\Parser\Visitor;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use PhpParser\ParserFactory;
use PhpParser\NodeTraverser;

class GenerateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('typescript:generate-interfaces')
            ->setDescription('Generate TypeScript interfaces from PHP classes in a directory')
            ->addArgument(
                'fromDir',
                InputArgument::REQUIRED,
                'The directory to scan for suitable classes'
            )
            ->addArgument(
                'toDir',
                InputArgument::OPTIONAL,
                'Where to export generated classes'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $projectDir = $this->getContainer()->get('kernel')->getProjectDir();
        $fromDir = $input->getArgument('fromDir');
        $toDir = $input->getArgument('toDir');

        if(!$toDir){
            $toDir = 'typescript';

        }

        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $traverser = new NodeTraverser();

        $fs = new Filesystem();
        $finder = new Finder();
        $finder->files('*.php')->in( $projectDir . '/' . $fromDir);

        foreach ($finder as $file) {

            $visitor = new Visitor();
            $traverser->addVisitor($visitor);
            $code = $file->getContents();

            try {

                $stmts = $parser->parse($code);
                $stmts = $traverser->traverse($stmts);

                if($visitor->getOutput()){
                    $targetFile = $toDir . '/' . str_replace( '.php','.d.ts', $file->getFilename());
                    $fs->dumpFile($targetFile,$visitor->getOutput());
                    $output->writeln('created interface ' . $targetFile);
                }

            } catch (\ParseError $e) {
                $output->writeln('Parse error: ' .$e->getMessage());
            }

        }

    }
}