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
    BriefcaseMedical,
} from 'lucide-react';
import React, { useState, useEffect } from 'react';
import { treatmentsStore } from '@/routes';

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
    const [medicineOrigin, setMedicineOrigin] = useState<'farm' | 'vet'>(
        'farm',
    );

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
            // eslint-disable-next-line react-hooks/set-state-in-effect
            setMedicineOrigin('farm');
        }
    }, [isOpen, reset, clearErrors]);

    if (!isOpen || !scheduledTreatment) {
        return null;
    }

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();

        const submitData = {
            ...data,
            cost: data.cost ? Number(data.cost) : null,
            batch_id:
                medicineOrigin === 'farm' && data.batch_id
                    ? Number(data.batch_id)
                    : null,
            scheduled_treatment_id: scheduledTreatment.id,
        };

        post(treatmentsStore(), {
            ...submitData,
            onSuccess: () => {
                onClose();
            },
        });
    };

    return (
        <div className="fixed inset-0 z-50 flex animate-in items-center justify-center bg-stone-900/50 p-4 backdrop-blur-sm duration-200 fade-in">
            <div className="w-full max-w-2xl animate-in overflow-hidden rounded-2xl bg-white shadow-xl duration-200 zoom-in-95">
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

                        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <label
                                className={`flex cursor-pointer flex-col items-center justify-center gap-3 rounded-2xl border p-4 text-center transition-all ${
                                    medicineOrigin === 'farm'
                                        ? 'border-amber-500 bg-amber-50 shadow-sm shadow-amber-500/10'
                                        : 'border-stone-200 bg-white text-stone-500 hover:bg-stone-50'
                                }`}
                            >
                                <input
                                    type="radio"
                                    name="medicineOrigin"
                                    value="farm"
                                    checked={medicineOrigin === 'farm'}
                                    onChange={(e) =>
                                        setMedicineOrigin(
                                            e.target.value as 'farm',
                                        )
                                    }
                                    className="sr-only"
                                />
                                <Package
                                    className={`h-8 w-8 ${medicineOrigin === 'farm' ? 'text-amber-500' : 'text-stone-400'}`}
                                />
                                <div>
                                    <h4
                                        className={`font-semibold ${medicineOrigin === 'farm' ? 'text-amber-700' : 'text-stone-700'}`}
                                    >
                                        Stock de la ferme
                                    </h4>
                                    <p className="mt-1 text-xs">
                                        Utiliser un lot de médicament existant
                                    </p>
                                </div>
                            </label>

                            <label
                                className={`flex cursor-pointer flex-col items-center justify-center gap-3 rounded-2xl border p-4 text-center transition-all ${
                                    medicineOrigin === 'vet'
                                        ? 'border-amber-500 bg-amber-50 shadow-sm shadow-amber-500/10'
                                        : 'border-stone-200 bg-white text-stone-500 hover:bg-stone-50'
                                }`}
                            >
                                <input
                                    type="radio"
                                    name="medicineOrigin"
                                    value="vet"
                                    checked={medicineOrigin === 'vet'}
                                    onChange={(e) =>
                                        setMedicineOrigin(
                                            e.target.value as 'vet',
                                        )
                                    }
                                    className="sr-only"
                                />
                                <BriefcaseMedical
                                    className={`h-8 w-8 ${medicineOrigin === 'vet' ? 'text-amber-500' : 'text-stone-400'}`}
                                />
                                <div>
                                    <h4
                                        className={`font-semibold ${medicineOrigin === 'vet' ? 'text-amber-700' : 'text-stone-700'}`}
                                    >
                                        Fourni par le vétérinaire
                                    </h4>
                                    <p className="mt-1 text-xs">
                                        Saisir les informations d'intervention
                                    </p>
                                </div>
                            </label>
                        </div>

                        <div className="grid grid-cols-1 gap-6 sm:grid-cols-2">
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

                            {medicineOrigin === 'farm' && (
                                <div className="flex flex-col gap-2">
                                    <label
                                        htmlFor="batch_id"
                                        className="text-sm font-medium text-stone-700"
                                    >
                                        Lot de médicament{' '}
                                        <span className="text-red-500">*</span>
                                    </label>
                                    <div className="relative">
                                        <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                            <Package className="h-4 w-4 text-stone-400" />
                                        </div>
                                        <select
                                            id="batch_id"
                                            value={data.batch_id}
                                            onChange={(e) =>
                                                setData(
                                                    'batch_id',
                                                    e.target.value,
                                                )
                                            }
                                            required
                                            className="h-12 w-full appearance-none rounded-xl border border-stone-200 bg-white pr-4 pl-11 text-stone-900 transition-shadow outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500"
                                        >
                                            <option value="" disabled>
                                                Sélectionner un produit...
                                            </option>
                                            {batches.map((batch) => (
                                                <option
                                                    key={batch.id}
                                                    value={batch.id}
                                                >
                                                    {batch.name} (
                                                    {batch.current_quantity}{' '}
                                                    unités)
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
                            )}

                            <div
                                className={`flex flex-col gap-2 ${medicineOrigin === 'vet' ? '' : ''}`}
                            >
                                <label
                                    htmlFor="veterinarian"
                                    className={`text-sm font-medium ${medicineOrigin === 'vet' ? 'font-bold text-amber-700' : 'text-stone-700'}`}
                                >
                                    Vétérinaire{' '}
                                    {medicineOrigin === 'vet' && (
                                        <span className="text-red-500">*</span>
                                    )}
                                </label>
                                <div className="relative">
                                    <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                        <Stethoscope
                                            className={`h-4 w-4 ${medicineOrigin === 'vet' ? 'text-amber-500' : 'text-stone-400'}`}
                                        />
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
                                        required={medicineOrigin === 'vet'}
                                        className={`h-12 w-full rounded-xl border bg-white pr-4 pl-11 text-stone-900 transition-shadow outline-none focus:ring-2 ${medicineOrigin === 'vet' ? 'border-amber-300 shadow-sm shadow-amber-500/10 focus:border-amber-500 focus:ring-amber-500' : 'border-stone-200 focus:border-amber-500 focus:ring-amber-500'}`}
                                        placeholder="Nom du médecin..."
                                    />
                                </div>
                                {errors.veterinarian && (
                                    <p className="text-sm text-red-600">
                                        {errors.veterinarian}
                                    </p>
                                )}
                            </div>

                            <div
                                className={`flex flex-col gap-2 ${medicineOrigin === 'vet' ? 'sm:col-span-2' : ''}`}
                            >
                                <label
                                    htmlFor="cost"
                                    className={`text-sm font-medium ${medicineOrigin === 'vet' ? 'font-bold text-amber-700' : 'text-stone-700'}`}
                                >
                                    Coût (Optionnel)
                                </label>
                                <div className="relative">
                                    <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                        <Banknote
                                            className={`h-4 w-4 ${medicineOrigin === 'vet' ? 'text-amber-500' : 'text-stone-400'}`}
                                        />
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
                                        className={`h-12 w-full rounded-xl border bg-white pr-4 pl-11 text-stone-900 transition-shadow outline-none focus:ring-2 ${medicineOrigin === 'vet' ? 'border-amber-300 shadow-sm shadow-amber-500/10 focus:border-amber-500 focus:ring-amber-500' : 'border-stone-200 focus:border-amber-500 focus:ring-amber-500'}`}
                                        placeholder="0.00"
                                    />
                                </div>
                                {errors.cost && (
                                    <p className="text-sm text-red-600">
                                        {errors.cost}
                                    </p>
                                )}
                            </div>

                            <div className="flex flex-col gap-2 sm:col-span-2">
                                <label
                                    htmlFor="description"
                                    className="text-sm font-medium text-stone-700"
                                >
                                    Description / Produit utilisé (Optionnel)
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
                                        placeholder={
                                            medicineOrigin === 'vet'
                                                ? 'Notez le nom du médicament fourni par le vétérinaire...'
                                                : "Ajoutez des détails sur l'exécution du soin..."
                                        }
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
                            className="rounded-xl bg-emerald-600 px-5 py-2.5 font-medium text-white shadow-sm transition-colors hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            Confirmer l'exécution
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
}
