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
            $nextDateTime = date_create($next);
            $now = date_create();
            $setDiff = true;
            
            // For example : metro comes at 14:48 and it's 14:49 -> to not incremente $diff
            if (strtotime($next) > strtotime('now')) {
                $diff = date_diff($now, $nextDateTime);
            } else {
                $diffText = 'est actuellement à quai';
                $setDiff = false;
            }

            if ($setDiff) {
                $diffMinute = (int) $diff->format('%i');
                $diffSeconds = (int) $diff->format('%s');

                if ($diffMinute === 0) {
                    if ($diffSeconds <= 20) {
                        $diffText = 'est actuellement à quai';
                    } else {
                        $diffText = 'dans ' . $diffSeconds . ' secondes';
                    }
                } else {
                    $diffText = 'dans ' . $diff->format('%i minutes %s');
                }
            }

            $items[] = [
                trim(
                    $next
                ),
                $diffText
            ];
        }

        return $items;
    }
}