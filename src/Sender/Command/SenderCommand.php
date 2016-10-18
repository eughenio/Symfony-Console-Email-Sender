<?php

namespace Sender\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class SenderCommand extends SenderCommandAbstract
{
  public function __construct()
  {
    parent::__construct();
  }

  protected function configure()
  {
    $this
      ->setName("sender:send")
      ->setDescription(
      <<<EOT
      Send your email to a base of emails
EOT
      )
      ->addOption(
        'base',
        'b',
        InputOption::VALUE_REQUIRED,
        'Path to CSV',
        ''
      )
      ->addOption(
        'template',
        't',
        InputOption::VALUE_REQUIRED,
        'Path to HTML',
        ''
      )
      ->addOption(
        'subject',
        'o',
        InputOption::VALUE_REQUIRED,
        'Email Subject',
        ''
      )
      ->addOption(
        'sender',
        's',
        InputOption::VALUE_OPTIONAL,
        'Which sender you will use? Swift or Mailgun',
        'Mailgun'
      )
    ;
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $base = $this->readerCsv($input->getOption('base'));
    $template = $input->getOption('template');
    $subject = $input->getOption('subject');

    switch ($input->getOption('sender')) {
      case 'Swift':
        $this->senderSwift($base, $template, $subject, $output);
        break;

      default:
        $this->senderMailgun($base, $template, $subject, $output);
        break;
    }
  }
}
