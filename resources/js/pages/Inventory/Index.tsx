import { Head } from '@inertiajs/react';
import {
    Package,
    LayoutGrid,
    History,
    SlidersHorizontal,
    ArrowDownToLine,
    ArrowUpFromLine,
    RefreshCcw,
} from 'lucide-react';
import React, { useState } from 'react';

interface StockItem {
    id: number;
    name: string;
    current_stock: number;
    unit: string;
    min_stock: number;
    type: 'ingredient' | 'feed_stock';
    pmp?: number;
    unit_cost?: number;
    status: 'ok' | 'low' | 'out';
}

interface Movement {
    id: number;
    date: string;
    item_name: string;
    type: 'in' | 'out' | 'adjust';
    quantity: number;
    unit: string;
    reason: string;
}

interface PageProps {
    stock_items: { data: StockItem[] };
    recent_movements: { data: Movement[] };
}

export default function Index({ stock_items, recent_movements }: PageProps) {
    const [activeTab, setActiveTab] = useState<'inventory' | 'movements'>(
        'inventory',
    );

    const formatCurrency = (value: number) => {
        return new Intl.NumberFormat('fr-FR', {
            style: 'currency',
            currency: 'XOF',
            maximumFractionDigits: 0,
        }).format(value);
    };

    return (
        <>
            <Head title="État des Stocks & Logistique" />

            <div className="min-h-screen bg-stone-50 pb-12 text-stone-900">
                <div className="border-b border-stone-200 bg-white px-6 pt-8 pb-6 shadow-sm md:px-8">
                    <div className="mx-auto flex max-w-7xl flex-col justify-between gap-4 md:flex-row md:items-center">
                        <div className="flex items-start gap-4">
                            <div className="mt-1 rounded-2xl border border-amber-200 bg-amber-50 p-3 text-amber-500 shadow-sm">
                                <Package className="h-8 w-8" />
                            </div>
                            <div>
                                <h1 className="text-3xl font-bold text-stone-900">
                                    État des Stocks & Logistique
                                </h1>
                                <p className="mt-1 font-medium text-stone-500">
                                    Surveillance des matières premières et
                                    provendes.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="mx-auto mt-8 max-w-7xl space-y-8 px-6 md:px-8">
                    <div className="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
                        <div className="flex overflow-x-auto border-b border-stone-200 bg-stone-50 px-4 pt-4">
                            <button
                                onClick={() => setActiveTab('inventory')}
                                className={`flex items-center gap-2 border-b-2 px-5 py-3 text-sm font-medium transition-colors ${
                                    activeTab === 'inventory'
                                        ? 'rounded-t-xl border-amber-500 bg-white text-amber-700'
                                        : 'border-transparent text-stone-500 hover:border-stone-300 hover:text-stone-700'
                                }`}
                            >
                                <LayoutGrid className="h-4 w-4" /> Inventaire
                                Actuel
                            </button>

                            <button
                                onClick={() => setActiveTab('movements')}
                                className={`flex items-center gap-2 border-b-2 px-5 py-3 text-sm font-medium transition-colors ${
                                    activeTab === 'movements'
                                        ? 'rounded-t-xl border-amber-500 bg-white text-amber-700'
                                        : 'border-transparent text-stone-500 hover:border-stone-300 hover:text-stone-700'
                                }`}
                            >
                                <History className="h-4 w-4" /> Historique des
                                Mouvements
                            </button>
                        </div>

                        <div className="p-0">
                            {activeTab === 'inventory' && (
                                <div className="overflow-x-auto">
                                    <table className="w-full text-left text-sm">
                                        <thead className="border-b border-stone-200 bg-stone-50 text-xs text-stone-500 uppercase">
                                            <tr>
                                                <th
                                                    scope="col"
                                                    className="px-6 py-4 font-medium"
                                                >
                                                    Article
                                                </th>
                                                <th
                                                    scope="col"
                                                    className="px-6 py-4 font-medium"
                                                >
                                                    Type
                                                </th>
                                                <th
                                                    scope="col"
                                                    className="px-6 py-4 text-right font-medium"
                                                >
                                                    Quantité
                                                </th>
                                                <th
                                                    scope="col"
                                                    className="px-6 py-4 text-right font-medium"
                                                >
                                                    Valeur Unitaire
                                                </th>
                                                <th
                                                    scope="col"
                                                    className="px-6 py-4 font-medium"
                                                >
                                                    Statut
                                                </th>
                                                <th
                                                    scope="col"
                                                    className="px-6 py-4 text-right font-medium"
                                                >
                                                    Actions
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody className="divide-y divide-stone-100 text-stone-700">
                                            {stock_items.data.length > 0 ? (
                                                stock_items.data.map((item) => (
                                                    <tr
                                                        key={item.id}
                                                        className="transition-colors hover:bg-stone-50/50"
                                                    >
                                                        <td className="px-6 py-4 font-medium whitespace-nowrap text-stone-900">
                                                            {item.name}
                                                        </td>
                                                        <td className="px-6 py-4 whitespace-nowrap">
                                                            <span
                                                                className={`inline-flex items-center rounded-md border px-2.5 py-0.5 text-xs font-medium ${
                                                                    item.type ===
                                                                    'ingredient'
                                                                        ? 'border-stone-200 bg-stone-100 text-stone-700'
                                                                        : 'border-amber-200 bg-amber-50 text-amber-700'
                                                                }`}
                                                            >
                                                                {item.type ===
                                                                'ingredient'
                                                                    ? 'Ingrédient'
                                                                    : 'Provende'}
                                                            </span>
                                                        </td>
                                                        <td className="px-6 py-4 text-right font-medium whitespace-nowrap text-stone-900">
                                                            {item.current_stock.toLocaleString(
                                                                'fr-FR',
                                                            )}{' '}
                                                            {item.unit}
                                                        </td>
                                                        <td className="px-6 py-4 text-right whitespace-nowrap text-stone-600">
                                                            {item.pmp !==
                                                            undefined
                                                                ? formatCurrency(
                                                                      item.pmp,
                                                                  )
                                                                : item.unit_cost !==
                                                                    undefined
                                                                  ? formatCurrency(
                                                                        item.unit_cost,
                                                                    )
                                                                  : '-'}
                                                        </td>
                                                        <td className="px-6 py-4 whitespace-nowrap">
                                                            <div className="flex items-center gap-2">
                                                                <span
                                                                    className={`h-2.5 w-2.5 rounded-full ${
                                                                        item.status ===
                                                                        'ok'
                                                                            ? 'bg-emerald-500'
                                                                            : item.status ===
                                                                                'low'
                                                                              ? 'bg-amber-500'
                                                                              : 'bg-red-500'
                                                                    }`}
                                                                ></span>
                                                                <span className="text-xs font-medium text-stone-600">
                                                                    {item.status ===
                                                                    'ok'
                                                                        ? 'Normal'
                                                                        : item.status ===
                                                                            'low'
                                                                          ? 'Seuil critique'
                                                                          : 'Rupture'}
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td className="px-6 py-4 text-right whitespace-nowrap">
                                                            <button className="inline-flex items-center gap-1.5 rounded-lg border border-stone-200 bg-stone-100 px-3 py-1.5 text-xs font-medium text-stone-700 shadow-sm transition-colors hover:bg-stone-200">
                                                                <SlidersHorizontal className="h-3.5 w-3.5" />{' '}
                                                                Ajuster
                                                            </button>
                                                        </td>
                                                    </tr>
                                                ))
                                            ) : (
                                                <tr>
                                                    <td
                                                        colSpan={6}
                                                        className="px-6 py-12 text-center text-stone-500"
                                                    >
                                                        Aucun article en stock.
                                                    </td>
                                                </tr>
                                            )}
                                        </tbody>
                                    </table>
                                </div>
                            )}

                            {activeTab === 'movements' && (
                                <div className="overflow-x-auto">
                                    <table className="w-full text-left text-sm">
                                        <thead className="border-b border-stone-200 bg-stone-50 text-xs text-stone-500 uppercase">
                                            <tr>
                                                <th
                                                    scope="col"
                                                    className="px-6 py-4 font-medium"
                                                >
                                                    Date
                                                </th>
                                                <th
                                                    scope="col"
                                                    className="px-6 py-4 font-medium"
                                                >
                                                    Article
                                                </th>
                                                <th
                                                    scope="col"
                                                    className="px-6 py-4 font-medium"
                                                >
                                                    Mouvement
                                                </th>
                                                <th
                                                    scope="col"
                                                    className="px-6 py-4 text-right font-medium"
                                                >
                                                    Quantité
                                                </th>
                                                <th
                                                    scope="col"
                                                    className="px-6 py-4 font-medium"
                                                >
                                                    Motif
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody className="divide-y divide-stone-100 text-stone-700">
                                            {recent_movements.data.length >
                                            0 ? (
                                                recent_movements.data.map(
                                                    (movement) => (
                                                        <tr
                                                            key={movement.id}
                                                            className="transition-colors hover:bg-stone-50/50"
                                                        >
                                                            <td className="px-6 py-4 font-medium whitespace-nowrap text-stone-900">
                                                                {movement.date}
                                                            </td>
                                                            <td className="px-6 py-4 font-medium whitespace-nowrap text-stone-900">
                                                                {
                                                                    movement.item_name
                                                                }
                                                            </td>
                                                            <td className="px-6 py-4 whitespace-nowrap">
                                                                <span
                                                                    className={`inline-flex items-center gap-1.5 rounded-md border px-2.5 py-1 text-xs font-medium ${
                                                                        movement.type ===
                                                                        'in'
                                                                            ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
                                                                            : movement.type ===
                                                                                'out'
                                                                              ? 'border-red-200 bg-red-50 text-red-700'
                                                                              : 'border-stone-200 bg-stone-100 text-stone-700'
                                                                    }`}
                                                                >
                                                                    {movement.type ===
                                                                        'in' && (
                                                                        <ArrowDownToLine className="h-3 w-3" />
                                                                    )}
                                                                    {movement.type ===
                                                                        'out' && (
                                                                        <ArrowUpFromLine className="h-3 w-3" />
                                                                    )}
                                                                    {movement.type ===
                                                                        'adjust' && (
                                                                        <RefreshCcw className="h-3 w-3" />
                                                                    )}
                                                                    {movement.type ===
                                                                    'in'
                                                                        ? 'Entrée'
                                                                        : movement.type ===
                                                                            'out'
                                                                          ? 'Sortie'
                                                                          : 'Ajustement'}
                                                                </span>
                                                            </td>
                                                            <td
                                                                className={`px-6 py-4 text-right font-medium whitespace-nowrap ${
                                                                    movement.type ===
                                                                    'in'
                                                                        ? 'text-emerald-600'
                                                                        : movement.type ===
                                                                            'out'
                                                                          ? 'text-red-600'
                                                                          : 'text-stone-600'
                                                                }`}
                                                            >
                                                                {movement.type ===
                                                                'in'
                                                                    ? '+'
                                                                    : movement.type ===
                                                                        'out'
                                                                      ? '-'
                                                                      : ''}
                                                                {movement.quantity.toLocaleString(
                                                                    'fr-FR',
                                                                )}{' '}
                                                                {movement.unit}
                                                            </td>
                                                            <td className="px-6 py-4">
                                                                {
                                                                    movement.reason
                                                                }
                                                            </td>
                                                        </tr>
                                                    ),
                                                )
                                            ) : (
                                                <tr>
                                                    <td
                                                        colSpan={5}
                                                        className="px-6 py-12 text-center text-stone-500"
                                                    >
                                                        Aucun mouvement récent
                                                        enregistré.
                                                    </td>
                                                </tr>
                                            )}
                                        </tbody>
                                    </table>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
