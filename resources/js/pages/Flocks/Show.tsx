import { usePage, router, Head, Link } from '@inertiajs/react';
import {
    ChevronLeft,
    Edit2,
    Send,
    CheckCircle,
    XCircle,
    MapPin,
    Calendar,
    Activity,
    Info,
    TrendingUp,
    NotebookTabs,
} from 'lucide-react';
import React, { useState } from 'react';
import FlockProfitability from '@/components/Flocks/FlockProfitability';
import type { FlockStatus } from '@/components/Flocks/FlockStatusBadge';
import FlockStatusBadge from '@/components/Flocks/FlockStatusBadge';
import {
    flocksIndex,
    flocksEdit,
    flocksSubmit,
    flocksApprove,
    flocksReject,
} from '@/routes';

// ─────────────────────────────────────────────
// Types
// ─────────────────────────────────────────────

interface Permissions {
    view: boolean;
    update: boolean;
    delete: boolean;
    submit: boolean;
    approve: boolean;
    reject: boolean;
    end: boolean;
}

interface Features {
    has_eggs: boolean;
    has_gmq: boolean;
    is_breeding: boolean;
}

interface Stats {
    mortality_rate?: number;
    total_losses?: number;
    total_eggs?: number;
    avg_eggs_per_day?: number;
    egg_efficiency?: number;
    gmq?: number;
    ic?: number;
    born_alive?: number;
    stillborn?: number;
    weaned?: number;
    weaning_rate?: number;
    litters_per_year?: number;
}

interface FlockResourceData {
    id: number;
    name: string;
    animal_type_id: number;
    animal_type_code: string;
    animal_type_name: string;
    building_id: number;
    building: string;
    arrival_date: string;
    arrival_date_formatted: string;
    initial_quantity: number;
    current_quantity: number;
    status: FlockStatus;
    is_legacy: boolean;
    standard_mortality_rate: number;
    notes: string | null;
    creator: string;
    approver: string | null;
    approved_at: string | null;
    ended_at: string | null;
    end_reason: string | null;
    created_at: string;
    features: Features;
    stats?: Stats;
    can: Permissions;
    daily_records?: any[];
    weight_records?: any[];
    breeding_events?: any[];
}

export default function Show() {
    const { props } = usePage();
    const flock = props.flock as FlockResourceData;
    const financial_analysis = props.financial_analysis as any;

    const [activeTab, setActiveTab] = useState<
        'overview' | 'profitability' | 'daily'
    >('overview');

    const { can } = flock;

    // Actions
    const handleSubmit = () => {
        if (
            confirm(
                'Voulez-vous vraiment soumettre cette bande pour validation ?',
            )
        ) {
            router.post(flocksSubmit({ flock: flock.id }));
        }
    };

    const handleApprove = () => {
        if (confirm('Confirmez-vous la validation de cette bande ?')) {
            router.post(flocksApprove({ flock: flock.id }));
        }
    };

    const handleReject = () => {
        const reason = prompt('Motif du rejet (optionnel) :');

        if (reason !== null) {
            router.post(flocksReject({ flock: flock.id }), { reason });
        }
    };

    return (
        <>
            <Head title={`Bande : ${flock.name}`} />

            <div className="min-h-screen bg-stone-50 pb-12 text-stone-900">
                {/* EN-TÊTE */}
                <div className="border-b border-stone-200 bg-white px-6 pt-8 pb-6 shadow-sm md:px-8">
                    <div className="mx-auto flex max-w-7xl flex-col justify-between gap-4 md:flex-row md:items-center">
                        <div className="flex flex-col gap-3">
                            <Link
                                href={flocksIndex()}
                                className="flex w-fit items-center text-sm font-medium text-stone-500 transition-colors hover:text-stone-800"
                            >
                                <ChevronLeft className="mr-1 h-4 w-4" />
                                Retour aux bandes
                            </Link>

                            <div className="flex items-center gap-4">
                                <h1 className="text-3xl font-bold text-stone-900">
                                    {flock.name}
                                </h1>
                                <FlockStatusBadge status={flock.status} />
                            </div>

                            <div className="mt-1 flex flex-wrap items-center gap-4 text-sm text-stone-600">
                                <span className="flex items-center">
                                    <MapPin className="mr-1.5 h-4 w-4 text-stone-400" />
                                    {flock.building}
                                </span>
                                <span className="flex items-center">
                                    <Calendar className="mr-1.5 h-4 w-4 text-stone-400" />
                                    Arrivée : {flock.arrival_date_formatted}
                                </span>
                                <span className="flex items-center">
                                    <Activity className="mr-1.5 h-4 w-4 text-stone-400" />
                                    {flock.animal_type_name}
                                </span>
                            </div>
                        </div>

                        {/* ACTIONS RAPIDES */}
                        <div className="flex items-center gap-3">
                            {can.update && (
                                <Link
                                    href={flocksEdit({ flock: flock.id })}
                                    className="flex items-center gap-2 rounded-xl border border-stone-200 bg-stone-100 px-4 py-2 font-medium text-stone-700 shadow-sm transition-colors hover:bg-stone-200"
                                >
                                    <Edit2 className="h-4 w-4" /> Modifier
                                </Link>
                            )}

                            {can.submit && (
                                <button
                                    onClick={handleSubmit}
                                    className="flex items-center gap-2 rounded-xl bg-amber-500 px-4 py-2 font-medium text-white shadow-sm shadow-amber-500/20 transition-colors hover:bg-amber-600"
                                >
                                    <Send className="h-4 w-4" /> Soumettre
                                </button>
                            )}

                            {can.reject && (
                                <button
                                    onClick={handleReject}
                                    className="flex items-center gap-2 rounded-xl border border-red-200 bg-red-100 px-4 py-2 font-medium text-red-700 transition-colors hover:bg-red-200"
                                >
                                    <XCircle className="h-4 w-4" /> Rejeter
                                </button>
                            )}

                            {can.approve && (
                                <button
                                    onClick={handleApprove}
                                    className="flex items-center gap-2 rounded-xl bg-emerald-500 px-4 py-2 font-medium text-white shadow-sm shadow-emerald-500/20 transition-colors hover:bg-emerald-600"
                                >
                                    <CheckCircle className="h-4 w-4" />{' '}
                                    Approuver
                                </button>
                            )}
                        </div>
                    </div>
                </div>

                <div className="mx-auto mt-8 max-w-7xl space-y-8 px-6 md:px-8">
                    {/* CARTES KPIs */}
                    <div className="grid grid-cols-1 gap-6 md:grid-cols-3">
                        <div className="flex items-start justify-between rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                            <div>
                                <p className="mb-1 text-sm font-medium text-stone-500">
                                    Effectif Actuel
                                </p>
                                <h3 className="text-3xl font-bold text-stone-900">
                                    {flock.current_quantity.toLocaleString(
                                        'fr-FR',
                                    )}
                                </h3>
                                <p className="mt-2 text-xs font-medium text-stone-400">
                                    Sur{' '}
                                    {flock.initial_quantity.toLocaleString(
                                        'fr-FR',
                                    )}{' '}
                                    à l'arrivée
                                </p>
                            </div>
                            <div className="flex h-12 w-12 items-center justify-center rounded-xl border border-stone-200 bg-stone-100">
                                <Activity className="h-6 w-6 text-stone-600" />
                            </div>
                        </div>

                        <div className="flex items-start justify-between rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                            <div>
                                <p className="mb-1 text-sm font-medium text-stone-500">
                                    Taux de Mortalité
                                </p>
                                <h3
                                    className={`text-3xl font-bold ${(flock.stats?.mortality_rate || 0) > flock.standard_mortality_rate ? 'text-red-600' : 'text-stone-900'}`}
                                >
                                    {flock.stats?.mortality_rate || 0}%
                                </h3>
                                <p className="mt-2 text-xs font-medium text-stone-400">
                                    Norme maximale :{' '}
                                    {flock.standard_mortality_rate}%
                                </p>
                            </div>
                            <div
                                className={`flex h-12 w-12 items-center justify-center rounded-xl border ${(flock.stats?.mortality_rate || 0) > flock.standard_mortality_rate ? 'border-red-200 bg-red-50' : 'border-stone-200 bg-stone-100'}`}
                            >
                                <TrendingUp
                                    className={`h-6 w-6 ${(flock.stats?.mortality_rate || 0) > flock.standard_mortality_rate ? 'text-red-500' : 'text-stone-600'}`}
                                />
                            </div>
                        </div>

                        <div className="flex items-start justify-between rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                            <div>
                                <p className="mb-1 text-sm font-medium text-stone-500">
                                    Création & Validation
                                </p>
                                <div className="mt-2 space-y-2">
                                    <p className="text-sm font-medium text-stone-800">
                                        <span className="font-normal text-stone-400">
                                            Créé par :{' '}
                                        </span>
                                        {flock.creator}
                                    </p>
                                    {flock.approver ? (
                                        <p className="text-sm font-medium text-emerald-700">
                                            <span className="font-normal text-stone-400">
                                                Approuvé par :{' '}
                                            </span>
                                            {flock.approver}{' '}
                                            <span className="ml-1 text-xs text-emerald-600/70">
                                                ({flock.approved_at})
                                            </span>
                                        </p>
                                    ) : (
                                        <p className="text-sm font-medium text-amber-600">
                                            En attente de validation
                                        </p>
                                    )}
                                </div>
                            </div>
                            <div className="flex h-12 w-12 items-center justify-center rounded-xl border border-stone-200 bg-stone-100">
                                <Info className="h-6 w-6 text-stone-600" />
                            </div>
                        </div>
                    </div>

                    {/* ZONE D'ONGLETS */}
                    <div className="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
                        <div className="flex overflow-x-auto border-b border-stone-200 bg-stone-50 px-4 pt-4">
                            <button
                                onClick={() => setActiveTab('overview')}
                                className={`flex items-center gap-2 border-b-2 px-5 py-3 text-sm font-medium transition-colors ${
                                    activeTab === 'overview'
                                        ? 'rounded-t-xl border-amber-500 bg-white text-amber-700'
                                        : 'border-transparent text-stone-500 hover:border-stone-300 hover:text-stone-700'
                                }`}
                            >
                                <Info className="h-4 w-4" /> Vue d'ensemble
                            </button>

                            <button
                                onClick={() => setActiveTab('profitability')}
                                className={`flex items-center gap-2 border-b-2 px-5 py-3 text-sm font-medium transition-colors ${
                                    activeTab === 'profitability'
                                        ? 'rounded-t-xl border-amber-500 bg-white text-amber-700'
                                        : 'border-transparent text-stone-500 hover:border-stone-300 hover:text-stone-700'
                                }`}
                            >
                                <TrendingUp className="h-4 w-4" /> Rentabilité
                            </button>

                            <button
                                onClick={() => setActiveTab('daily')}
                                className={`flex items-center gap-2 border-b-2 px-5 py-3 text-sm font-medium transition-colors ${
                                    activeTab === 'daily'
                                        ? 'rounded-t-xl border-amber-500 bg-white text-amber-700'
                                        : 'border-transparent text-stone-500 hover:border-stone-300 hover:text-stone-700'
                                }`}
                            >
                                <NotebookTabs className="h-4 w-4" /> Suivi
                                Quotidien
                            </button>
                        </div>

                        <div className="p-6 md:p-8">
                            {activeTab === 'overview' && (
                                <div className="space-y-6">
                                    <div>
                                        <h3 className="mb-4 text-lg font-bold text-stone-900">
                                            Observations et Notes
                                        </h3>
                                        <div className="min-h-[150px] rounded-2xl border border-stone-200 bg-stone-50 p-6 text-stone-700">
                                            {flock.notes ? (
                                                <p className="leading-relaxed whitespace-pre-wrap">
                                                    {flock.notes}
                                                </p>
                                            ) : (
                                                <p className="text-stone-400 italic">
                                                    Aucune note enregistrée pour
                                                    cette bande.
                                                </p>
                                            )}
                                        </div>
                                    </div>
                                </div>
                            )}

                            {activeTab === 'profitability' && (
                                <div>
                                    {financial_analysis ? (
                                        <FlockProfitability
                                            data={financial_analysis}
                                            features={flock.features}
                                        />
                                    ) : (
                                        <div className="rounded-2xl border border-stone-200 bg-stone-50 py-16 text-center">
                                            <TrendingUp className="mx-auto mb-4 h-12 w-12 text-stone-300" />
                                            <h4 className="mb-1 font-bold text-stone-900">
                                                Données financières
                                                indisponibles
                                            </h4>
                                            <p className="text-sm text-stone-500">
                                                Les indicateurs de rentabilité
                                                s'afficheront ici une fois les
                                                transactions enregistrées.
                                            </p>
                                        </div>
                                    )}
                                </div>
                            )}

                            {activeTab === 'daily' && (
                                <div className="rounded-2xl border border-stone-200 bg-stone-50 py-16 text-center">
                                    <NotebookTabs className="mx-auto mb-4 h-12 w-12 text-stone-300" />
                                    <p className="font-medium text-stone-500">
                                        Le module de suivi quotidien sera chargé
                                        ici.
                                    </p>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
