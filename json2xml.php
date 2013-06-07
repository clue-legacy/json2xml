<?php

$json = stream_get_contents(STDIN);
$data = @json_decode($json, false);

if (!is_array($data) && !is_object($data)) {
    echo 'ERROR: Invalid JSON given' . PHP_EOL;
    exit(1);
}


class Exporter
{
    private $root = 'document';
    private $indentation = '    ';
    // TODO: private $this->addtypes = false; // type="string|int|float|array|null|bool"

    public function export($data)
    {
        $data = array($this->root => $data);

        echo '<?xml version="1.0" encoding="UTF-8">';
        $this->recurse($data, 0);
        echo PHP_EOL;
    }

    private function recurse($data, $level)
    {
        $indent = str_repeat($this->indentation, $level);
        foreach ($data as $key => $value) {
            echo PHP_EOL . $indent . '<' . $key;

            if ($value === null) {
                echo ' />';
            } else {
                echo '>';

                if (is_array($value)) {
                    if ($value) {
                        $temporary = $this->getArrayName($key);
                        foreach ($value as $entry) {
                            $this->recurse(array($temporary => $entry), $level + 1);
                        }
                        echo PHP_EOL . $indent;
                    }
                } else if (is_object($value)) {
                    if ($value) {
                        $this->recurse($value, $level + 1);
                        echo PHP_EOL . $indent;
                    }
                } else {
                    if (is_bool($value)) {
                        $value = $value ? 'true' : 'false';
                    }
                    echo $this->escape($value);
                }

                echo '</' . $key . '>';
            }
        }
    }

    private function escape($value)
    {
        // TODO:
        return $value;
    }

    private function getArrayName($parentName)
    {
        // TODO: special namding for tag names within arrays
        return $parentName;
    }
}

$exporter = new Exporter();
$exporter->export($data);
