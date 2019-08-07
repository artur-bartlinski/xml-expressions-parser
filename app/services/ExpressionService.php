<?php

namespace app\services;

use App\Core\App;

class ExpressionService
{
    const XML_STORAGE_PATH = 'app/storage/xml';
    const SCHEMA_DIR = '/schema';

    protected $xmlProcessedDir;
    protected $xmlValidSchema;
    protected $readyToUpload;
    protected $dom;
    protected $mathOperationsMapper;

    public function __construct()
    {
        $this->xmlProcessedDir = self::XML_STORAGE_PATH . '/processed';
        $this->xmlValidSchema = self::XML_STORAGE_PATH . self::SCHEMA_DIR . '/expressions.xsd';
        $this->readyToUpload = false;
        $this->dom = new \DOMDocument();
        $this->mathOperationsMapper = [
            'add' => '+',
            'minus' => '-',
            'multiply' => '*',
            'divide' => '/'
        ];
    }

    public function saveFile($file)
    {
        $targetFile = $this->xmlProcessedDir . basename($file['name']);

        if (move_uploaded_file($file["tmp_name"], $targetFile)) {
            print "The file ". basename($file["name"]). " has been uploaded.";
        } else {
            print "Sorry, there was an error uploading your file.";
        }
    }

    public function processFile($file)
    {
        if (! $this->isXml($file)) {
            throw new \Exception('Wrong file type! We only accept XML files');
        }

        $this->dom->load($file['tmp_name']);

        if (empty($errors = $this->validateXml($this->dom, $this->xmlValidSchema))) {
            $this->prepareDir();

            return $this->processXML($this->dom->getElementsByTagName('expression'));
        }

        return $errors;
    }

    public function addToDatabase($table, $data)
    {
        App::get('database')->insert($table, $data);
    }

    public function validateXml($xml, $xmlSchema)
    {
        $this->dom->preserveWhiteSpace = false;
        $this->dom->formatOutput       = false;
        libxml_use_internal_errors(true);
        $this->dom->schemaValidate($xmlSchema);
        $errors = libxml_get_errors();
        libxml_clear_errors();

        return $errors;
    }

    protected function isXml($file)
    {
        return preg_match("/\.xml$/", basename($file['name']));
    }

    protected function prepareDir()
    {
        if (! is_dir($this->xmlProcessedDir)) {
            try {
                mkdir($this->xmlProcessedDir, 0755, true);
            } catch (\Exception $e) {
                print 'Could not create directory! ' . $e->getMessage();
            }
        }
    }

    protected function processXML(\DOMNodeList $expressions)
    {
        $data = [];

        for ($i = 0; $i < $expressions->length; $i++) {
            $data[] = [
                'expression' => $expressionString = substr($this->processExpressionTree($expressions[$i]), 0, -1),
                'total' => eval('return ' . $expressionString . ';')
            ];
        }

        return $data;
    }

    private function processExpressionTree($expression)
    {
        for ($i = 0; $i < $expression->childNodes->length; $i++) {
            if (array_key_exists($expression->childNodes[$i]->nodeName, $this->mathOperationsMapper)) {
                return $this->processNode($expression->childNodes[$i], '', $this->mathOperationsMapper[$expression->childNodes[$i]->nodeName]);
            }
        }

        return null;
    }

    private function processNode($node, $expressionString = '', $operator = '')
    {
        for ($i = 1; $i < $node->childNodes->length; $i += 2) {
            if ($node->childNodes[$i]->nodeName === 'number') {
                $operator = $i == ($node->childNodes->length-2) && array_key_exists($node->parentNode->nodeName, $this->mathOperationsMapper) ? ')' : $operator;
                $expressionString .= $node->childNodes[$i]->nodeValue . $operator;
            } elseif (array_key_exists($node->childNodes[$i]->nodeName, $this->mathOperationsMapper)) {
                $expressionString .= $this->processNode($node->childNodes[$i], '(', $this->mathOperationsMapper[$node->childNodes[$i]->nodeName]);
                $expressionString .= $i == ($node->childNodes->length-2) && array_key_exists($node->parentNode->nodeName, $this->mathOperationsMapper) ? ')' : (array_key_exists($node->nodeName, $this->mathOperationsMapper) ? $this->mathOperationsMapper[$node->nodeName] : '');
            }
        }

        return $expressionString;
    }
}
