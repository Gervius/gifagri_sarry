import { useState, useEffect, useMemo } from 'react';
import { Head, router, usePage } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';

import {
  Plus, Search, ChevronLeft, ChevronRight, MoreHorizontal,
  Eye, ClipboardList, CheckCircle, XCircle, Send, Archive, Edit2, Trash2,
  MapPin, Calendar, Filter
} from 'lucide-react';
import { flocksStore, flocksSubmit, flocksReject, flocksDestroy, flocksEnd, generation, flocksShow, flocksEdit } from '@/routes';


// Types
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
  draft:     { label: 'Brouillon', classes: 'bg-slate-100 text-slate-700 border-slate-200' },
  pending:   { label: 'En attente', classes: 'bg-amber-100 text-amber-700 border-amber-200' },
  active:    { label: 'Actif', classes: 'bg-emerald-100 text-emerald-700 border-emerald-200' },
  rejected:  { label: 'Rejeté', classes: 'bg-red-100 text-red-600 border-red-200' },
  completed: { label: 'Terminé', classes: 'bg-stone-100 text-stone-500 border-stone-200' },
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
  const [buildingFilter, setBuildingFilter] = useState(filters.building_id || '');

  useEffect(() => {
    
  }, [flash]);

  const applyFilters = () => {
    router.get(generation.url(), {
      search: search || undefined,
      status: statusFilter || undefined,
      building_id: buildingFilter || undefined,
    }, { preserveState: true, replace: true });
  };

  const resetFilters = () => {
    setSearch('');
    setStatusFilter('');
    setBuildingFilter('');
    router.get(generation.url(), {}, { replace: true });
  };

  const handleDelete = (flock: Flock) => {
    if (!confirm(`Supprimer définitivement le lot "${flock.name}" ?`)) return;
    router.delete(flocksDestroy.url(flock.id), {
      
    });
  };

  const handleSubmit = (flock: Flock) => {
    router.patch(flocksSubmit.url(flock.id), {}, {
      
    });
  };

  const handleApprove = (flock: Flock) => {
    
  };

  const handleReject = (flock: Flock) => {
    const reason = prompt('Motif du rejet :');
    if (reason === null) return;
    router.patch(flocksReject.url(flock.id), {
    });
  };

  const handleEnd = (flock: Flock) => {
    if (!confirm(`Terminer le lot "${flock.name}" ? Cette action est irréversible.`)) return;
    router.post(flocksEnd.url(flock.id), {}, {
    });
  };

  return (
    <AppLayout>
      <Head title="Gestion des lots" />

      <div className="min-h-screen bg-stone-50">
        {/* Header */}
        <div className="bg-white border-b border-stone-200 px-6 py-5 sm:px-8">
          <div className="max-w-7xl mx-auto flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
              <h1 className="text-2xl font-semibold text-stone-900">Gestion des lots</h1>
              <p className="text-stone-500 text-sm mt-0.5">
                {flocks.total} lot{flocks.total !== 1 ? 's' : ''} au total
              </p>
            </div>
            <button
              type="button"
              onClick={() => router.get(flocksStore.url())}
              className="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium rounded-lg transition-colors shadow-sm"
            >
              <Plus className="w-4 h-4" />
              Nouveau lot
            </button>
          </div>
        </div>

        <div className="max-w-7xl mx-auto px-6 sm:px-8 py-6 space-y-6">
          {/* Filtres */}
          <div className="bg-white border border-stone-200 rounded-xl p-4 flex flex-wrap gap-3 items-end">
            <div className="flex-1 min-w-[200px]">
              <label className="block text-xs font-medium text-stone-500 mb-1.5">Recherche</label>
              <div className="relative">
                <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-stone-400" />
                <input
                  type="text"
                  value={search}
                  onChange={(e) => setSearch(e.target.value)}
                  onKeyDown={(e) => e.key === 'Enter' && applyFilters()}
                  placeholder="Nom du lot..."
                  className="w-full pl-9 pr-4 py-2 border border-stone-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 bg-white"
                />
              </div>
            </div>

            <div className="min-w-[150px]">
              <label className="block text-xs font-medium text-stone-500 mb-1.5">Statut</label>
              <select
                value={statusFilter}
                onChange={(e) => setStatusFilter(e.target.value)}
                className="w-full px-3 py-2 border border-stone-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 bg-white"
              >
                <option value="">Tous</option>
                {Object.entries(STATUS_META).map(([key, { label }]) => (
                  <option key={key} value={key}>{label}</option>
                ))}
              </select>
            </div>

            <div className="min-w-[180px]">
              <label className="block text-xs font-medium text-stone-500 mb-1.5">Bâtiment</label>
              <select
                value={buildingFilter}
                onChange={(e) => setBuildingFilter(e.target.value)}
                className="w-full px-3 py-2 border border-stone-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 bg-white"
              >
                <option value="">Tous</option>
                {buildings.map(b => (
                  <option key={b.id} value={b.id}>{b.name}</option>
                ))}
              </select>
            </div>

            <div className="flex gap-2">
              <button
                type="button"
                onClick={applyFilters}
                className="px-4 py-2 bg-stone-900 text-white text-sm rounded-lg hover:bg-stone-800 transition-colors flex items-center gap-1.5"
              >
                <Filter className="w-4 h-4" />
                Filtrer
              </button>
              {(search || statusFilter || buildingFilter) && (
                <button
                  type="button"
                  onClick={resetFilters}
                  className="px-4 py-2 border border-stone-200 text-stone-600 text-sm rounded-lg hover:bg-stone-50 transition-colors"
                >
                  Réinitialiser
                </button>
              )}
            </div>
          </div>

          {/* Tableau */}
          <div className="bg-white border border-stone-200 rounded-xl overflow-hidden shadow-sm">
            <div className="overflow-x-auto">
              <table className="w-full text-sm">
                <thead className="bg-stone-50 border-b border-stone-200">
                  <tr>
                    <th className="px-5 py-3.5 text-left text-xs font-semibold text-stone-500 uppercase tracking-wider">Lot</th>
                    <th className="px-5 py-3.5 text-left text-xs font-semibold text-stone-500 uppercase tracking-wider">Bâtiment</th>
                    <th className="px-5 py-3.5 text-left text-xs font-semibold text-stone-500 uppercase tracking-wider">Arrivée</th>
                    <th className="px-5 py-3.5 text-left text-xs font-semibold text-stone-500 uppercase tracking-wider">Effectif</th>
                    <th className="px-5 py-3.5 text-left text-xs font-semibold text-stone-500 uppercase tracking-wider">Statut</th>
                    <th className="px-5 py-3.5 text-right text-xs font-semibold text-stone-500 uppercase tracking-wider">Actions</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-stone-100">
                  {flocks.data.length === 0 ? (
                    <tr>
                      <td colSpan={6} className="px-5 py-12 text-center text-stone-400">
                        Aucun lot trouvé.
                      </td>
                    </tr>
                  ) : (
                    flocks.data.map(flock => (
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
              <div className="border-t border-stone-100 px-5 py-4 flex items-center justify-between">
                <span className="text-sm text-stone-500">
                  Page {flocks.current_page} sur {flocks.last_page} · {flocks.total} résultats
                </span>
                <div className="flex gap-1">
                  {(() => {
                    const prevLink = flocks.links.find(link => link.label.includes('Précédent') || link.label === '&laquo; Previous');
                    const nextLink = flocks.links.find(link => link.label.includes('Suivant') || link.label === 'Next &raquo;');
                    return (
                      <>
                        <button
                          type="button"
                          disabled={!prevLink?.url || flocks.current_page === 1}
                          onClick={() => prevLink?.url && router.get(prevLink.url)}
                          className="p-1.5 rounded hover:bg-stone-100 disabled:opacity-30 disabled:cursor-not-allowed"
                        >
                          <ChevronLeft className="w-4 h-4" />
                        </button>
                        <button
                          type="button"
                          disabled={!nextLink?.url || flocks.current_page === flocks.last_page}
                          onClick={() => nextLink?.url && router.get(nextLink.url)}
                          className="p-1.5 rounded hover:bg-stone-100 disabled:opacity-30 disabled:cursor-not-allowed"
                        >
                          <ChevronRight className="w-4 h-4" />
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
function FlockRow({ flock, onDelete, onSubmit, onApprove, onReject, onEnd }: {
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
    <tr className="hover:bg-stone-50/80 transition-colors">
      <td className="px-5 py-4">
        <div className="flex items-center gap-2">
          <span className="font-medium text-stone-900">{flock.name}</span>
          {SPECIES_BADGE[flock.animal_type_code] && (
            <span className="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-stone-100 text-stone-600">
              {SPECIES_BADGE[flock.animal_type_code]}
            </span>
          )}
        </div>
      </td>
      <td className="px-5 py-4">
        <span className="flex items-center gap-1.5 text-stone-600">
          <MapPin className="w-3.5 h-3.5 text-stone-400" />
          {flock.building}
        </span>
      </td>
      <td className="px-5 py-4">
        <span className="flex items-center gap-1.5 text-stone-600">
          <Calendar className="w-3.5 h-3.5 text-stone-400" />
          {flock.arrival_date_formatted}
        </span>
      </td>
      <td className="px-5 py-4">
        <div className="flex flex-col">
          <span className="text-stone-900 font-medium">{flock.current_quantity.toLocaleString()}</span>
          <span className="text-xs text-stone-400">/ {flock.initial_quantity.toLocaleString()}</span>
          <div className="w-24 h-1.5 bg-stone-100 rounded-full mt-1 overflow-hidden">
            <div
              className={`h-full rounded-full ${isLowStock ? 'bg-amber-500' : 'bg-emerald-500'}`}
              style={{ width: `${Math.min(progress, 100)}%` }}
            />
          </div>
        </div>
      </td>
      <td className="px-5 py-4">
        <span className={`inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border ${statusMeta.classes}`}>
          {statusMeta.label}
        </span>
      </td>
      <td className="px-5 py-4 text-right">
        <div className="relative flex items-center justify-end gap-1">
          <button
            type="button"
            onClick={() => router.get(flocksShow.url(flock.id))}
            className="p-1.5 rounded-lg text-stone-500 hover:text-indigo-600 hover:bg-indigo-50 transition-colors"
            title="Voir détails"
          >
            <Eye className="w-4 h-4" />
          </button>
          
          {flock.status === 'active' && (
            <button
              type="button"
              onClick={() => {}}
              className="p-1.5 rounded-lg text-stone-500 hover:text-emerald-600 hover:bg-emerald-50 transition-colors"
              title="Nouveau suivi"
            >
              <ClipboardList className="w-4 h-4" />
            </button>
          )}

          {flock.can_approve && (
            <button
              type="button"
              onClick={() => onApprove(flock)}
              className="p-1.5 rounded-lg text-stone-500 hover:text-emerald-600 hover:bg-emerald-50 transition-colors"
              title="Approuver"
            >
              <CheckCircle className="w-4 h-4" />
            </button>
          )} 

          {flock.can_submit && (
            <button
              type="button"
              onClick={() => onSubmit(flock)}
              className="p-1.5 rounded-lg text-stone-500 hover:text-indigo-600 hover:bg-indigo-50 transition-colors"
              title="Soumettre"
            >
              <Send className="w-4 h-4" />
            </button>
          )}

          {/* Menu déroulant */}
          <div className="relative">
            <button
              type="button"
              onClick={() => setShowActions(!showActions)}
              className="p-1.5 rounded-lg text-stone-500 hover:text-stone-700 hover:bg-stone-100 transition-colors"
              title="Plus d'actions"
            >
              <MoreHorizontal className="w-4 h-4" />
            </button>

            {showActions && (
              <>
                <div className="fixed inset-0 z-10" onClick={() => setShowActions(false)} />
                <div className="absolute right-0 top-full mt-1 w-48 bg-white rounded-lg shadow-lg border border-stone-200 py-1 z-20">
                  {flock.can_edit && (
                    <button
                      type="button"
                      onClick={() => {
                        setShowActions(false);
                        router.get(flocksEdit.url(flock.id));
                      }}
                      className="w-full px-4 py-2 text-left text-sm text-stone-700 hover:bg-stone-50 flex items-center gap-2"
                    >
                      <Edit2 className="w-4 h-4" /> Modifier
                    </button>
                  )}
                  {flock.status === 'active' && flock.can_end && (
                    <button
                      type="button"
                      onClick={() => {
                        setShowActions(false);
                        onEnd(flock);
                      }}
                      className="w-full px-4 py-2 text-left text-sm text-amber-700 hover:bg-amber-50 flex items-center gap-2"
                    >
                      <Archive className="w-4 h-4" /> Terminer
                    </button>
                  )}
                  {flock.can_reject && (
                    <button
                      type="button"
                      onClick={() => {
                        setShowActions(false);
                        onReject(flock);
                      }}
                      className="w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50 flex items-center gap-2"
                    >
                      <XCircle className="w-4 h-4" /> Rejeter
                    </button>
                  )}
                  {flock.can_delete && (
                    <button
                      type="button"
                      onClick={() => {
                        setShowActions(false);
                        onDelete(flock);
                      }}
                      className="w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50 flex items-center gap-2"
                    >
                      <Trash2 className="w-4 h-4" /> Supprimer
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