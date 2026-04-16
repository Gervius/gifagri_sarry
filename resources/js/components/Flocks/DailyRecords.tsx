import { useForm } from '@inertiajs/react';
import { Plus, X, Save, AlertCircle } from 'lucide-react';
import React, { useState } from 'react';
import { dailyRecordsStore } from '@/routes';

interface Entity {
    id: number;
    name: string;
}

interface Flock {
    id: number;
    name: string;
    animal_type_code: string;
}

interface DailyRecord {
    id: number;
    date: string;
    losses: number;
    eggs?: number;
    feed_consumed: number;
    water_consumed: number;
    status: 'pending' | 'approved' | 'rejected';
}

interface Props {
    flock: Flock;
    records: DailyRecord[];
    recipes: Entity[];
    batches: Entity[];
}

export default function DailyRecords({
    flock,
    records = [],
    recipes = [],
    batches = [],
}: Props) {
    const [showForm, setShowForm] = useState(false);
    const hasEggs = !['pig', 'broiler'].includes(flock.animal_type_code);

    const today = new Date().toISOString().split('T')[0];

    const { data, setData, post, processing, errors, reset } = useForm({
        flock_id: flock.id,
        date: today,
        losses: 0,
        eggs: hasEggs ? 0 : undefined,
        feed_consumed: 0,
        feed_type_id: '',
        water_consumed: 0,
        feed_batch_id: '',
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(dailyRecordsStore(), {
            onSuccess: () => {
                reset();
                setShowForm(false);
            },
        });
    };

    return (
        <div className="space-y-6">
            <div className="flex items-center justify-between">
                <h3 className="text-lg font-bold text-stone-900">
                    Saisie Journalière
                </h3>
                <button
                    onClick={() => setShowForm(!showForm)}
                    className="flex items-center gap-2 rounded-xl bg-stone-900 px-4 py-2 font-medium text-white shadow-sm transition-colors hover:bg-stone-800"
                >
                    {showForm ? (
                        <X className="h-4 w-4" />
                    ) : (
                        <Plus className="h-4 w-4" />
                    )}
                    {showForm ? 'Annuler' : 'Nouvelle saisie'}
                </button>
            </div>

            {showForm && (
                <div className="animate-in rounded-2xl border border-stone-200 bg-stone-50 p-6 shadow-sm duration-200 fade-in slide-in-from-top-4">
                    <form onSubmit={handleSubmit} className="space-y-6">
                        <div className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                            <div className="flex flex-col gap-2">
                                <label
                                    htmlFor="date"
                                    className="text-sm font-medium text-stone-700"
                                >
                                    Date <span className="text-red-500">*</span>
                                </label>
                                <input
                                    type="date"
                                    id="date"
                                    value={data.date}
                                    max={today}
                                    onChange={(e) =>
                                        setData('date', e.target.value)
                                    }
                                    required
                                    className="h-12 w-full rounded-xl border border-stone-200 bg-white px-4 text-stone-900 transition-shadow outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500"
                                />
                                {errors.date && (
                                    <p className="flex items-center gap-1 text-sm text-red-600">
                                        <AlertCircle className="h-4 w-4" />{' '}
                                        {errors.date}
                                    </p>
                                )}
                            </div>

                            <div className="flex flex-col gap-2">
                                <label
                                    htmlFor="losses"
                                    className="text-sm font-medium text-stone-700"
                                >
                                    Pertes (mortalité){' '}
                                    <span className="text-red-500">*</span>
                                </label>
                                <input
                                    type="number"
                                    id="losses"
                                    value={data.losses}
                                    min="0"
                                    onChange={(e) =>
                                        setData(
                                            'losses',
                                            Number(e.target.value),
                                        )
                                    }
                                    required
                                    className="h-12 w-full rounded-xl border border-stone-200 bg-white px-4 text-stone-900 transition-shadow outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500"
                                />
                                {errors.losses && (
                                    <p className="flex items-center gap-1 text-sm text-red-600">
                                        <AlertCircle className="h-4 w-4" />{' '}
                                        {errors.losses}
                                    </p>
                                )}
                            </div>

                            {hasEggs && (
                                <div className="flex flex-col gap-2">
                                    <label
                                        htmlFor="eggs"
                                        className="text-sm font-medium text-stone-700"
                                    >
                                        Œufs collectés{' '}
                                        <span className="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="number"
                                        id="eggs"
                                        value={data.eggs || 0}
                                        min="0"
                                        onChange={(e) =>
                                            setData(
                                                'eggs',
                                                Number(e.target.value),
                                            )
                                        }
                                        required
                                        className="h-12 w-full rounded-xl border border-stone-200 bg-white px-4 text-stone-900 transition-shadow outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500"
                                    />
                                    {errors.eggs && (
                                        <p className="flex items-center gap-1 text-sm text-red-600">
                                            <AlertCircle className="h-4 w-4" />{' '}
                                            {errors.eggs}
                                        </p>
                                    )}
                                </div>
                            )}

                            <div className="flex flex-col gap-2">
                                <label
                                    htmlFor="water_consumed"
                                    className="text-sm font-medium text-stone-700"
                                >
                                    Eau consommée (L){' '}
                                    <span className="text-red-500">*</span>
                                </label>
                                <input
                                    type="number"
                                    id="water_consumed"
                                    value={data.water_consumed}
                                    min="0"
                                    onChange={(e) =>
                                        setData(
                                            'water_consumed',
                                            Number(e.target.value),
                                        )
                                    }
                                    required
                                    className="h-12 w-full rounded-xl border border-stone-200 bg-white px-4 text-stone-900 transition-shadow outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500"
                                />
                                {errors.water_consumed && (
                                    <p className="flex items-center gap-1 text-sm text-red-600">
                                        <AlertCircle className="h-4 w-4" />{' '}
                                        {errors.water_consumed}
                                    </p>
                                )}
                            </div>

                            <div className="flex flex-col gap-2">
                                <label
                                    htmlFor="feed_consumed"
                                    className="text-sm font-medium text-stone-700"
                                >
                                    Aliment consommé (kg){' '}
                                    <span className="text-red-500">*</span>
                                </label>
                                <input
                                    type="number"
                                    id="feed_consumed"
                                    value={data.feed_consumed}
                                    min="0"
                                    onChange={(e) => {
                                        const val = Number(e.target.value);
                                        setData('feed_consumed', val);

                                        if (val === 0) {
                                            setData((prevData) => ({
                                                ...prevData,
                                                feed_type_id: '',
                                                feed_batch_id: '',
                                            }));
                                        }
                                    }}
                                    required
                                    className="h-12 w-full rounded-xl border border-stone-200 bg-white px-4 text-stone-900 transition-shadow outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500"
                                />
                                {errors.feed_consumed && (
                                    <p className="flex items-center gap-1 text-sm text-red-600">
                                        <AlertCircle className="h-4 w-4" />{' '}
                                        {errors.feed_consumed}
                                    </p>
                                )}
                            </div>

                            <div className="flex flex-col gap-2">
                                <label
                                    htmlFor="feed_type_id"
                                    className={`text-sm font-medium ${data.feed_consumed > 0 ? 'text-stone-700' : 'text-stone-400'}`}
                                >
                                    Type d'aliment{' '}
                                    {data.feed_consumed > 0 && (
                                        <span className="text-red-500">*</span>
                                    )}
                                </label>
                                <select
                                    id="feed_type_id"
                                    value={data.feed_type_id}
                                    onChange={(e) =>
                                        setData('feed_type_id', e.target.value)
                                    }
                                    disabled={data.feed_consumed === 0}
                                    required={data.feed_consumed > 0}
                                    className="h-12 w-full appearance-none rounded-xl border border-stone-200 bg-white px-4 text-stone-900 transition-shadow outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500 disabled:cursor-not-allowed disabled:bg-stone-100"
                                >
                                    <option value="" disabled>
                                        Sélectionner
                                    </option>
                                    {recipes.map((r) => (
                                        <option key={r.id} value={r.id}>
                                            {r.name}
                                        </option>
                                    ))}
                                </select>
                                {errors.feed_type_id && (
                                    <p className="flex items-center gap-1 text-sm text-red-600">
                                        <AlertCircle className="h-4 w-4" />{' '}
                                        {errors.feed_type_id}
                                    </p>
                                )}
                            </div>

                            <div className="flex flex-col gap-2">
                                <label
                                    htmlFor="feed_batch_id"
                                    className={`text-sm font-medium ${data.feed_consumed > 0 ? 'text-stone-700' : 'text-stone-400'}`}
                                >
                                    Lot d'aliment
                                </label>
                                <select
                                    id="feed_batch_id"
                                    value={data.feed_batch_id}
                                    onChange={(e) =>
                                        setData('feed_batch_id', e.target.value)
                                    }
                                    disabled={data.feed_consumed === 0}
                                    className="h-12 w-full appearance-none rounded-xl border border-stone-200 bg-white px-4 text-stone-900 transition-shadow outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500 disabled:cursor-not-allowed disabled:bg-stone-100"
                                >
                                    <option value="">(Optionnel)</option>
                                    {batches.map((b) => (
                                        <option key={b.id} value={b.id}>
                                            {b.name}
                                        </option>
                                    ))}
                                </select>
                                {errors.feed_batch_id && (
                                    <p className="flex items-center gap-1 text-sm text-red-600">
                                        <AlertCircle className="h-4 w-4" />{' '}
                                        {errors.feed_batch_id}
                                    </p>
                                )}
                            </div>
                        </div>

                        <div className="mt-4 flex justify-end border-t border-stone-200 pt-4">
                            <button
                                type="submit"
                                disabled={processing}
                                className="flex items-center gap-2 rounded-xl bg-amber-500 px-6 py-2.5 font-medium text-white shadow-sm shadow-amber-500/20 transition-colors hover:bg-amber-600 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <Save className="h-4 w-4" /> Enregistrer la
                                saisie
                            </button>
                        </div>
                    </form>
                </div>
            )}

            <div className="mt-6 overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
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
                                    Pertes
                                </th>
                                <th
                                    scope="col"
                                    className="px-6 py-4 font-medium"
                                >
                                    Aliment (kg)
                                </th>
                                <th
                                    scope="col"
                                    className="px-6 py-4 font-medium"
                                >
                                    Eau (L)
                                </th>
                                {hasEggs && (
                                    <th
                                        scope="col"
                                        className="px-6 py-4 font-medium"
                                    >
                                        Œufs
                                    </th>
                                )}
                                <th
                                    scope="col"
                                    className="px-6 py-4 font-medium"
                                >
                                    Statut
                                </th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-stone-100 text-stone-700">
                            {records.length > 0 ? (
                                records.map((record) => (
                                    <tr
                                        key={record.id}
                                        className="transition-colors hover:bg-stone-50/50"
                                    >
                                        <td className="px-6 py-4 font-medium whitespace-nowrap text-stone-900">
                                            {record.date}
                                        </td>
                                        <td className="px-6 py-4">
                                            {record.losses}
                                        </td>
                                        <td className="px-6 py-4">
                                            {record.feed_consumed}
                                        </td>
                                        <td className="px-6 py-4">
                                            {record.water_consumed}
                                        </td>
                                        {hasEggs && (
                                            <td className="px-6 py-4">
                                                {record.eggs}
                                            </td>
                                        )}
                                        <td className="px-6 py-4">
                                            <span
                                                className={`inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ${
                                                    record.status === 'approved'
                                                        ? 'border border-emerald-200 bg-emerald-50 text-emerald-700'
                                                        : record.status ===
                                                            'rejected'
                                                          ? 'border border-red-200 bg-red-50 text-red-700'
                                                          : 'border border-amber-200 bg-amber-50 text-amber-700'
                                                }`}
                                            >
                                                {record.status === 'approved'
                                                    ? 'Validé'
                                                    : record.status ===
                                                        'rejected'
                                                      ? 'Rejeté'
                                                      : 'En attente'}
                                            </span>
                                        </td>
                                    </tr>
                                ))
                            ) : (
                                <tr>
                                    <td
                                        colSpan={hasEggs ? 6 : 5}
                                        className="px-6 py-8 text-center text-stone-500"
                                    >
                                        Aucun suivi enregistré pour l'instant.
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    );
}
