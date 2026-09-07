<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * SimpleXLSX / Spreadsheet reader for Microsoft Teams Attendance
 * Reads .xlsx (via native ZipArchive + SimpleXML) and .csv (with BOM/delimiter auto-detection)
 */
class SimpleXLSX
{
    /**
     * Parse an XLSX or CSV file into a 2D array of rows
     */
    public static function parse($filePath)
    {
        if (!file_exists($filePath)) {
            return [];
        }

        // Check if it's a zip/xlsx by inspecting magic bytes "PK\x03\x04"
        $fh = fopen($filePath, 'rb');
        $magic = fread($fh, 4);
        fclose($fh);

        if ($magic === "PK\x03\x04") {
            return self::parseXlsx($filePath);
        } else {
            return self::parseCsv($filePath);
        }
    }

    /**
     * Parse .xlsx file using built-in ZipArchive and SimpleXML
     */
    public static function parseXlsx($filePath)
    {
        $rows = [];
        $zip = new ZipArchive();
        if ($zip->open($filePath) !== TRUE) {
            return [];
        }

        // 1. Read shared strings
        $sharedStrings = [];
        $ssIndex = $zip->locateName('xl/sharedStrings.xml');
        if ($ssIndex !== false) {
            $xmlString = $zip->getFromIndex($ssIndex);
            if ($xmlString) {
                $xml = simplexml_load_string($xmlString);
                if ($xml && isset($xml->si)) {
                    foreach ($xml->si as $si) {
                        if (isset($si->t)) {
                            $sharedStrings[] = (string)$si->t;
                        } elseif (isset($si->r)) {
                            $text = '';
                            foreach ($si->r as $r) {
                                $text .= (string)$r->t;
                            }
                            $sharedStrings[] = $text;
                        } else {
                            $sharedStrings[] = '';
                        }
                    }
                }
            }
        }

        // 2. Read first worksheet
        $sheetIndex = $zip->locateName('xl/worksheets/sheet1.xml');
        if ($sheetIndex === false) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $stat = $zip->statIndex($i);
                if (preg_match('/xl\/worksheets\/sheet\d+\.xml/i', $stat['name'])) {
                    $sheetIndex = $i;
                    break;
                }
            }
        }

        if ($sheetIndex !== false) {
            $sheetXmlString = $zip->getFromIndex($sheetIndex);
            if ($sheetXmlString) {
                $sheetXml = simplexml_load_string($sheetXmlString);
                if ($sheetXml && isset($sheetXml->sheetData->row)) {
                    foreach ($sheetXml->sheetData->row as $row) {
                        $rowData = [];
                        $colIdx = 0;
                        foreach ($row->c as $cell) {
                            $r = (string)$cell['r'];
                            $colLetter = preg_replace('/[0-9]/', '', $r);
                            $targetIdx = self::colLetterToIndex($colLetter);

                            while ($colIdx < $targetIdx) {
                                $rowData[] = '';
                                $colIdx++;
                            }

                            $type = (string)$cell['t'];
                            $val = isset($cell->v) ? (string)$cell->v : '';

                            if ($type === 's') {
                                $ssId = (int)$val;
                                $cellVal = isset($sharedStrings[$ssId]) ? $sharedStrings[$ssId] : '';
                            } elseif ($type === 'inlineStr' && isset($cell->is->t)) {
                                $cellVal = (string)$cell->is->t;
                            } else {
                                $cellVal = $val;
                            }

                            $rowData[] = trim($cellVal);
                            $colIdx++;
                        }
                        if (!empty(array_filter($rowData, function($v) { return $v !== ''; }))) {
                            $rows[] = $rowData;
                        }
                    }
                }
            }
        }

        $zip->close();
        return $rows;
    }

    /**
     * Parse CSV file with auto-detection for BOM, UTF-16, and delimiters
     */
    public static function parseCsv($filePath)
    {
        $content = file_get_contents($filePath);
        if ($content === false || strlen($content) === 0) {
            return [];
        }

        if (substr($content, 0, 2) === "\xFF\xFE") {
            $content = mb_convert_encoding(substr($content, 2), 'UTF-8', 'UTF-16LE');
        } elseif (substr($content, 0, 2) === "\xFE\xFF") {
            $content = mb_convert_encoding(substr($content, 2), 'UTF-8', 'UTF-16BE');
        } elseif (substr($content, 0, 3) === "\xEF\xBB\xBF") {
            $content = substr($content, 3);
        }

        $content = str_replace(["\r\n", "\r"], "\n", $content);
        $lines = explode("\n", $content);

        $delimiter = ',';
        $sample = implode("\n", array_slice($lines, 0, 5));
        $counts = [
            "\t" => substr_count($sample, "\t"),
            ","  => substr_count($sample, ","),
            ";"  => substr_count($sample, ";")
        ];
        arsort($counts);
        $delimiter = key($counts);

        $rows = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') continue;
            $data = str_getcsv($line, $delimiter, '"', '\\');
            if (!empty(array_filter($data, function($v) { return trim($v) !== ''; }))) {
                $rows[] = array_map('trim', $data);
            }
        }

        return $rows;
    }

    private static function colLetterToIndex($col)
    {
        $col = strtoupper($col);
        $len = strlen($col);
        $num = 0;
        for ($i = 0; $i < $len; $i++) {
            $num = $num * 26 + (ord($col[$i]) - 64);
        }
        return max(0, $num - 1);
    }
}
