<?php
namespace Services\Service;

use Zend\Log\Logger;
use Zend\Log\Writer\Stream;
use Zend\Log\Formatter\Simple;

class Writer
{
    private $logger;

    public function write($msg, $type = 'INFO')
    {
        $writerConfig = include 'config/writer.php';

        switch($type) {
            case 'INFO':
                $function = 'info';
                $path = $writerConfig['path'] . '/info/' . $writerConfig['file_name'] . '.txt';
                break;

            case 'ERR':
                $function = 'error';
                $path = $writerConfig['path'] . '/error/' . $writerConfig['file_name'] . '.txt';
                break;

            default:
                break;
        }

        $formatter  = new Simple($writerConfig['format']);

        $writer     = new Stream($path);
        $writer->setFormatter($formatter);

        $this->logger = new Logger();
        $this->logger->addWriter($writer);

        $this->$function($msg);
    }

    public function info($msg)
    {
        $this->logger->info($msg);
    }

    public function error($msg)
    {
        $this->logger->err($msg);
    }
}