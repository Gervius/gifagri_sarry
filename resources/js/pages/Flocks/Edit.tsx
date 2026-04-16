import { useForm, Head, Link } from '@inertiajs/react';
import { ChevronLeft, Save, Layers } from 'lucide-react';
import React from 'react';
import { flocksIndex, flocksUpdate } from '@/routes';

interface Entity {
    id: number;
    name: string;
}

interface Flock {
    id: number;
    name: string | null;
    animal_type_id: number;
    building_id: number;
    arrival_date: string;
    initial_quantity: number;
}

interface PageProps {
    flock: Flock;
    buildings: { data: Entity[] };
    animalTypes: { data: Entity[] };
}

export default function Edit({ flock, buildings, animalTypes }: PageProps) {
    const { data, setData, put, processing, errors } = useForm({
        name: flock.name || '',
        animal_type_id: flock.animal_type_id || '',
        building_id: flock.building_id || '',
        arrival_date: flock.arrival_date
            ? flock.arrival_date.split('T')[0]
            : '', // ensure standard YYYY-MM-DD
        initial_quantity: flock.initial_quantity || 0,
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        put(flocksUpdate({ flock: flock.id }));
    };

    return (
        <>
            <Head
                title={`Configuration de la bande - ${flock.name || 'Sans nom'}`}
            />

            <div className="min-h-screen bg-stone-50 px-6 py-12">
                <div className="mx-auto max-w-4xl space-y-8">
                    {/* Header */}
                    <div className="flex flex-col gap-2">
                        <Link
                            href={flocksIndex()}
                            className="flex w-fit items-center text-sm font-medium text-stone-500 transition-colors hover:text-stone-800"
                        >
                            <ChevronLeft className="mr-1 h-4 w-4" />
                            Retour aux bandes
                        </Link>
                        <div className="flex items-center gap-3">
                            <div className="rounded-xl border border-stone-200 bg-white p-3 shadow-sm">
                                <Layers className="h-6 w-6 text-stone-700" />
                            </div>
                            <h1 className="text-3xl font-bold text-stone-900">
                                Configuration de la bande
                            </h1>
                        </div>
                    </div>

                    {/* Form Card */}
                    <div className="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
                        <form onSubmit={handleSubmit}>
                            <div className="p-6 md:p-8">
                                <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                                    {/* Name */}
                                    <div className="flex flex-col gap-2">
                                        <label
                                            htmlFor="name"
                                            className="text-sm font-medium text-stone-700"
                                        >
                                            Nom de la bande (optionnel)
                                        </label>
                                        <input
                                            type="text"
                                            id="name"
                                            value={data.name}
                                            onChange={(e) =>
                                                setData('name', e.target.value)
                                            }
                                            className="h-12 w-full rounded-xl border border-stone-200 bg-white px-4 text-stone-900 transition-shadow outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500"
                                            placeholder="Ex: Lot Pondeuses 2024"
                                        />
                                        {errors.name && (
                                            <p className="text-sm text-red-600">
                                                {errors.name}
                                            </p>
                                        )}
                                    </div>

                                    {/* Animal Type */}
                                    <div className="flex flex-col gap-2">
                                        <label
                                            htmlFor="animal_type_id"
                                            className="text-sm font-medium text-stone-700"
                                        >
                                            Type d'animal{' '}
                                            <span className="text-red-500">
                                                *
                                            </span>
                                        </label>
                                        <select
                                            id="animal_type_id"
                                            value={data.animal_type_id}
                                            onChange={(e) =>
                                                setData(
                                                    'animal_type_id',
                                                    Number(e.target.value),
                                                )
                                            }
                                            required
                                            className="h-12 w-full cursor-pointer appearance-none rounded-xl border border-stone-200 bg-white px-4 text-stone-900 transition-shadow outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500"
                                        >
                                            <option value="" disabled>
                                                Sélectionner un type
                                            </option>
                                            {animalTypes.data.map((type) => (
                                                <option
                                                    key={type.id}
                                                    value={type.id}
                                                >
                                                    {type.name}
                                                </option>
                                            ))}
                                        </select>
                                        {errors.animal_type_id && (
                                            <p className="text-sm text-red-600">
                                                {errors.animal_type_id}
                                            </p>
                                        )}
                                    </div>

                                    {/* Building */}
                                    <div className="flex flex-col gap-2">
                                        <label
                                            htmlFor="building_id"
                                            className="text-sm font-medium text-stone-700"
                                        >
                                            Bâtiment{' '}
                                            <span className="text-red-500">
                                                *
                                            </span>
                                        </label>
                                        <select
                                            id="building_id"
                                            value={data.building_id}
                                            onChange={(e) =>
                                                setData(
                                                    'building_id',
                                                    Number(e.target.value),
                                                )
                                            }
                                            required
                                            className="h-12 w-full cursor-pointer appearance-none rounded-xl border border-stone-200 bg-white px-4 text-stone-900 transition-shadow outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500"
                                        >
                                            <option value="" disabled>
                                                Sélectionner un bâtiment
                                            </option>
                                            {buildings.data.map((b) => (
                                                <option key={b.id} value={b.id}>
                                                    {b.name}
                                                </option>
                                            ))}
                                        </select>
                                        {errors.building_id && (
                                            <p className="text-sm text-red-600">
                                                {errors.building_id}
                                            </p>
                                        )}
                                    </div>

                                    {/* Arrival Date */}
                                    <div className="flex flex-col gap-2">
                                        <label
                                            htmlFor="arrival_date"
                                            className="text-sm font-medium text-stone-700"
                                        >
                                            Date d'arrivée{' '}
                                            <span className="text-red-500">
                                                *
                                            </span>
                                        </label>
                                        <input
                                            type="date"
                                            id="arrival_date"
                                            value={data.arrival_date}
                                            onChange={(e) =>
                                                setData(
                                                    'arrival_date',
                                                    e.target.value,
                                                )
                                            }
                                            required
                                            className="h-12 w-full rounded-xl border border-stone-200 bg-white px-4 text-stone-900 transition-shadow outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500"
                                        />
                                        {errors.arrival_date && (
                                            <p className="text-sm text-red-600">
                                                {errors.arrival_date}
                                            </p>
                                        )}
                                    </div>

                                    {/* Initial Quantity */}
                                    <div className="flex flex-col gap-2 md:col-span-2">
                                        <label
                                            htmlFor="initial_quantity"
                                            className="text-sm font-medium text-stone-700"
                                        >
                                            Quantité initiale{' '}
                                            <span className="text-red-500">
                                                *
                                            </span>
                                        </label>
                                        <input
                                            type="number"
                                            id="initial_quantity"
                                            value={data.initial_quantity}
                                            onChange={(e) =>
                                                setData(
                                                    'initial_quantity',
                                                    Number(e.target.value),
                                                )
                                            }
                                            required
                                            min="0"
                                            className="h-12 w-full rounded-xl border border-stone-200 bg-white px-4 text-stone-900 transition-shadow outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500"
                                            placeholder="Ex: 5000"
                                        />
                                        {errors.initial_quantity && (
                                            <p className="text-sm text-red-600">
                                                {errors.initial_quantity}
                                            </p>
                                        )}
                                    </div>
                                </div>
                            </div>

                            {/* Footer */}
                            <div className="flex items-center justify-end gap-4 rounded-b-2xl border-t border-stone-200 bg-stone-50 px-6 py-5 md:px-8">
                                <Link
                                    href={flocksIndex()}
                                    className="rounded-xl px-6 py-2.5 font-medium text-stone-600 transition-colors hover:bg-stone-200 hover:text-stone-900"
                                >
                                    Annuler
                                </Link>
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="flex items-center gap-2 rounded-xl bg-amber-500 px-6 py-2.5 font-medium text-white shadow-sm shadow-amber-500/20 transition-colors hover:bg-amber-600 disabled:cursor-not-allowed disabled:opacity-50"
                                >
                                    <Save className="h-5 w-5" />
                                    Enregistrer
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </>
    );
}
