<?php

// src/Service/ProductScraper.php

namespace App\Service;

use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

class ProductScraper
{
    public function scrapeProduct(string $url): array
    {
        $client = new Client();
        $crawler = $client->request('GET', $url);

        // Recherchez tous les scripts avec le type "application/ld+json"
        $scriptElements = $crawler->filter('script[type="application/ld+json"]');

        $productsInfo = [];

        foreach ($scriptElements as $element) {
            // Obtenez le contenu JSON du script et décodez-le
            $jsonData = json_decode($element->nodeValue, true);

            // Vérifiez si le JSON est un produit
            if ($jsonData['@type'] === 'Product') {
                $productsInfo[] = [
                    'name' => $jsonData['name'],
                    'image' => $jsonData['image'],
                    'price' => $jsonData['offers']['price'],
                    // etc...
                ];
            }
        }

        $title = $productsInfo[0]['name'];
        $image = $productsInfo[0]['image'];
        $price = $productsInfo[0]['price'];
        
        return [
            'nom' => $title,
            'image' => $image,
            'prix' => floatval($price), // Assurez-vous de convertir le prix en float.
        ];
    }
}
