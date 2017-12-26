<?php
namespace App\Commands;

use App\Exceptions\FileException;
use Menshov\BracketMatch;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class CheckBracketCommand extends Command
{
    private $filePath = '';

    protected function configure()
    {
        $this
            ->setName('check-brackets')
            ->setDescription('Проверить правильность расставленных скобок в файле');
        $this
            ->addArgument('path', InputArgument::REQUIRED, 'Путь к файлу');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $output->writeln('Путь к файлу: '.$input->getArgument('path'));

            $root = dirname(__DIR__);
            $this->filePath = $root . $input->getArgument('path');

            if(!is_file($this->filePath)){
                throw new FileException('Файл на сервере не найден либо путь задан неверно');
            }
            if(!is_readable($this->filePath)){
                throw new FileException('Файл нельзя прочесть');
            }

            $text = file_get_contents($this->filePath);
            $output->writeln([
                '',
                'Текст из файла:',
                $text,
                '',
            ]);

            if(!BracketMatch::checkBrackets($text)){
                $output->writeln("<error>Есть ошибки</error>");
            }else{
                $output->writeln('<info>Ошибок нет</info>');
            }

        } catch (\Exception $e) {
            $output->writeln("<error>{$e->getMessage()}</error>");
        }
    }
}