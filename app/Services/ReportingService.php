<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class ReportingService
{
    public function getProfitabilityTree(?string $rootCode = null): Collection
    {
        $bindings = [];
        $rootCondition = '';

        if ($rootCode !== null) {
            $rootCondition = 'WHERE root.code = ?';
            $bindings[] = $rootCode;
        }

        $sql = <<<SQL
WITH RECURSIVE account_hierarchy AS (
    SELECT id, parent_id, code, name, id AS root_id
    FROM analytical_accounts
    UNION ALL
    SELECT child.id, child.parent_id, child.code, child.name, account_hierarchy.root_id
    FROM analytical_accounts child
    JOIN account_hierarchy ON child.parent_id = account_hierarchy.id
),
account_costs AS (
    SELECT aal.analytical_account_id, SUM(aal.amount) AS total_amount
    FROM analytical_allocations aal
    JOIN journal_entries je ON je.id = aal.journal_entry_id
    GROUP BY aal.analytical_account_id
),
rollup AS (
    SELECT ah.root_id AS analytical_account_id, SUM(COALESCE(ac.total_amount, 0)) AS rolled_amount
    FROM account_hierarchy ah
    LEFT JOIN account_costs ac ON ac.analytical_account_id = ah.id
    GROUP BY ah.root_id
)
SELECT aa.id,
       aa.code,
       aa.name,
       aa.parent_id,
       COALESCE(r.rolled_amount, 0) AS total_amount
FROM analytical_accounts aa
LEFT JOIN rollup r ON r.analytical_account_id = aa.id
{$rootCondition}
ORDER BY aa.parent_id NULLS FIRST, aa.code
SQL;

        return collect(DB::select($sql, $bindings));
    }

    public function getCostSummaryByAccountHierarchy(): Collection
    {
        $sql = <<<SQL
WITH RECURSIVE account_hierarchy AS (
    SELECT id, parent_id, code, name, id AS root_id
    FROM analytical_accounts
    UNION ALL
    SELECT child.id, child.parent_id, child.code, child.name, account_hierarchy.root_id
    FROM analytical_accounts child
    JOIN account_hierarchy ON child.parent_id = account_hierarchy.id
),
account_costs AS (
    SELECT aal.analytical_account_id, SUM(aal.amount) AS total_amount
    FROM analytical_allocations aal
    JOIN journal_entries je ON je.id = aal.journal_entry_id
    GROUP BY aal.analytical_account_id
),
rollup AS (
    SELECT ah.root_id AS analytical_account_id, SUM(COALESCE(ac.total_amount, 0)) AS rolled_amount
    FROM account_hierarchy ah
    LEFT JOIN account_costs ac ON ac.analytical_account_id = ah.id
    GROUP BY ah.root_id
)
SELECT aa.id,
       aa.code,
       aa.name,
       aa.parent_id,
       COALESCE(r.rolled_amount, 0) AS total_amount
FROM analytical_accounts aa
LEFT JOIN rollup r ON r.analytical_account_id = aa.id
ORDER BY aa.parent_id NULLS FIRST, aa.code
SQL;

        return collect(DB::select($sql));
    }
}
