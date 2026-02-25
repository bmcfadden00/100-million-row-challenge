<?php

namespace App;

use Exception;

final class Parser
{
    public function parse(string $inputPath, string $outputPath): void
    {
        $fh = fopen($inputPath, 'r');
        if (!$fh) {
            throw new Exception('input file not found');
        }
        $results = [];
        $skipUrl = strlen('https://stitcher.io');
        while ($line = fgets($fh)) {
            $fields = explode(',', trim($line));
            $path = substr($fields[0], $skipUrl);
            $date = substr($fields[1], 0, 10); // 2026-01-24
        
            if (!array_key_exists($path, $results)) {
                // $results[$path] needs to be ordered lexigraphically
                $results[$path] = array();
            }
            if (!array_key_exists($date, $results[$path])) {
                $results[$path][$date] = 1;
            } else {
                $results[$path][$date] += 1;
            }
        }
        // change to by reference
        foreach($results as &$days) {
            ksort($days);
        }
        fclose($fh);
        file_put_contents($outputPath, json_encode($results, JSON_PRETTY_PRINT));
    }
}