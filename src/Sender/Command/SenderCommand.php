<?php

namespace Sender\Command;

use Dwoo;
use Mailgun\Mailgun;
use League\Csv\Reader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SenderCommand extends Command
{
	protected $msg;
	protected $template;

	public function __construct()
	{
		parent::__construct();
		$this->msg = Mailgun::create(getenv('API_KEY'));
		$this->template = new Dwoo\Core();
	}

	protected function configure()
	{
		$this
			->setName('sender:dispatch')
			->addArgument('csv', InputArgument::REQUIRED, 'CSV')
			->addArgument('template', InputArgument::REQUIRED, 'Template do email, deve ter extensão tpl')
			->addArgument('subject', InputArgument::REQUIRED, 'Título do email')
			->setDescription('Envia emai para uma lista CSV')
			->setHelp('Para disparar email para uma lista de CSV, deve usar o comando: sender:dispatch lista.csv template.tpl "Titulo do email".
O template html utiliza a engine http://dwoo.org/documentation/v1.3/')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$output->writeln(
			'Lendo CSV',
			OutputInterface::VERBOSITY_VERBOSE
		);
		$csv = Reader::createFromPath($input->getArgument('csv'))->setHeaderOffset(0)->getRecords();

		$this->sendEmail($csv, $input->getArgument('template'), $input->getArgument('subject'), $output);

		$output->writeln("Emails Enviados");
	}

	protected function sendEmail($csv, $template, $subject, $output){
		foreach ($csv as $key => $value) {

			$temp = $this->template->get($template, $value);

			$response = $this->msg->messages()->send(getenv('DOMAIN'), [
				'from'    => getenv('SENDER'),
				'to'      => $value['email'],
				'subject' => $subject,
				'html'	  => $temp,
				'o:tracking-opens' => true,
			]);

			$output->writeln(
				"Enviado para {$value['nome']} - {$value['email']}",
				OutputInterface::VERBOSITY_VERBOSE
			);
		}
	}
}