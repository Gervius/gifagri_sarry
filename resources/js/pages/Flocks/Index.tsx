<<<<<<< HEAD
import { Head, router, usePage } from '@inertiajs/react';
import {
    Plus,
    Search,
    ChevronLeft,
    ChevronRight,
    MoreHorizontal,
    Eye,
    ClipboardList,
    CheckCircle,
    XCircle,
    Send,
    Archive,
    Edit2,
    Trash2,
    MapPin,
    Calendar,
    Filter,
} from 'lucide-react';
import { useState, useEffect, useMemo } from 'react';
import AppLayout from '@/layouts/app-layout';

import {
    flocksStore,
    flocksSubmit,
    flocksReject,
    flocksDestroy,
    flocksEnd,
    generation,
    flocksShow,
    flocksEdit,
} from '@/routes';

// Types
=======
import { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import { 
  Plus, Search, Eye, CheckCircle, Edit2, Trash2, MapPin, Layers
} from 'lucide-react';
import { 
  flocksCreate, flocksShow, flocksEdit, flocksSubmit, flocksDestroy 
} from '@/routes';

>>>>>>> f73b061 (modified:   app/Http/Controllers/DailyRecordController.php)
type FlockStatus = 'draft' | 'pending' | 'active' | 'rejected' | 'completed';

interface FlockPermissions {
    can_view: boolean;
    can_edit: boolean;
    can_delete: boolean;
    can_submit: boolean;
    can_approve: boolean;
    can_reject: boolean;
    can_end: boolean;
}

interface Flock extends FlockPermissions {
    id: number;
    name: string;
    animal_type_code: string;
    building: string;
    arrival_date_formatted: string;
    initial_quantity: number;
    current_quantity: number;
    status: FlockStatus;
    standard_mortality_rate: number | null;
    features: { has_eggs: boolean; has_gmq: boolean; is_breeding: boolean };
}

interface PaginatedFlocks {
    data: Flock[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: { url: string | null; label: string; active: boolean }[];
}

<<<<<<< HEAD
interface Building {
    id: number;
    name: string;
}

interface PageProps {
    flocks: PaginatedFlocks;
    buildings: Building[];
    filters: { search?: string; status?: string; building_id?: string };
    flash?: { success?: string; error?: string };
}

// Constantes d'affichage
const STATUS_META: Record<FlockStatus, { label: string; classes: string }> = {
    draft: {
        label: 'Brouillon',
        classes: 'bg-slate-100 text-slate-700 border-slate-200',
    },
    pending: {
        label: 'En attente',
        classes: 'bg-amber-100 text-amber-700 border-amber-200',
    },
    active: {
        label: 'Actif',
        classes: 'bg-emerald-100 text-emerald-700 border-emerald-200',
    },
    rejected: {
        label: 'Rejeté',
        classes: 'bg-red-100 text-red-600 border-red-200',
    },
    completed: {
        label: 'Terminé',
        classes: 'bg-stone-100 text-stone-500 border-stone-200',
    },
};

const SPECIES_BADGE: Record<string, string> = {
    PP: 'Pondeuse',
    PC: 'Chair',
    POR: 'Porc',
};

export default function Index({ flocks, buildings, filters }: PageProps) {
    //const { addToast } = useToasts();
    const { flash } = usePage().props as any;

    const [search, setSearch] = useState(filters.search || '');
    const [statusFilter, setStatusFilter] = useState(filters.status || '');
    const [buildingFilter, setBuildingFilter] = useState(
        filters.building_id || '',
    );

    useEffect(() => {}, [flash]);

    const applyFilters = () => {
        router.get(
            generation.url(),
            {
                search: search || undefined,
                status: statusFilter || undefined,
                building_id: buildingFilter || undefined,
            },
            { preserveState: true, replace: true },
        );
    };

    const resetFilters = () => {
        setSearch('');
        setStatusFilter('');
        setBuildingFilter('');
        router.get(generation.url(), {}, { replace: true });
    };

    const handleDelete = (flock: Flock) => {
        if (!confirm(`Supprimer définitivement le lot "${flock.name}" ?`)) {
            return;
        }

        router.delete(flocksDestroy.url(flock.id), {});
    };

    const handleSubmit = (flock: Flock) => {
        router.patch(flocksSubmit.url(flock.id), {}, {});
    };

    const handleApprove = (flock: Flock) => {};

    const handleReject = (flock: Flock) => {
        const reason = prompt('Motif du rejet :');

        if (reason === null) {
            return;
        }

        router.patch(flocksReject.url(flock.id), {});
    };

    const handleEnd = (flock: Flock) => {
        if (
            !confirm(
                `Terminer le lot "${flock.name}" ? Cette action est irréversible.`,
            )
        ) {
            return;
        }

        router.post(flocksEnd.url(flock.id), {}, {});
    };

    return (
        <AppLayout>
            <Head title="Gestion des lots" />

            <div className="min-h-screen bg-stone-50">
                {/* Header */}
                <div className="border-b border-stone-200 bg-white px-6 py-5 sm:px-8">
                    <div className="mx-auto flex max-w-7xl flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h1 className="text-2xl font-semibold text-stone-900">
                                Gestion des lots
                            </h1>
                            <p className="mt-0.5 text-sm text-stone-500">
                                {flocks.total} lot
                                {flocks.total !== 1 ? 's' : ''} au total
                            </p>
                        </div>
                        <button
                            type="button"
                            onClick={() => router.get(flocksStore.url())}
                            className="inline-flex items-center justify-center gap-2 rounded-lg bg-amber-500 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-amber-600"
                        >
                            <Plus className="h-4 w-4" />
                            Nouveau lot
                        </button>
                    </div>
                </div>

                <div className="mx-auto max-w-7xl space-y-6 px-6 py-6 sm:px-8">
                    {/* Filtres */}
                    <div className="flex flex-wrap items-end gap-3 rounded-xl border border-stone-200 bg-white p-4">
                        <div className="min-w-[200px] flex-1">
                            <label className="mb-1.5 block text-xs font-medium text-stone-500">
                                Recherche
                            </label>
                            <div className="relative">
                                <Search className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-stone-400" />
                                <input
                                    type="text"
                                    value={search}
                                    onChange={(e) => setSearch(e.target.value)}
                                    onKeyDown={(e) =>
                                        e.key === 'Enter' && applyFilters()
                                    }
                                    placeholder="Nom du lot..."
                                    className="w-full rounded-lg border border-stone-200 bg-white py-2 pr-4 pl-9 text-sm focus:ring-2 focus:ring-amber-400 focus:outline-none"
                                />
                            </div>
                        </div>

                        <div className="min-w-[150px]">
                            <label className="mb-1.5 block text-xs font-medium text-stone-500">
                                Statut
                            </label>
                            <select
                                value={statusFilter}
                                onChange={(e) =>
                                    setStatusFilter(e.target.value)
                                }
                                className="w-full rounded-lg border border-stone-200 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-amber-400 focus:outline-none"
                            >
                                <option value="">Tous</option>
                                {Object.entries(STATUS_META).map(
                                    ([key, { label }]) => (
                                        <option key={key} value={key}>
                                            {label}
                                        </option>
                                    ),
                                )}
                            </select>
                        </div>

                        <div className="min-w-[180px]">
                            <label className="mb-1.5 block text-xs font-medium text-stone-500">
                                Bâtiment
                            </label>
                            <select
                                value={buildingFilter}
                                onChange={(e) =>
                                    setBuildingFilter(e.target.value)
                                }
                                className="w-full rounded-lg border border-stone-200 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-amber-400 focus:outline-none"
                            >
                                <option value="">Tous</option>
                                {buildings.map((b) => (
                                    <option key={b.id} value={b.id}>
                                        {b.name}
                                    </option>
                                ))}
                            </select>
                        </div>

                        <div className="flex gap-2">
                            <button
                                type="button"
                                onClick={applyFilters}
                                className="flex items-center gap-1.5 rounded-lg bg-stone-900 px-4 py-2 text-sm text-white transition-colors hover:bg-stone-800"
                            >
                                <Filter className="h-4 w-4" />
                                Filtrer
                            </button>
                            {(search || statusFilter || buildingFilter) && (
                                <button
                                    type="button"
                                    onClick={resetFilters}
                                    className="rounded-lg border border-stone-200 px-4 py-2 text-sm text-stone-600 transition-colors hover:bg-stone-50"
                                >
                                    Réinitialiser
                                </button>
                            )}
                        </div>
                    </div>

                    {/* Tableau */}
                    <div className="overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm">
                        <div className="overflow-x-auto">
                            <table className="w-full text-sm">
                                <thead className="border-b border-stone-200 bg-stone-50">
                                    <tr>
                                        <th className="px-5 py-3.5 text-left text-xs font-semibold tracking-wider text-stone-500 uppercase">
                                            Lot
                                        </th>
                                        <th className="px-5 py-3.5 text-left text-xs font-semibold tracking-wider text-stone-500 uppercase">
                                            Bâtiment
                                        </th>
                                        <th className="px-5 py-3.5 text-left text-xs font-semibold tracking-wider text-stone-500 uppercase">
                                            Arrivée
                                        </th>
                                        <th className="px-5 py-3.5 text-left text-xs font-semibold tracking-wider text-stone-500 uppercase">
                                            Effectif
                                        </th>
                                        <th className="px-5 py-3.5 text-left text-xs font-semibold tracking-wider text-stone-500 uppercase">
                                            Statut
                                        </th>
                                        <th className="px-5 py-3.5 text-right text-xs font-semibold tracking-wider text-stone-500 uppercase">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-stone-100">
                                    {flocks.data.length === 0 ? (
                                        <tr>
                                            <td
                                                colSpan={6}
                                                className="px-5 py-12 text-center text-stone-400"
                                            >
                                                Aucun lot trouvé.
                                            </td>
                                        </tr>
                                    ) : (
                                        flocks.data.map((flock) => (
                                            <FlockRow
                                                key={flock.id}
                                                flock={flock}
                                                onDelete={handleDelete}
                                                onSubmit={handleSubmit}
                                                onApprove={handleApprove}
                                                onReject={handleReject}
                                                onEnd={handleEnd}
                                            />
                                        ))
                                    )}
                                </tbody>
                            </table>
                        </div>

                        {/* Pagination */}
                        {flocks.last_page > 1 && (
                            <div className="flex items-center justify-between border-t border-stone-100 px-5 py-4">
                                <span className="text-sm text-stone-500">
                                    Page {flocks.current_page} sur{' '}
                                    {flocks.last_page} · {flocks.total}{' '}
                                    résultats
                                </span>
                                <div className="flex gap-1">
                                    {(() => {
                                        const prevLink = flocks.links.find(
                                            (link) =>
                                                link.label.includes(
                                                    'Précédent',
                                                ) ||
                                                link.label ===
                                                    '&laquo; Previous',
                                        );
                                        const nextLink = flocks.links.find(
                                            (link) =>
                                                link.label.includes(
                                                    'Suivant',
                                                ) ||
                                                link.label === 'Next &raquo;',
                                        );

                                        return (
                                            <>
                                                <button
                                                    type="button"
                                                    disabled={
                                                        !prevLink?.url ||
                                                        flocks.current_page ===
                                                            1
                                                    }
                                                    onClick={() =>
                                                        prevLink?.url &&
                                                        router.get(prevLink.url)
                                                    }
                                                    className="rounded p-1.5 hover:bg-stone-100 disabled:cursor-not-allowed disabled:opacity-30"
                                                >
                                                    <ChevronLeft className="h-4 w-4" />
                                                </button>
                                                <button
                                                    type="button"
                                                    disabled={
                                                        !nextLink?.url ||
                                                        flocks.current_page ===
                                                            flocks.last_page
                                                    }
                                                    onClick={() =>
                                                        nextLink?.url &&
                                                        router.get(nextLink.url)
                                                    }
                                                    className="rounded p-1.5 hover:bg-stone-100 disabled:cursor-not-allowed disabled:opacity-30"
                                                >
                                                    <ChevronRight className="h-4 w-4" />
                                                </button>
                                            </>
                                        );
                                    })()}
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}

// Composant ligne – les imports manquants ont été ajoutés
function FlockRow({
    flock,
    onDelete,
    onSubmit,
    onApprove,
    onReject,
    onEnd,
}: {
    flock: Flock;
    onDelete: (f: Flock) => void;
    onSubmit: (f: Flock) => void;
    onApprove: (f: Flock) => void;
    onReject: (f: Flock) => void;
    onEnd: (f: Flock) => void;
}) {
    const [showActions, setShowActions] = useState(false);
    const statusMeta = STATUS_META[flock.status];
    const progress = (flock.current_quantity / flock.initial_quantity) * 100;
    const isLowStock = progress < 50;

    return (
        <tr className="transition-colors hover:bg-stone-50/80">
            <td className="px-5 py-4">
                <div className="flex items-center gap-2">
                    <span className="font-medium text-stone-900">
                        {flock.name}
                    </span>
                    {SPECIES_BADGE[flock.animal_type_code] && (
                        <span className="inline-flex items-center rounded-full bg-stone-100 px-2 py-0.5 text-[10px] font-medium text-stone-600">
                            {SPECIES_BADGE[flock.animal_type_code]}
                        </span>
                    )}
                </div>
            </td>
            <td className="px-5 py-4">
                <span className="flex items-center gap-1.5 text-stone-600">
                    <MapPin className="h-3.5 w-3.5 text-stone-400" />
                    {flock.building}
                </span>
            </td>
            <td className="px-5 py-4">
                <span className="flex items-center gap-1.5 text-stone-600">
                    <Calendar className="h-3.5 w-3.5 text-stone-400" />
                    {flock.arrival_date_formatted}
                </span>
            </td>
            <td className="px-5 py-4">
                <div className="flex flex-col">
                    <span className="font-medium text-stone-900">
                        {flock.current_quantity.toLocaleString()}
                    </span>
                    <span className="text-xs text-stone-400">
                        / {flock.initial_quantity.toLocaleString()}
                    </span>
                    <div className="mt-1 h-1.5 w-24 overflow-hidden rounded-full bg-stone-100">
                        <div
                            className={`h-full rounded-full ${isLowStock ? 'bg-amber-500' : 'bg-emerald-500'}`}
                            style={{ width: `${Math.min(progress, 100)}%` }}
                        />
                    </div>
                </div>
            </td>
            <td className="px-5 py-4">
                <span
                    className={`inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-medium ${statusMeta.classes}`}
                >
                    {statusMeta.label}
                </span>
            </td>
            <td className="px-5 py-4 text-right">
                <div className="relative flex items-center justify-end gap-1">
                    <button
                        type="button"
                        onClick={() => router.get(flocksShow.url(flock.id))}
                        className="rounded-lg p-1.5 text-stone-500 transition-colors hover:bg-indigo-50 hover:text-indigo-600"
                        title="Voir détails"
                    >
                        <Eye className="h-4 w-4" />
                    </button>

                    {flock.status === 'active' && (
                        <button
                            type="button"
                            onClick={() => {}}
                            className="rounded-lg p-1.5 text-stone-500 transition-colors hover:bg-emerald-50 hover:text-emerald-600"
                            title="Nouveau suivi"
                        >
                            <ClipboardList className="h-4 w-4" />
                        </button>
                    )}

                    {flock.can_approve && (
                        <button
                            type="button"
                            onClick={() => onApprove(flock)}
                            className="rounded-lg p-1.5 text-stone-500 transition-colors hover:bg-emerald-50 hover:text-emerald-600"
                            title="Approuver"
                        >
                            <CheckCircle className="h-4 w-4" />
                        </button>
                    )}

                    {flock.can_submit && (
                        <button
                            type="button"
                            onClick={() => onSubmit(flock)}
                            className="rounded-lg p-1.5 text-stone-500 transition-colors hover:bg-indigo-50 hover:text-indigo-600"
                            title="Soumettre"
                        >
                            <Send className="h-4 w-4" />
                        </button>
                    )}

                    {/* Menu déroulant */}
                    <div className="relative">
                        <button
                            type="button"
                            onClick={() => setShowActions(!showActions)}
                            className="rounded-lg p-1.5 text-stone-500 transition-colors hover:bg-stone-100 hover:text-stone-700"
                            title="Plus d'actions"
                        >
                            <MoreHorizontal className="h-4 w-4" />
                        </button>

                        {showActions && (
                            <>
                                <div
                                    className="fixed inset-0 z-10"
                                    onClick={() => setShowActions(false)}
                                />
                                <div className="absolute top-full right-0 z-20 mt-1 w-48 rounded-lg border border-stone-200 bg-white py-1 shadow-lg">
                                    {flock.can_edit && (
                                        <button
                                            type="button"
                                            onClick={() => {
                                                setShowActions(false);
                                                router.get(
                                                    flocksEdit.url(flock.id),
                                                );
                                            }}
                                            className="flex w-full items-center gap-2 px-4 py-2 text-left text-sm text-stone-700 hover:bg-stone-50"
                                        >
                                            <Edit2 className="h-4 w-4" />{' '}
                                            Modifier
                                        </button>
                                    )}
                                    {flock.status === 'active' &&
                                        flock.can_end && (
                                            <button
                                                type="button"
                                                onClick={() => {
                                                    setShowActions(false);
                                                    onEnd(flock);
                                                }}
                                                className="flex w-full items-center gap-2 px-4 py-2 text-left text-sm text-amber-700 hover:bg-amber-50"
                                            >
                                                <Archive className="h-4 w-4" />{' '}
                                                Terminer
                                            </button>
                                        )}
                                    {flock.can_reject && (
                                        <button
                                            type="button"
                                            onClick={() => {
                                                setShowActions(false);
                                                onReject(flock);
                                            }}
                                            className="flex w-full items-center gap-2 px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50"
                                        >
                                            <XCircle className="h-4 w-4" />{' '}
                                            Rejeter
                                        </button>
                                    )}
                                    {flock.can_delete && (
                                        <button
                                            type="button"
                                            onClick={() => {
                                                setShowActions(false);
                                                onDelete(flock);
                                            }}
                                            className="flex w-full items-center gap-2 px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50"
                                        >
                                            <Trash2 className="h-4 w-4" />{' '}
                                            Supprimer
                                        </button>
                                    )}
                                </div>
                            </>
                        )}
                    </div>
                </div>
            </td>
        </tr>
    );
}
=======
interface PageProps {
  flocks: PaginatedFlocks;
  filters: { search?: string; status?: string; building_id?: string };
  buildings: { data: { id: number; name: string }[] };
}

const statusConfig: Record<FlockStatus, { color: string; dot: string; label: string }> = {
  draft: { color: 'bg-stone-100 text-stone-700', dot: 'bg-stone-400', label: 'Brouillon' },
  pending: { color: 'bg-amber-50 text-amber-700 border-amber-200', dot: 'bg-amber-500', label: 'En attente' },
  active: { color: 'bg-emerald-50 text-emerald-700 border-emerald-200', dot: 'bg-emerald-500', label: 'Active' },
  rejected: { color: 'bg-red-50 text-red-700 border-red-200', dot: 'bg-red-500', label: 'Rejetée' },
  completed: { color: 'bg-blue-50 text-blue-700 border-blue-200', dot: 'bg-blue-500', label: 'Terminée' },
};

export default function Index({ flocks, filters }: PageProps) {
  const [searchTerm, setSearchTerm] = useState(filters.search || '');
  const [statusFilter, setStatusFilter] = useState(filters.status || '');

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    router.get(window.location.pathname, { search: searchTerm, status: statusFilter }, { preserveState: true });
  };

  const handleAction = (url: string, method: 'post' | 'delete', confirmMessage?: string) => {
    if (confirmMessage && !window.confirm(confirmMessage)) return;
    router[method](url, {}, { preserveScroll: true });
  };

  return (
    <>
      <Head title="Gestion des Bandes" />

      {/* En-tête de page optimisé UX */}
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-6">
        <div>
          <h1 className="text-3xl font-bold text-stone-900 tracking-tight flex items-center gap-3">
            <Layers className="w-8 h-8 text-amber-500" />
            Générations & Bandes
          </h1>
          <p className="text-base text-stone-500 mt-2">Gérez le cycle de vie de vos cheptels avec précision.</p>
        </div>
        <Link
          href={flocksCreate.url()}
          className="inline-flex items-center justify-center rounded-lg text-sm font-medium transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 disabled:opacity-50 bg-amber-500 text-white hover:bg-amber-600 shadow-sm hover:shadow h-11 px-6 py-2"
        >
          <Plus className="w-5 h-5 mr-2" />
          Nouvelle Bande
        </Link>
      </div>

      {/* Conteneur principal avec paddings généreux */}
      <div className="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden mb-8">
        
        {/* Barre d'outils et filtres */}
        <div className="p-6 border-b border-stone-100 bg-stone-50/50">
          <form onSubmit={handleSearch} className="flex flex-col sm:flex-row gap-4">
            <div className="relative flex-1">
              <Search className="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-stone-400" />
              <input
                type="text"
                placeholder="Rechercher une bande..."
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
                className="flex h-12 w-full rounded-xl border border-stone-200 bg-white px-4 py-2 pl-12 text-sm text-stone-900 ring-offset-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 transition-shadow"
              />
            </div>
            <select
              value={statusFilter}
              onChange={(e) => setStatusFilter(e.target.value)}
              className="flex h-12 w-full sm:w-[240px] items-center justify-between rounded-xl border border-stone-200 bg-white px-4 py-2 text-sm text-stone-900 ring-offset-white focus:outline-none focus:ring-2 focus:ring-amber-500 transition-shadow cursor-pointer"
            >
              <option value="">Tous les statuts</option>
              <option value="draft">Brouillons</option>
              <option value="active">Actives</option>
              <option value="completed">Terminées</option>
            </select>
            <button type="submit" className="hidden" />
          </form>
        </div>

        {/* Table de données */}
        <div className="overflow-x-auto">
          <table className="w-full text-sm text-left whitespace-nowrap">
            <thead className="text-xs text-stone-500 uppercase bg-white border-b border-stone-200">
              <tr>
                <th className="px-6 py-5 font-semibold tracking-wider">Identifiant & Bâtiment</th>
                <th className="px-6 py-5 font-semibold tracking-wider">Effectif</th>
                <th className="px-6 py-5 font-semibold tracking-wider">Date de réception</th>
                <th className="px-6 py-5 font-semibold tracking-wider">Statut</th>
                <th className="px-6 py-5 font-semibold tracking-wider text-right">Actions</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-stone-100">
              {flocks.data.length === 0 ? (
                <tr>
                  <td colSpan={5} className="px-6 py-12 text-center">
                    <div className="flex flex-col items-center justify-center text-stone-400">
                      <Layers className="w-12 h-12 mb-4 opacity-50" />
                      <p className="text-base font-medium text-stone-600">Aucune bande trouvée</p>
                      <p className="text-sm mt-1">Essayez de modifier vos filtres de recherche.</p>
                    </div>
                  </td>
                </tr>
              ) : (
                flocks.data.map((flock) => (
                  <tr key={flock.id} className="hover:bg-stone-50/80 transition-colors group">
                    <td className="px-6 py-5">
                      <div className="font-semibold text-stone-900 text-base">{flock.name}</div>
                      <div className="flex items-center text-sm text-stone-500 mt-1.5">
                        <MapPin className="w-3.5 h-3.5 mr-1.5 text-stone-400" />
                        {flock.building}
                      </div>
                    </td>
                    <td className="px-6 py-5">
                      <div className="font-semibold text-stone-900 text-base">{flock.current_quantity.toLocaleString()}</div>
                      <div className="text-sm text-stone-500 mt-1.5">sur {flock.initial_quantity.toLocaleString()}</div>
                    </td>
                    <td className="px-6 py-5 text-stone-600 font-medium">
                      {flock.arrival_date_formatted}
                    </td>
                    <td className="px-6 py-5">
                      <span className={`inline-flex items-center px-3 py-1 rounded-full text-xs font-medium border ${statusConfig[flock.status].color}`}>
                        <span className={`w-1.5 h-1.5 rounded-full mr-2 ${statusConfig[flock.status].dot}`}></span>
                        {statusConfig[flock.status].label}
                      </span>
                    </td>
                    <td className="px-6 py-5 text-right">
                      <div className="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        {flock.can_view && (
                          <Link
                            href={flocksShow.url(flock.id)}
                            className="p-2 text-stone-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all"
                            title="Voir les détails"
                          >
                            <Eye className="w-5 h-5" />
                          </Link>
                        )}
                        {flock.can_edit && (
                          <Link
                            href={flocksEdit.url(flock.id)}
                            className="p-2 text-stone-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all"
                            title="Modifier"
                          >
                            <Edit2 className="w-5 h-5" />
                          </Link>
                        )}
                        {flock.can_submit && (
                          <button
                            onClick={() => handleAction(flocksSubmit.url(flock.id), 'post', 'Soumettre cette bande pour approbation ?')}
                            className="p-2 text-stone-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all"
                            title="Soumettre"
                          >
                            <CheckCircle className="w-5 h-5" />
                          </button>
                        )}
                        {flock.can_delete && (
                          <button
                            onClick={() => handleAction(flocksDestroy.url(flock.id), 'delete', 'Êtes-vous sûr de vouloir supprimer cette bande ?')}
                            className="p-2 text-stone-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all"
                            title="Supprimer"
                          >
                            <Trash2 className="w-5 h-5" />
                          </button>
                        )}
                      </div>
                    </td>
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>
      </div>
    </>
  );
}
>>>>>>> f73b061 (modified:   app/Http/Controllers/DailyRecordController.php)
