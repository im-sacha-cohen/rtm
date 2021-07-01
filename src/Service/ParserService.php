<?php

namespace App\Service;

use DOMXPath;
use DOMDocument;

class ParserService {
    /**
     * @param string $request the XML string response
     * 
     * @return array
     */
    public function parse(string $request): array {
        $document = new DOMDocument();
        $document->loadXML($request);
        $xpath = new DOMXPath($document);

        $items = [];
        foreach ($xpath->evaluate('//ns1:getStationDetailsResponse') as $tableNode) {
            $items[] = [
                trim(
                    $xpath->evaluate('string(ns1:comLieu)', $tableNode),
                ),
                trim(
                    $xpath->evaluate('string(ns1:passage/ns1:destination)', $tableNode)
                )
            ];
        }

        foreach ($xpath->evaluate('//ns1:passage') as $tableNode) {
            $next = $xpath->evaluate('string(ns1:heurePassageReel)', $tableNode);
            $diff = date_diff(date_create($next), date_create());
            $diffMinute = $diff->format('%i');

            if ($diffMinute == 0) {
                $diffText = $diff->format('%s secondes');
            } else {
                $diffText = $diff->format('%i minutes %s');
            }

            $items[] = [
                trim(
                    $next
                ),
                $diffText,
                'heeey hh'
            ];
        }

        return $items;
    }
}