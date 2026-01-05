<?php

namespace App\Services;

use App\Models\Transfer;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransferService
{
    /**
     * Enregistre un transfert et met à jour les stocks des produits.
     */
    public function storeTransfer(
        ?User $user,
        int $productId,
        string $lieuSource,
        string $lieuDestination,
        int $quantiteGros,
        int $quantiteDetail,
        Carbon $dateTransfert,
        ?string $notes = null
    ): Transfer {
        $product = Product::findOrFail($productId);

        // Validation : vérifier que le produit est bien dans le lieu source
        $this->validateTransfer($product, $lieuSource, $quantiteGros, $quantiteDetail);

        return DB::transaction(function () use (
            $user,
            $product,
            $productId,
            $lieuSource,
            $lieuDestination,
            $quantiteGros,
            $quantiteDetail,
            $dateTransfert,
            $notes
        ) {
            // Créer le transfert
            $transfer = Transfer::create([
                'reference' => $this->generateReference($dateTransfert),
                'product_id' => $productId,
                'lieu_source' => $lieuSource,
                'lieu_destination' => $lieuDestination,
                'quantite_gros' => $quantiteGros,
                'quantite_detail' => $quantiteDetail,
                'date_transfert' => $dateTransfert,
                'user_id' => $user?->id,
                'notes' => $notes,
            ]);

            // Mettre à jour les stocks du produit
            $this->updateProductStocks($product, $lieuSource, $lieuDestination, $quantiteGros, $quantiteDetail);

            return $transfer->load('product', 'user');
        });
    }

    /**
     * Valide qu'un transfert est possible.
     */
    protected function validateTransfer(Product $product, string $lieuSource, int $quantiteGros, int $quantiteDetail): void
    {
        // Vérifier que le produit est dans le lieu source
        if ($product->lieu !== $lieuSource) {
            throw new \InvalidArgumentException(
                "Le produit {$product->libelle} n'est pas dans le lieu source {$lieuSource}. Il est actuellement dans {$product->lieu}."
            );
        }

        // Vérifier que les quantités sont disponibles
        if ($quantiteGros > 0 && ($product->stock_gros ?? 0) < $quantiteGros) {
            throw new \InvalidArgumentException(
                "Stock insuffisant en gros. Disponible: {$product->stock_gros}, Demandé: {$quantiteGros}"
            );
        }

        if ($quantiteDetail > 0 && ($product->stock_detail ?? 0) < $quantiteDetail) {
            throw new \InvalidArgumentException(
                "Stock insuffisant en détail. Disponible: {$product->stock_detail}, Demandé: {$quantiteDetail}"
            );
        }

        // Vérifier qu'au moins une quantité est fournie
        if ($quantiteGros === 0 && $quantiteDetail === 0) {
            throw new \InvalidArgumentException("Vous devez spécifier au moins une quantité (gros ou détail) à transférer.");
        }
    }

    /**
     * Met à jour les stocks du produit lors d'un transfert.
     * 
     * Note: Pour simplifier, on considère que le produit est dans UN seul lieu à la fois.
     * Lors d'un transfert, on retire les quantités du lieu source et on met à jour le lieu.
     */
    protected function updateProductStocks(
        Product $product,
        string $lieuSource,
        string $lieuDestination,
        int $quantiteGros,
        int $quantiteDetail
    ): void {
        // Retirer du lieu source
        if ($quantiteGros > 0) {
            $product->stock_gros = max(0, ($product->stock_gros ?? 0) - $quantiteGros);
        }

        if ($quantiteDetail > 0) {
            $product->stock_detail = max(0, ($product->stock_detail ?? 0) - $quantiteDetail);
        }

        // Recalculer le stock actuel
        $conversionRate = $product->conversionRate();
        $product->stock_actuel = ($product->stock_gros * $conversionRate) + $product->stock_detail;

        // Mettre à jour le lieu du produit vers la destination
        // Le produit sera désormais dans le lieu de destination
        $product->lieu = $lieuDestination;

        $product->save();
    }

    /**
     * Génère une référence unique pour un transfert.
     */
    protected function generateReference(Carbon $date): string
    {
        do {
            $reference = sprintf('TRF-%s-%s', $date->format('Ymd'), Str::upper(Str::random(4)));
        } while (Transfer::where('reference', $reference)->exists());

        return $reference;
    }
}

