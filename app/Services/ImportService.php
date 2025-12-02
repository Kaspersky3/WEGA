<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use OpenSpout\Reader\Common\Creator\ReaderEntityFactory;
use OpenSpout\Reader\ReaderInterface;

class ImportService
{
    /**
     * Parse un fichier CSV/XLSX et retourne les données d'inventaire.
     *
     * @return array{products: array, errors: array}
     */
    public function parseInventoryFile(UploadedFile $file): array
    {
        $errors = [];
        $products = [];

        try {
            $reader = $this->createReader($file);
            $reader->open($file->getPathname());

            $header = null;
            $rowIndex = 0;

            foreach ($reader->getSheetIterator() as $sheet) {
                foreach ($sheet->getRowIterator() as $row) {
                    $rowIndex++;
                    $cells = $row->getCells();

                    // Convertir les cellules en tableau simple
                    $cellValues = [];
                    foreach ($cells as $cell) {
                        $cellValues[] = $cell->getValue();
                    }

                    // Première ligne = en-têtes
                    if ($header === null) {
                        $header = $this->normalizeHeader($cellValues);
                        continue;
                    }

                    // Ignorer les lignes vides
                    if ($this->isEmptyRow($cellValues)) {
                        continue;
                    }

                    // Parser la ligne
                    $parsed = $this->parseInventoryRow($header, $cellValues, $rowIndex);
                    
                    if ($parsed['error']) {
                        $errors[] = $parsed['error'];
                        continue;
                    }

                    $products[] = $parsed['data'];
                }
            }

            $reader->close();
        } catch (\Exception $e) {
            $errors[] = "Erreur lors de la lecture du fichier : " . $e->getMessage();
        }

        return [
            'products' => $products,
            'errors' => $errors,
        ];
    }

    /**
     * Crée un reader approprié selon le type de fichier.
     */
    protected function createReader(UploadedFile $file): ReaderInterface
    {
        $extension = strtolower($file->getClientOriginalExtension());

        if ($extension === 'csv' || $extension === 'txt') {
            // OpenSpout détecte automatiquement le délimiteur
            // Pour forcer le point-virgule, on peut utiliser les options
            $options = new \OpenSpout\Reader\CSV\Options();
            $options->setFieldDelimiter(';');
            $options->setFieldEnclosure('"');
            
            $reader = ReaderEntityFactory::createCSVReader();
            $reader->setOptions($options);
            return $reader;
        }

        if ($extension === 'xlsx') {
            return ReaderEntityFactory::createXLSXReader();
        }

        throw new \InvalidArgumentException("Format de fichier non supporté : {$extension}");
    }

    /**
     * Normalise les en-têtes du fichier.
     */
    protected function normalizeHeader(array $cells): array
    {
        $header = [];
        foreach ($cells as $index => $cell) {
            $value = strtolower(trim($cell ?? ''));
            $header[$index] = $value;
        }
        return $header;
    }

    /**
     * Vérifie si une ligne est vide.
     */
    protected function isEmptyRow(array $cells): bool
    {
        foreach ($cells as $cell) {
            $value = trim($cell ?? '');
            if ($value !== '') {
                return false;
            }
        }
        return true;
    }

    /**
     * Parse une ligne d'inventaire.
     */
    protected function parseInventoryRow(array $header, array $cells, int $rowIndex): array
    {
        $data = [];
        foreach ($cells as $index => $cell) {
            $value = trim($cell ?? '');
            $headerKey = $header[$index] ?? $index;
            $data[$headerKey] = $value;
            // Ajouter aussi l'index numérique pour compatibilité
            $data[$index] = $value;
        }

        // Normaliser les clés d'en-tête pour la recherche (sans accents, minuscules)
        $normalizedData = [];
        foreach ($data as $key => $value) {
            $normalizedKey = $this->normalizeKey($key);
            $normalizedData[$normalizedKey] = $value;
            $normalizedData[$key] = $value; // Garder aussi la clé originale
        }
        $data = array_merge($data, $normalizedData);

        // Format 1 : Format d'export complet (avec colonnes Produit, Code, Stock réel gros, Stock réel détail)
        $code = $data['code'] ?? $data['code produit'] ?? $data['code_produit'] ?? '';
        $stockReelGros = null;
        $stockReelDetail = null;

        // Chercher les colonnes de stock réel avec différentes variantes
        $stockGrosKeys = ['stock réel (gros)', 'stock_reel_gros', 'stock reel gros', 'stock reel (gros)', 'gros'];
        $stockDetailKeys = ['stock réel (détail)', 'stock_reel_detail', 'stock reel detail', 'stock reel (detail)', 'détail', 'detail'];

        foreach ($stockGrosKeys as $key) {
            if (isset($data[$key])) {
                $stockReelGros = $this->parseNumber($data[$key]);
                break;
            }
        }

        foreach ($stockDetailKeys as $key) {
            if (isset($data[$key])) {
                $stockReelDetail = $this->parseNumber($data[$key]);
                break;
            }
        }

        // Format 2 : Format simple (Code, Stock réel gros, Stock réel détail) - colonnes par index
        if (empty($code) && isset($data[0])) {
            $code = trim($data[0]);
        }
        if ($stockReelGros === null && isset($data[1])) {
            $stockReelGros = $this->parseNumber($data[1]);
        }
        if ($stockReelDetail === null && isset($data[2])) {
            $stockReelDetail = $this->parseNumber($data[2]);
        }

        // Si toujours pas trouvé, essayer avec les index suivants
        if ($stockReelGros === null && isset($data[3])) {
            $stockReelGros = $this->parseNumber($data[3]);
        }
        if ($stockReelDetail === null && isset($data[4])) {
            $stockReelDetail = $this->parseNumber($data[4]);
        }

        // Valeurs par défaut
        $stockReelGros = $stockReelGros ?? 0;
        $stockReelDetail = $stockReelDetail ?? 0;

        if (empty($code)) {
            return [
                'error' => "Ligne {$rowIndex} : Code produit manquant",
                'data' => null,
            ];
        }

        if (empty($code)) {
            return [
                'error' => "Ligne {$rowIndex} : Code produit manquant",
                'data' => null,
            ];
        }

        // Rechercher le produit par code
        $product = Product::where('code_produit', $code)->first();

        if (!$product) {
            return [
                'error' => "Ligne {$rowIndex} : Produit avec le code '{$code}' introuvable",
                'data' => null,
            ];
        }

        return [
            'error' => null,
            'data' => [
                'product_id' => $product->id,
                'stock_reel_gros' => (int) $stockReelGros,
                'stock_reel_detail' => (int) $stockReelDetail,
            ],
        ];
    }

    /**
     * Parse un nombre depuis une chaîne (gère les espaces, virgules, etc.).
     */
    protected function parseNumber(string $value): float
    {
        if (empty($value)) {
            return 0;
        }

        // Supprimer les espaces
        $value = str_replace(' ', '', $value);
        // Remplacer la virgule par un point
        $value = str_replace(',', '.', $value);
        // Extraire uniquement les chiffres et le point
        $value = preg_replace('/[^0-9.-]/', '', $value);
        
        return (float) ($value ?: 0);
    }

    /**
     * Normalise une clé pour la recherche (supprime accents, espaces, etc.).
     */
    protected function normalizeKey(string $key): string
    {
        $key = strtolower(trim($key));
        // Supprimer les accents
        $key = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $key);
        // Remplacer les espaces et caractères spéciaux par underscore
        $key = preg_replace('/[^a-z0-9]/', '_', $key);
        return $key;
    }
}

