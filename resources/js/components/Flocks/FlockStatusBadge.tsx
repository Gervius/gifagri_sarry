import React from 'react';

// Locally define FlockStatus to fix the missing export error from '@/types'
export type FlockStatus =
    | 'draft'
    | 'pending'
    | 'active'
    | 'rejected'
    | 'completed';

interface Props {
    status: FlockStatus;
}

const statusConfig: Record<FlockStatus, { color: string; label: string }> = {
    draft: {
        color: 'bg-stone-100 text-stone-700 border border-stone-200',
        label: 'Brouillon',
    },
    pending: {
        color: 'bg-amber-50 text-amber-700 border border-amber-200',
        label: 'En attente',
    },
    active: {
        color: 'bg-emerald-50 text-emerald-700 border border-emerald-200',
        label: 'Active',
    },
    completed: {
        color: 'bg-blue-50 text-blue-700 border border-blue-200',
        label: 'Terminée',
    },
    rejected: {
        color: 'bg-red-50 text-red-700 border border-red-200',
        label: 'Rejetée',
    },
};

export default function FlockStatusBadge({ status }: Props) {
    const config = statusConfig[status] || statusConfig.draft;

    return (
        <span
            className={`rounded-full px-3 py-1 text-xs font-medium ${config.color}`}
        >
            {config.label}
        </span>
    );
}
