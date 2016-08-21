<?php

namespace Sender\Command;

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
    ;
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $base = $this->readerCsv('base/base_teste.csv');
    $this->senderMailgun($base, 'index.html', $output);
  }
}
