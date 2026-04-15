# Architecture Financière - ERP Gestion Agricole

## Vue d'ensemble

L'architecture financière du système implémente une séparation stricte entre les mouvements physiques et les mouvements financiers, respectant les principes SOLID et utilisant une architecture orientée événements (Event-Driven Architecture).

## Principes Clés

### 1. Séparation Physique/Financière

**Facture d'Achat (Purchase)**
- Approbation = Génération de dette fournisseur (comptabilité)
- N'affecte PAS le stock physique
- Met à jour le PMP (Prix Moyen Pondéré) via les Landed Costs

**Facture de Vente (Sale)**
- Approbation = Réalisation du chiffre d'affaires (comptabilité)
- Affecte le stock physique (décrément)
- Génère les écritures de produits/clients

### 2. Flux d'Approbation

```
ApproveInvoiceAction (Entry Point)
    ↓
DB::transaction {
    - Validence l'invoice
    - Déclenche l'événement InvoiceApproved
    - Écritures comptables via AccountingEngineService
}
    ↓
InvoiceApproved Event
    ├→ ProcessPartialSales (Décrément stock si SALE + BROILER)
    └→ AdjustPMPFromLandedCosts (Ajuste PMP si PURCHASE + Ingredient)
```

### 3. Moteur Comptable

**AccountingEngineService**
- Lit les `AccountingRules` par type d'événement
- Résout les comptes dynamiquement (partenaire, taxe, items)
- Génère `JournalVoucher` + `JournalEntries`
- Valide l'équilibre débit/crédit

**Types d'Événements**
- `purchase_invoice_validated` → Enregistre la dette fournisseur
- `sale_invoice_validated` → Enregistre les produits/créances clients

### 4. Frais d'Approche (Landed Costs)

Les coûts du transport, assurance, etc., sont lus de `landed_cost_allocations` et répartis sur les ingrédients via `StockValuationService`.

**Formule PMP**
```
Nouveau PMP = (Stock Existant × PMP Ancienne + (Coût Achat Net + Frais)) / Quantité Totale
```

## Architecture Événementielle

### Event: `InvoiceApproved`
```php
public function __construct(
    public Invoice $invoice
) {}
```

### Listeners

#### `AdjustPMPFromLandedCosts`
- **Condition** : `invoice->type === 'purchase'`
- **Action** : Met à jour PMP des ingrédients
- **Responsibilité** : Valorisation du stock

#### `ProcessPartialSales`
- **Condition** : `invoice->type === 'sale'` ET `flock->animalType->code === 'broiler'`
- **Action** : Décrémente `flock->current_quantity`
- **Responsibilité** : Mouvements physiques de vente

## Composants Techniques

### 1. ApproveInvoiceAction
```php
public function execute(Invoice $invoice, int $approverId): void
```
- Point d'entrée unique pour l'approbation
- Encapsule la transaction complète
- Dispatche l'événement

### 2. AdjustPMPFromLandedCosts Listener
```php
public function handle(InvoiceApproved $event): void
```
- Itère les items de la facture
- Récupère les `LandedCostAllocations`
- Appelle `StockValuationService::calculateAndApplyNewPMP()`

### 3. ProcessPartialSales Listener (Existant)
```php
public function handle(InvoiceApproved $event): void
```
- Pour les ventes, décrémente la quantité
- Actuellement supporté pour les Broilers
- Extensible pour d'autres espèces (porcs, etc.)

### 4. InvoiceController (Thin)
```php
public function approve(ApproveRequest $request, Invoice $invoice, ApproveInvoiceAction $approveAction): RedirectResponse
{
    $approveAction->execute($invoice, $request->user()->id);
    return redirect()->back()->with('success', '...');
}
```

## Intégrité Comptable

### Validation Automatique
- Chaque `JournalVoucher` est validé : **Débits = Crédits**
- Lève `AccountingImbalanceException` si déséquilibre

### Traçabilité
- `JournalVoucher` enregistre la source (`source_type`, `source_id`)
- Permet l'audit complet du document source

## Cas d'Usage

### Achat d'Ingrédients
1. Facture d'achat créée
2. Utilisateur approuve la facture
3. **Comptabilité** : Enregistre la dette fournisseur
4. **Stock Financier** : Met à jour le PMP (via Landed Costs)
5. **Stock Physique** : Inchangé (récépisse séparé)

### Vente à Chaud (Broilers)
1. Facture de vente créée (100 broilers vendus)
2. Utilisateur approuve la facture
3. **Comptabilité** : Enregistre le chiffre d'affaires
4. **Stock Physique** : Décrémente `flock->current_quantity` de 100

### Future Extension: Porcs en Engraissement
- Même logique que les Broilers
- Listener `ProcessPartialSales` déjà extensible
- Vérifier `flock->animalType->code === 'pig'` dans la condition

## Responsabilités (SRP)

| Classe | Responsabilité |
|--------|----------------|
| `ApproveInvoiceAction` | Orchestration de l'approbation |
| `AccountingEngineService` | Génération des écritures comptables |
| `StockValuationService` | Calcul du PMP et Landed Costs |
| `AdjustPMPFromLandedCosts` | Réaction aux approvals (PMP) |
| `ProcessPartialSales` | Réaction aux approvals (Stock physique) |
| `InvoiceController` | Routage HTTP (Thin) |

## Extensibilité (OCP)

- Ajouter un nouveau type de coût (ex: douanes) : Modifier `LandedCostAllocation`
- Ajouter une nouvelle règle comptable : Créer `AccountingRule` en base
- Ajouter une nouvelle espèce pour ventes : Ajouter condition dans `ProcessPartialSales`
- Ajouter un nouveau listener : Enregistrer dans `EventServiceProvider`

## Configuration Requise

### AccountingRules (en base de données)
```sql
-- Purchase invoice
INSERT INTO accounting_rules (event_type, is_active, ...)
VALUES ('purchase_invoice_validated', true, ...);

-- Sale invoice
INSERT INTO accounting_rules (event_type, is_active, ...)
VALUES ('sale_invoice_validated', true, ...);
```

### Partners
- Doivent avoir un `accounting_account_id` pour les dettes/créances

### Ingredients
- Doivent avoir un `accounting_account_id` pour les stocks

## Tests

Voir `tests/Feature/InvoiceFinancialLogicTest.php` pour les scénarios couverts :
- Approbation génère écritures
- Achat n'affecte pas stock physique
- Vente décrémente stock
- Landed Costs mettent à jour le PMP
