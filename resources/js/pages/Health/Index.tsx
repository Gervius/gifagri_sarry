import { Head, Link } from '@inertiajs/react';
import {
    Syringe,
    Calendar,
    AlertTriangle,
    Clock,
    Banknote,
    PlusCircle,
} from 'lucide-react';
import React, { useState } from 'react';
import { scheduledTreatmentsExecute } from '@/routes';

interface ScheduledTreatment {
    id: number;
    flock_name: string;
    scheduled_date_formatted: string;
    step_name: string;
    method: string;
    status: 'pending' | 'missed';
    days_overdue?: number;
}

interface TreatmentHistory {
    id: number;
    flock_name: string;
    treatment_date_formatted: string;
    type: string;
    veterinarian: string;
    cost: number;
    status: 'draft' | 'approved' | 'executed';
}

interface PageProps {
    kpis: {
        upcoming_count: number;
        missed_count: number;
        pending_approval_count: number;
        monthly_cost: number;
    };
    scheduled_treatments: { data: ScheduledTreatment[] };
    history: { data: TreatmentHistory[] };
}

export default function Index({
    kpis,
    scheduled_treatments,
    history,
}: PageProps) {
    const [activeTab, setActiveTab] = useState<'calendar' | 'history'>(
        'calendar',
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
            <Head title="Santé & Prophylaxie" />

            <div className="min-h-screen bg-stone-50 pb-12 text-stone-900">
                {/* HEADER */}
                <div className="border-b border-stone-200 bg-white px-6 pt-8 pb-6 shadow-sm md:px-8">
                    <div className="mx-auto flex max-w-7xl flex-col justify-between gap-4 md:flex-row md:items-center">
                        <div className="flex items-start gap-4">
                            <div className="mt-1 rounded-2xl border border-amber-200 bg-amber-50 p-3 text-amber-500 shadow-sm">
                                <Syringe className="h-8 w-8" />
                            </div>
                            <div>
                                <h1 className="text-3xl font-bold text-stone-900">
                                    Santé & Prophylaxie
                                </h1>
                                <p className="mt-1 font-medium text-stone-500">
                                    Suivi médical et exécution du calendrier
                                    prophylactique.
                                </p>
                            </div>
                        </div>
                        <div>
                            {/* Optional global action, e.g., register new treatment */}
                        </div>
                    </div>
                </div>

                <div className="mx-auto mt-8 max-w-7xl space-y-8 px-6 md:px-8">
                    {/* KPIs GRID */}
                    <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        <div className="flex items-start justify-between rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                            <div>
                                <p className="mb-1 text-sm font-medium text-stone-500">
                                    Soins à venir
                                </p>
                                <h3 className="text-3xl font-bold text-stone-900">
                                    {kpis.upcoming_count}
                                </h3>
                            </div>
                            <div className="flex h-12 w-12 items-center justify-center rounded-xl border border-stone-200 bg-stone-50">
                                <Calendar className="h-6 w-6 text-stone-600" />
                            </div>
                        </div>

                        <div className="flex items-start justify-between rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                            <div>
                                <p className="mb-1 text-sm font-medium text-stone-500">
                                    Soins en retard
                                </p>
                                <h3
                                    className={`text-3xl font-bold ${kpis.missed_count > 0 ? 'text-red-600' : 'text-stone-900'}`}
                                >
                                    {kpis.missed_count}
                                </h3>
                            </div>
                            <div
                                className={`flex h-12 w-12 items-center justify-center rounded-xl border ${kpis.missed_count > 0 ? 'border-red-200 bg-red-50' : 'border-stone-200 bg-stone-50'}`}
                            >
                                <AlertTriangle
                                    className={`h-6 w-6 ${kpis.missed_count > 0 ? 'text-red-500' : 'text-stone-600'}`}
                                />
                            </div>
                        </div>

                        <div className="flex items-start justify-between rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                            <div>
                                <p className="mb-1 text-sm font-medium text-stone-500">
                                    En attente d'approbation
                                </p>
                                <h3
                                    className={`text-3xl font-bold ${kpis.pending_approval_count > 0 ? 'text-amber-600' : 'text-stone-900'}`}
                                >
                                    {kpis.pending_approval_count}
                                </h3>
                            </div>
                            <div
                                className={`flex h-12 w-12 items-center justify-center rounded-xl border ${kpis.pending_approval_count > 0 ? 'border-amber-200 bg-amber-50' : 'border-stone-200 bg-stone-50'}`}
                            >
                                <Clock
                                    className={`h-6 w-6 ${kpis.pending_approval_count > 0 ? 'text-amber-500' : 'text-stone-600'}`}
                                />
                            </div>
                        </div>

                        <div className="flex items-start justify-between rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                            <div>
                                <p className="mb-1 text-sm font-medium text-stone-500">
                                    Coût mensuel
                                </p>
                                <h3 className="text-3xl font-bold text-stone-900">
                                    {formatCurrency(kpis.monthly_cost)}
                                </h3>
                            </div>
                            <div className="flex h-12 w-12 items-center justify-center rounded-xl border border-emerald-200 bg-emerald-50">
                                <Banknote className="h-6 w-6 text-emerald-500" />
                            </div>
                        </div>
                    </div>

                    {/* TABS ZONE */}
                    <div className="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
                        <div className="flex overflow-x-auto border-b border-stone-200 bg-stone-50 px-4 pt-4">
                            <button
                                onClick={() => setActiveTab('calendar')}
                                className={`flex items-center gap-2 border-b-2 px-5 py-3 text-sm font-medium transition-colors ${
                                    activeTab === 'calendar'
                                        ? 'rounded-t-xl border-amber-500 bg-white text-amber-700'
                                        : 'border-transparent text-stone-500 hover:border-stone-300 hover:text-stone-700'
                                }`}
                            >
                                <Calendar className="h-4 w-4" /> Calendrier des
                                Soins (À faire)
                            </button>

                            <button
                                onClick={() => setActiveTab('history')}
                                className={`flex items-center gap-2 border-b-2 px-5 py-3 text-sm font-medium transition-colors ${
                                    activeTab === 'history'
                                        ? 'rounded-t-xl border-amber-500 bg-white text-amber-700'
                                        : 'border-transparent text-stone-500 hover:border-stone-300 hover:text-stone-700'
                                }`}
                            >
                                <Clock className="h-4 w-4" /> Historique &
                                Traitements
                            </button>
                        </div>

                        <div className="p-0">
                            {activeTab === 'calendar' && (
                                <div className="overflow-x-auto">
                                    <table className="w-full text-left text-sm">
                                        <thead className="border-b border-stone-200 bg-stone-50 text-xs text-stone-500 uppercase">
                                            <tr>
                                                <th
                                                    scope="col"
                                                    className="px-6 py-4 font-medium"
                                                >
                                                    Bande
                                                </th>
                                                <th
                                                    scope="col"
                                                    className="px-6 py-4 font-medium"
                                                >
                                                    Soin Prévu
                                                </th>
                                                <th
                                                    scope="col"
                                                    className="px-6 py-4 font-medium"
                                                >
                                                    Méthode
                                                </th>
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
                                                    Statut
                                                </th>
                                                <th
                                                    scope="col"
                                                    className="px-6 py-4 text-right font-medium"
                                                >
                                                    Action
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody className="divide-y divide-stone-100 text-stone-700">
                                            {scheduled_treatments.data.length >
                                            0 ? (
                                                scheduled_treatments.data.map(
                                                    (treatment) => (
                                                        <tr
                                                            key={treatment.id}
                                                            className="transition-colors hover:bg-stone-50/50"
                                                        >
                                                            <td className="px-6 py-4 font-medium whitespace-nowrap text-stone-900">
                                                                {
                                                                    treatment.flock_name
                                                                }
                                                            </td>
                                                            <td className="px-6 py-4 font-medium">
                                                                {
                                                                    treatment.step_name
                                                                }
                                                            </td>
                                                            <td className="px-6 py-4">
                                                                {
                                                                    treatment.method
                                                                }
                                                            </td>
                                                            <td className="px-6 py-4 whitespace-nowrap">
                                                                {
                                                                    treatment.scheduled_date_formatted
                                                                }
                                                            </td>
                                                            <td className="px-6 py-4 whitespace-nowrap">
                                                                <span
                                                                    className={`inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ${
                                                                        treatment.status ===
                                                                        'missed'
                                                                            ? 'border border-red-200 bg-red-50 text-red-700'
                                                                            : 'border border-amber-200 bg-amber-50 text-amber-700'
                                                                    }`}
                                                                >
                                                                    {treatment.status ===
                                                                    'missed'
                                                                        ? 'En retard'
                                                                        : 'En attente'}
                                                                    {treatment.status ===
                                                                        'missed' &&
                                                                    treatment.days_overdue
                                                                        ? ` (${treatment.days_overdue}j)`
                                                                        : ''}
                                                                </span>
                                                            </td>
                                                            <td className="px-6 py-4 text-right whitespace-nowrap">
                                                                <Link
                                                                    href={scheduledTreatmentsExecute(
                                                                        {
                                                                            treatment:
                                                                                treatment.id,
                                                                        },
                                                                    )}
                                                                    className="inline-flex items-center gap-1.5 rounded-lg bg-amber-500 px-3 py-1.5 text-xs font-medium text-white shadow-sm transition-colors hover:bg-amber-600"
                                                                >
                                                                    <PlusCircle className="h-3.5 w-3.5" />{' '}
                                                                    Exécuter le
                                                                    soin
                                                                </Link>
                                                            </td>
                                                        </tr>
                                                    ),
                                                )
                                            ) : (
                                                <tr>
                                                    <td
                                                        colSpan={6}
                                                        className="px-6 py-12 text-center text-stone-500"
                                                    >
                                                        Aucun soin prévu dans le
                                                        calendrier.
                                                    </td>
                                                </tr>
                                            )}
                                        </tbody>
                                    </table>
                                </div>
                            )}

                            {activeTab === 'history' && (
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
                                                    Bande
                                                </th>
                                                <th
                                                    scope="col"
                                                    className="px-6 py-4 font-medium"
                                                >
                                                    Type
                                                </th>
                                                <th
                                                    scope="col"
                                                    className="px-6 py-4 font-medium"
                                                >
                                                    Vétérinaire
                                                </th>
                                                <th
                                                    scope="col"
                                                    className="px-6 py-4 font-medium"
                                                >
                                                    Coût
                                                </th>
                                                <th
                                                    scope="col"
                                                    className="px-6 py-4 font-medium"
                                                >
                                                    Statut
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody className="divide-y divide-stone-100 text-stone-700">
                                            {history.data.length > 0 ? (
                                                history.data.map((item) => (
                                                    <tr
                                                        key={item.id}
                                                        className="transition-colors hover:bg-stone-50/50"
                                                    >
                                                        <td className="px-6 py-4 font-medium whitespace-nowrap text-stone-900">
                                                            {
                                                                item.treatment_date_formatted
                                                            }
                                                        </td>
                                                        <td className="px-6 py-4 font-medium whitespace-nowrap text-stone-900">
                                                            {item.flock_name}
                                                        </td>
                                                        <td className="px-6 py-4">
                                                            {item.type}
                                                        </td>
                                                        <td className="px-6 py-4">
                                                            {item.veterinarian ||
                                                                '-'}
                                                        </td>
                                                        <td className="px-6 py-4 font-medium whitespace-nowrap text-stone-900">
                                                            {formatCurrency(
                                                                item.cost,
                                                            )}
                                                        </td>
                                                        <td className="px-6 py-4 whitespace-nowrap">
                                                            <span
                                                                className={`inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ${
                                                                    item.status ===
                                                                    'executed'
                                                                        ? 'border border-emerald-200 bg-emerald-50 text-emerald-700'
                                                                        : item.status ===
                                                                            'approved'
                                                                          ? 'border border-blue-200 bg-blue-50 text-blue-700'
                                                                          : 'border border-stone-200 bg-stone-100 text-stone-700'
                                                                }`}
                                                            >
                                                                {item.status ===
                                                                'executed'
                                                                    ? 'Exécuté'
                                                                    : item.status ===
                                                                        'approved'
                                                                      ? 'Validé'
                                                                      : 'Brouillon'}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                ))
                                            ) : (
                                                <tr>
                                                    <td
                                                        colSpan={6}
                                                        className="px-6 py-12 text-center text-stone-500"
                                                    >
                                                        Aucun historique de
                                                        traitement trouvé.
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
