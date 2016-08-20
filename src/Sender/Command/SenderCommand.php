<?php

namespace Sender\Command;

use Mailgun\Mailgun;
use League\Csv\Reader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SenderCommand extends Command
{
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
    $loader = new \Twig_Loader_Filesystem('template');
    $twig = new \Twig_Environment($loader);
    $template = $twig->loadTemplate('index.html');

    $csv = Reader::createFromPath('base.csv');
    $csv->setDelimiter(";");
    $headers = $csv->fetchOne();
    $results = $csv->setOffset(1)->fetchAssoc($headers);

    $mgClient = new Mailgun('key-0167669a231c925f22b420c05e90a552');

    foreach ($results as $row) {
      $result = $mgClient->sendMessage('mg.caseicom.vc', array(
        'from'    => 'Daniella e Eughenio <daniellaeeughenio@caseicom.vc>',
        'to'      => $row['email'],
        'subject' => 'Save the Date: Daniella e Eughenio irão se casar',
        'html'    => $template->render(["name" => $row['name']]),
        'text'    => "Olá ".$row['name'].", Eughenio Constantino e Daniella Velloso irão se casar. Então, guarde bem esse dia. 07/01/2017 - Convite Formal em breve. R.S.V.P neste e-mail.",
      ));

      var_dump($result);
    }
  }
}
