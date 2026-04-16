import { useForm } from '@inertiajs/react';
import {
    X,
    Calendar,
    AlertCircle,
    Info,
    Stethoscope,
    Banknote,
    FileText,
    Package,
} from 'lucide-react';
import React, { useEffect } from 'react';
import { scheduledTreatmentsExecute } from '@/routes';

interface Batch {
    id: number;
    name: string;
    current_quantity: number;
}

interface ScheduledTreatment {
    id: number;
    flock_name: string;
    step_name: string;
    scheduled_date_formatted: string;
    status: 'pending' | 'missed';
    days_overdue?: number;
}

interface Props {
    isOpen: boolean;
    onClose: () => void;
    scheduledTreatment: ScheduledTreatment | null;
    batches: Batch[];
}

export default function ExecuteTreatmentModal({
    isOpen,
    onClose,
    scheduledTreatment,
    batches = [],
}: Props) {
    const today = new Date().toISOString().split('T')[0];

    const { data, setData, post, processing, errors, reset, clearErrors } =
        useForm({
            treatment_date: today,
            veterinarian: '',
            cost: '',
            batch_id: '',
            description: '',
        });

    useEffect(() => {
        if (isOpen) {
            reset();
            clearErrors();
        }
    }, [isOpen, reset, clearErrors]);

    if (!isOpen || !scheduledTreatment) {
return null;
}

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();

        // Ensure optional numeric strings are sent as null or numbers
        const submitData = {
            ...data,
            cost: data.cost ? Number(data.cost) : null,
            batch_id: data.batch_id ? Number(data.batch_id) : null,
        };

        // If the route expects the ID in the URL, pass it as a route param and the body separately
        post(scheduledTreatmentsExecute({ treatment: scheduledTreatment.id }), {
            ...submitData,
            onSuccess: () => {
                onClose();
            },
        });
    };

    return (
        <div className="fixed inset-0 z-50 flex animate-in items-center justify-center bg-stone-900/50 p-4 backdrop-blur-sm duration-200 fade-in">
            <div className="w-full max-w-2xl animate-in overflow-hidden rounded-2xl bg-white shadow-xl duration-200 zoom-in-95">
                {/* Header */}
                <div className="flex items-center justify-between border-b border-stone-200 bg-stone-50 px-6 py-4">
                    <h2 className="text-xl font-bold text-stone-900">
                        Exécuter : {scheduledTreatment.step_name}
                    </h2>
                    <button
                        onClick={onClose}
                        className="rounded-xl p-2 text-stone-400 transition-colors hover:bg-stone-200 hover:text-stone-600"
                    >
                        <X className="h-5 w-5" />
                    </button>
                </div>

                <form onSubmit={handleSubmit}>
                    <div className="space-y-6 p-6 md:p-8">
                        {/* Info Block */}
                        <div className="flex flex-col gap-4 rounded-xl border border-stone-200 bg-stone-100 p-4 sm:flex-row">
                            <div className="flex items-center gap-2 text-sm text-stone-700">
                                <Info className="h-4 w-4 text-stone-400" />
                                <span>
                                    Bande :{' '}
                                    <strong className="text-stone-900">
                                        {scheduledTreatment.flock_name}
                                    </strong>
                                </span>
                            </div>
                            <div className="flex items-center gap-2 text-sm text-stone-700">
                                <Calendar className="h-4 w-4 text-stone-400" />
                                <span>
                                    Date prévue :{' '}
                                    <strong className="text-stone-900">
                                        {
                                            scheduledTreatment.scheduled_date_formatted
                                        }
                                    </strong>
                                </span>
                            </div>
                            {scheduledTreatment.status === 'missed' && (
                                <span className="inline-flex items-center gap-1 rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-700">
                                    <AlertCircle className="h-3 w-3" />
                                    En retard{' '}
                                    {scheduledTreatment.days_overdue
                                        ? `(${scheduledTreatment.days_overdue}j)`
                                        : ''}
                                </span>
                            )}
                        </div>

                        {/* Form Grid */}
                        <div className="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            {/* Date */}
                            <div className="flex flex-col gap-2">
                                <label
                                    htmlFor="treatment_date"
                                    className="text-sm font-medium text-stone-700"
                                >
                                    Date d'exécution{' '}
                                    <span className="text-red-500">*</span>
                                </label>
                                <div className="relative">
                                    <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                        <Calendar className="h-4 w-4 text-stone-400" />
                                    </div>
                                    <input
                                        type="date"
                                        id="treatment_date"
                                        value={data.treatment_date}
                                        max={today}
                                        onChange={(e) =>
                                            setData(
                                                'treatment_date',
                                                e.target.value,
                                            )
                                        }
                                        required
                                        className="h-12 w-full rounded-xl border border-stone-200 bg-white pr-4 pl-11 text-stone-900 transition-shadow outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500"
                                    />
                                </div>
                                {errors.treatment_date && (
                                    <p className="text-sm text-red-600">
                                        {errors.treatment_date}
                                    </p>
                                )}
                            </div>

                            {/* Veterinarian */}
                            <div className="flex flex-col gap-2">
                                <label
                                    htmlFor="veterinarian"
                                    className="text-sm font-medium text-stone-700"
                                >
                                    Vétérinaire (Optionnel)
                                </label>
                                <div className="relative">
                                    <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                        <Stethoscope className="h-4 w-4 text-stone-400" />
                                    </div>
                                    <input
                                        type="text"
                                        id="veterinarian"
                                        value={data.veterinarian}
                                        onChange={(e) =>
                                            setData(
                                                'veterinarian',
                                                e.target.value,
                                            )
                                        }
                                        className="h-12 w-full rounded-xl border border-stone-200 bg-white pr-4 pl-11 text-stone-900 transition-shadow outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500"
                                        placeholder="Nom du médecin..."
                                    />
                                </div>
                                {errors.veterinarian && (
                                    <p className="text-sm text-red-600">
                                        {errors.veterinarian}
                                    </p>
                                )}
                            </div>

                            {/* Cost */}
                            <div className="flex flex-col gap-2">
                                <label
                                    htmlFor="cost"
                                    className="text-sm font-medium text-stone-700"
                                >
                                    Coût (Optionnel)
                                </label>
                                <div className="relative">
                                    <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                        <Banknote className="h-4 w-4 text-stone-400" />
                                    </div>
                                    <input
                                        type="number"
                                        id="cost"
                                        value={data.cost}
                                        min="0"
                                        step="0.01"
                                        onChange={(e) =>
                                            setData('cost', e.target.value)
                                        }
                                        className="h-12 w-full rounded-xl border border-stone-200 bg-white pr-4 pl-11 text-stone-900 transition-shadow outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500"
                                        placeholder="0.00"
                                    />
                                </div>
                                {errors.cost && (
                                    <p className="text-sm text-red-600">
                                        {errors.cost}
                                    </p>
                                )}
                            </div>

                            {/* Batch Selection */}
                            <div className="flex flex-col gap-2">
                                <label
                                    htmlFor="batch_id"
                                    className="text-sm font-medium text-stone-700"
                                >
                                    Lot de médicament (Optionnel)
                                </label>
                                <div className="relative">
                                    <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                        <Package className="h-4 w-4 text-stone-400" />
                                    </div>
                                    <select
                                        id="batch_id"
                                        value={data.batch_id}
                                        onChange={(e) =>
                                            setData('batch_id', e.target.value)
                                        }
                                        className="h-12 w-full appearance-none rounded-xl border border-stone-200 bg-white pr-4 pl-11 text-stone-900 transition-shadow outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500"
                                    >
                                        <option value="">
                                            Sélectionner un produit...
                                        </option>
                                        {batches.map((batch) => (
                                            <option
                                                key={batch.id}
                                                value={batch.id}
                                            >
                                                {batch.name} (
                                                {batch.current_quantity} unités)
                                            </option>
                                        ))}
                                    </select>
                                </div>
                                {errors.batch_id && (
                                    <p className="text-sm text-red-600">
                                        {errors.batch_id}
                                    </p>
                                )}
                            </div>

                            {/* Description */}
                            <div className="flex flex-col gap-2 sm:col-span-2">
                                <label
                                    htmlFor="description"
                                    className="text-sm font-medium text-stone-700"
                                >
                                    Description / Observations (Optionnel)
                                </label>
                                <div className="relative">
                                    <div className="pointer-events-none absolute top-3 left-0 pl-4">
                                        <FileText className="h-4 w-4 text-stone-400" />
                                    </div>
                                    <textarea
                                        id="description"
                                        value={data.description}
                                        onChange={(e) =>
                                            setData(
                                                'description',
                                                e.target.value,
                                            )
                                        }
                                        rows={3}
                                        className="w-full resize-none rounded-xl border border-stone-200 bg-white py-3 pr-4 pl-11 text-stone-900 transition-shadow outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500"
                                        placeholder="Ajoutez des détails sur l'exécution du soin..."
                                    />
                                </div>
                                {errors.description && (
                                    <p className="text-sm text-red-600">
                                        {errors.description}
                                    </p>
                                )}
                            </div>
                        </div>
                    </div>

                    {/* Footer */}
                    <div className="flex items-center justify-end gap-3 rounded-b-2xl border-t border-stone-200 bg-stone-50 px-6 py-5">
                        <button
                            type="button"
                            onClick={onClose}
                            className="rounded-xl px-5 py-2.5 font-medium text-stone-600 transition-colors hover:bg-stone-200 hover:text-stone-900"
                        >
                            Annuler
                        </button>
                        <button
                            type="submit"
                            disabled={processing}
                            className="rounded-xl bg-amber-500 px-5 py-2.5 font-medium text-white shadow-sm transition-colors hover:bg-amber-600 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            Confirmer l'exécution
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
}
