<?php

namespace Sender\Command;

use Mailgun\Mailgun;
use League\Csv\Reader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SenderCommandAbstract extends Command
{
  /**
   * renderTemplate
   * Render the HTML template with the infomration received in the array.
   * @param  [string] $template  [HTML file that should be rendered with the infomrations informed in the array]
   * @param  [array]  $variables [List of fields that will be parsed in the HTML]
   * @return [string]            [String with the rendered template]
   */
  protected function renderTemplate($template, $variables)
  {
    $loader = new \Twig_Loader_Filesystem($_ENV['TEMPLATE_DIR']);
    $twig = new \Twig_Environment($loader);
    $loadedTemplate = $twig->loadTemplate($template);

    $templateRendered = $loadedTemplate->render($variables);

    return $templateRendered;
  }

  /**
   * readerCsv
   * Reads the csv informed in the $filePath and returns an array
   * @param  [file] $filePath [File CSV]
   * @return [iterator]       [Iterator where in the array the first line of the CSV is key]
   */
  protected function readerCsv($filePath)
  {
    $csv = Reader::createFromPath($filePath);
    $delimiters = $csv->fetchDelimitersOccurrence([',', ';'], 100);
    $delimiter = array_search(max($delimiters), $delimiters);
    $csv->setDelimiter($delimiter);
    $headers = $csv->fetchOne();
    $results = $csv->setOffset(1)->fetchAssoc($headers);

    return $results;
  }

  /**
   * senderMailgun
   * Send the email with the template and parameters by Mailgun
   * @param  [string]        $template   [email template]
   * @param  [array]         $parameters [Array with emails parameters]
   * @param  OutputInterface $output     [Console Ouput]
   * @return [string]                    [String with response from Mailgun]
   */
  protected function senderMailgun($base, $html, $output)
  {
    $mailgun = new Mailgun($_ENV['MAILGUN_KEY']);

    foreach ($base as $value) {
      $template = $this->renderTemplate($html, $value);

      $parameters = [
        'from'    => 'Daniella e Eughenio <daniellaeeughenio@caseicom.vc>',
        'to'      => $value['email'],
        'subject' => 'Save the Date: Daniella e Eughenio irão se casar',
        'html'    => $template,
      ];

      $send = $mailgun->sendMessage($_ENV['MAILGUN_DOMAIN'], $parameters);

      $output->writeln("<info>".$value['email']." - ".$send->http_response_code." - ".$send->http_response_body->message."</info>");
    }
  }
}
